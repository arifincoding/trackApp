<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

// validation
use App\Validations\ServiceValidation;
use App\Validations\WarrantyValidation;

// repository
use App\Repositories\ServiceRepository;
use App\Repositories\ResponbilityRepository;
use App\Repositories\ServiceTrackRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\BrokenRepository;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class ServiceController extends Controller{
    
    private $serviceRepository;
    private $serviceTrackRepository;
    private $responbilityRepository;
    private $customerRepository;
    private $brokenRepository;

    function __construct(ServiceRepository $service, ServiceTrackRepository $serviceTrack, ResponbilityRepository $responbility, CustomerRepository $customer, BrokenRepository $broken){
        $this->serviceRepository = $service;
        $this->serviceTrackRepository = $serviceTrack;
        $this->responbilityRepository = $responbility;
        $this->customerRepository = $customer;
        $this->brokenRepository = $broken;
    }

    function getListService(Request $request){
        $limit = $request->query('limit',0);
        $filter = $request->only('kategori','status','cari');
        $data = $this->serviceRepository->getListDataJoinCustomer($limit,$filter);
        return $this->jsonSuccess('sukses',200,$data['data']);
    }

    function getServiceById($id){
        $data = $this->serviceRepository->getDataJoinCustomerById($id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    function getServiceQueue(Request $request){
        $username = auth()->payload()->get('username');
        $filter = $request->only('limit','kategori','cari');
        $limit = $request->query('limit',0);
        $resp = $this->responbilityRepository->getListDataByUsername($username);
        if($resp){
            $data = $this->serviceRepository->getListDataQueue($resp, $limit, $filter);
            return $this->jsonSuccess('sukses',200,$data);
        }
        throw new ModelNotFoundException();
    }

    function getMyProgressService(Request $request){
        $username = auth()->payload()->get('username');
        $filter = $request->only('status','cari','kategori');
        $limit = $request->query('limit',0);
        $data = $this->serviceRepository->getListDataMyProgress($username,$limit,$filter);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function getServiceTrackByCode(string $id){
        $data = $this->serviceRepository->getDataByCode($id);
        if($data === []){
            return $this->jsonSuccess('data tidak ditemukan',200,[]);
        }
        $data['kerusakan'] = $this->brokenRepository->getAllByIdService($data['idService']);
        $data['riwayat'] = $this->serviceTrackRepository->getAllByIdService($data['idService']);
        return $this->jsonSuccess('sukses',200,$data);
    }

    function newService(Request $request, ServiceValidation $validator):JsonResponse
    {
        $input= $request->all();
        $validator->post();
        $validation = $validator->validate($input);
        
        $customerExist = $this->customerRepository->isCustomerExist($input);
        
        if($customerExist['exist'] === false){
            $dataCustomer = $this->customerRepository->create($input);
            $idCustomer = $dataCustomer['idCustomer'];
        }else{
            $dataCustomer = $this->customerRepository->updateCount($customerExist['idCustomer'],'plus');
            $idCustomer = $dataCustomer['idCustomer'];
        }
        
        $dataService = $this->serviceRepository->create($input,$idCustomer);
        $this->addServiceTrack('antri',$dataService['idService']);
        return $this->jsonSuccess('sukses',200,$dataService);
    }

    public function updateService(Request $request, $id, ServiceValidation $validator){
        $input = $request->all();
        $validator->post();
        $validation = $validator->validate($input);
        $dataService = $this->serviceRepository->getDataById($id);
        if($dataService){
            $dataCustomer = $this->customerRepository->update($input,$dataService['idCustomer']);
            $data = $this->serviceRepository->update($input,$dataCustomer['idCustomer'],$id);
            return $this->jsonSuccess('sukses',200,$data);
        }
    }

    public function updateServiceStatus(Request $request,$id, ServiceValidation $validator){
        $input = $request->only('status');
        $validator->statusService();
        $validator->validate($input);
        $data = $this->serviceRepository->updateDataStatus($input,$id);
        $this->addServiceTrack($input['status'],$id);
        $find = $this->serviceRepository->getDataById($id);
        if($input['status'] === 'selesai diagnosa' && boolval($find['butuhKonfirmasi'])===true){
            $this->addServiceTrack('tunggu',$id);
            $this->serviceRepository->updateDataStatus(['status'=>'tunggu'],$id);
        }
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function setServiceTake(string $id){
        $data = $this->serviceRepository->setDataTake($id);
        $this->addServiceTrack('diambil',$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function setServiceConfirmCost(string $id){
        $data = $this->serviceRepository->setDataConfirmCost($id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function updateServiceWarranty(Request $request, $id,ServiceValidation $validator){
        $input = $request->only('garansi');
        $validator->serviceWarranty();
        $validator->validate($input);
        $data = $this->serviceRepository->updateWarranty($input,$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function setServiceConfirmation(Request $request,string $id,ServiceValidation $validator){
        $input =  $request->only('dikonfirmasi');
        $validator->confirmation($input);
        $validator->validate($input);
        $data = $this->serviceRepository->setDataConfirmation($id,$input);
        if($input['dikonfirmasi'] === true){
            $this->addServiceTrack('setuju',$id);
        }else{
            $this->addServiceTrack('batal',$id);
        }
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function deleteService($id){
        $dataService = $this->serviceRepository->getDataById($id);
        if($dataService){
            $dataCustomer = $this->customerRepository->findDataById($dataService['idCustomer']);
            if($dataCustomer['jumlahService'] > 1){
                $this->customerRepository->updateCount($dataCustomer['id'],'minus');
            }else{
                $this->customerRepository->deleteById($dataService['idCustomer']);
            }
            $data = $this->serviceRepository->deleteById($id);
            if($data['sukses']===true){
                $this->serviceTrackRepository->deleteByIdService($id);
                $this->brokenRepository->deleteByIdService($id);
            }
            return $this->jsonMessageOnly('sukses hapus data service');
        }
    }

    private function addServiceTrack(string $status, string $id){
        $service = $this->serviceRepository->getDataById($id);
        $messages = $this->getTrackMessage($service['kategori']);
        $message = '';
        if($service['dikonfirmasi'] === 0 && $status === 'selesai'){
            $message = 'proses pembatalan selesai, '.$service['kategori'].' sudah bisa untuk diambil.';
        }else{
            foreach($messages as $key=>$item){
                if($status === $key){
                    $message = $item;
                    break;
                }
        }
        }
        $attributs = [
            'idService'=>$id,
            'judul'=>$message,
            'status'=>$status
        ];
        $this->serviceTrackRepository->create($attributs);
    }

    private function getTrackMessage(string $kategori){
        return [
            'antri'=> $kategori.' anda telah di terima oleh '.auth()->payload()->get('name').' dan sedang menunggu untuk di diagnosa',
            'mulai diagnosa'=> $kategori.' anda sedang dalam proses diagnosa',
            'selesai diagnosa'=> $kategori.' anda telah selesai di diagnosa',
            'tunggu'=> $kategori.' anda sedang menunggu persetujuan dari anda',
            'proses perbaikan'=> $kategori.' anda sedang dalam proses perbaikan',
            'proses pembatalan'=> $kategori.' anda sedang dalam proses pembatalan',
            'selesai'=> 'proses perbaikan selesai, '.$kategori.' anda sudah bisa untuk diambil',
            'diambil'=> $kategori.' anda telah diambil',
            'batal'=> 'anda mengajukan pembatalan proses perbaikan',
            'setuju'=> 'anda telah menyetujui proses perbaikan'
        ];
    }
}
?>