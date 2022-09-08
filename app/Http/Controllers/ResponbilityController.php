<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ResponbilityRepository;
use App\Repositories\UserRepository;
use App\Validations\ResponbilityValidation;
use Illuminate\Http\JsonResponse;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use App\Transformers\ResponbilitiesTransformer;
use App\Http\Controllers\Contracts\ResponbilityControllerContract;

class ResponbilityController extends Controller implements ResponbilityControllerContract
{

    private $responbilityRepository;
    private $userRepository;

    public function __construct(ResponbilityRepository $responbility, UserRepository $user)
    {
        $this->responbilityRepository = $responbility;
        $this->userRepository = $user;
    }

    function all(string $id): JsonResponse
    {
        $query = $this->responbilityRepository->getListDataByUsername($id);
        if ($query) {
            $fractal = new Manager();
            $data = $fractal->createData(new Collection($query, new ResponbilitiesTransformer))->toArray();
            return $this->jsonSuccess('sukses', 200, $data);
        }
        return $this->jsonSuccess('sukses', 200, []);
    }

    function create(Request $request, int $id, ResponbilityValidation $validator): JsonResponse
    {
        $input = $request->only(['idKategori']);
        $validator->post($id, $input);
        $validator->validate($input);
        $findUser = $this->userRepository->getDataById($id);
        if ($findUser['peran'] !== 'teknisi') {
            return $this->jsonValidationError('gagal tambah tanggung jawab karena pegawai ini bukan teknisi');
        }
        $this->responbilityRepository->create($input, $findUser['peran'], $findUser['username']);
        return $this->jsonMessageOnly('sukses tambah tanggung jawab');
    }

    public function delete(int $id): JsonResponse
    {
        $this->responbilityRepository->deleteDataById($id);
        return $this->jsonMessageOnly('sukses hapus data tanggung jawab');
    }
}