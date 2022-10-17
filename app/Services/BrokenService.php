<?php

namespace App\Services;

use App\Repositories\BrokenRepository;
use App\Validations\BrokenValidation;
use App\Repositories\ServiceRepository;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use App\Transformers\BrokensTransformer;
use App\Services\Contracts\BrokenServiceContract;
use Illuminate\Support\Facades\Log;

class BrokenService implements BrokenServiceContract
{

    private $brokenRepository;
    private $serviceRepository;
    private $brokenValidator;

    public function __construct(BrokenRepository $broken, ServiceRepository $service, BrokenValidation $validator)
    {
        $this->brokenRepository = $broken;
        $this->serviceRepository = $service;
        $this->brokenValidator = $validator;
    }

    public function getListBrokenByIdService(int $id): array
    {
        Log::info("User is trying to accessing all broken data by id service", ['id service' => $id]);
        $query = $this->brokenRepository->getListDataByIdService($id);
        Log::info("User is accessing all broken data by id Service");
        $fractal = new Manager();
        $data = $fractal->createData(new Collection($query, new BrokensTransformer))->toArray();
        return $data;
    }

    public function newBrokenByIdService(array $inputs, int $idService): array
    {
        Log::info("User is trying to create a single broken data by id service", ['id service' => $idService, 'data' => $inputs]);
        $this->brokenValidator->validate($inputs, 'create');
        $findService = $this->serviceRepository->findById($idService);
        Log::info("Service data found for creating broken data by id service", ['id service' => $findService->id]);
        $confirm = null;
        if ($findService->butuhPersetujuan === false) {
            $confirm = true;
        }
        $inputs += [
            'idService' => $idService,
            'disetujui' => $confirm,
        ];
        $data = $this->brokenRepository->save($inputs);
        Log::info("User create a single broken data by id service successfully", ["id broken" => $data->id]);
        return ['idKerusakan' => $data->id];
    }

    public function getBrokenById(int $id): array
    {
        Log::info("User trying to accessing a single broken data by id broken", ['id broken' => $id]);
        $data = $this->brokenRepository->getDataById($id);
        Log::info("User is accessing a single broken data", ["id broken" => $data->idKerusakan]);
        return $data->toArray();
    }

    public function updateBroken(array $inputs, int $id): array
    {
        Log::info("User is trying to update a single broken data by id broken", ["id broken" => $id, "data" => $inputs]);
        $this->brokenValidator->validate($inputs, 'update');
        $data = $this->brokenRepository->save($inputs, $id);
        Log::info("User update a single broken data by id broken successfully", ["id broken" => $data->id]);
        return [
            'idKerusakan' => $data->id,
            'idService' => $data->idService
        ];
    }

    public function updateBrokenCost(array $inputs, int $id): array
    {
        Log::info("trying to updating broken cost in the single broken data by id broken", ["id broken" => $id, "data" => $inputs]);
        $this->brokenValidator->cost();
        $this->brokenValidator->validate($inputs, "updateCost");
        $data = $this->brokenRepository->save($inputs, $id);
        Log::info("User update broken cost in the single broken data by id broken successfully", ["id broken" => $data->id]);
        return ['idKerusakan' => $data->id];
    }

    public function updateBrokenCofirmation(array $inputs, int $id): array
    {
        Log::info("trying to updating broken confirmation in the single broken data by id broken", ['id broken' => $id, 'data' => $inputs]);
        $this->brokenValidator->confirm();
        $this->brokenValidator->validate($inputs, 'updateConfirm');
        $data = $this->brokenRepository->save($inputs, $id);
        Log::info("User update broken confirmation in the single broken data by id broken successfully", ["id broken" => $data->id]);
        return ['idKerusakan' => $data->id];
    }

    public function deleteBrokenById(int $id): string
    {
        Log::info("trying to deleting a single broken data by id broken", ["id broken" => $id]);
        $this->brokenRepository->delete($id);
        Log::info("User delete a single broken data by id broken successfully", ['id broken' => $id]);
        return 'sukses hapus data kerusakan';
    }
}