<?php

namespace App\Services;

use App\Services\Contracts\ResponbilityServiceContract;
use App\Repositories\ResponbilityRepository;
use App\Validations\ResponbilityValidation;
use App\Repositories\userRepository;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use App\Transformers\ResponbilitiesTransformer;

class ResponbilityService implements ResponbilityServiceContract
{
    private $responbilityRepository;
    private $userRepository;
    private $responbilityValidator;

    public function __construct(ResponbilityRepository $responbility, UserRepository $user, ResponbilityValidation $validator)
    {
        $this->responbilityRepository = $responbility;
        $this->userRepository = $user;
        $this->responbilityValidator = $validator;
    }

    public function getAllRespobilities(string $username): array
    {
        $data = [];
        $query = $this->responbilityRepository->getListDataByUsername($username);
        if ($query) {
            $fractal = new Manager();
            $data = $fractal->createData(new Collection($query, new ResponbilitiesTransformer))->toArray();
        }
        return $data;
    }

    public function newResponbilities(array $inputs, int $id): array
    {
        $this->responbilityValidator->post($id, $inputs);
        $this->responbilityValidator->validate($inputs);
        $findUser = $this->userRepository->getDataById($id);
        if ($findUser['peran'] !== 'teknisi') {
            return [
                'success' => false,
                'message' => 'gagal tambah tanggung jawab karena pegawai ini bukan teknisi'
            ];
        }
        $this->responbilityRepository->create($inputs, $findUser['peran'], $findUser['username']);
        return [
            'success' => true,
            'message' => 'sukses tambah tanggung jawab'
        ];
    }

    public function deleteResponbilityById(int $id): string
    {
        $this->responbilityRepository->deleteDataById($id);
        return 'sukses hapus data tanggung jawab';
    }
}