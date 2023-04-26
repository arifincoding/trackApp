<?php

namespace App\Services;

use App\Services\Contracts\ResponbilityServiceContract;
use App\Repositories\ResponbilityRepository;
use App\Validations\ResponbilityValidation;
use App\Repositories\UserRepository;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use App\Transformers\ResponbilitiesTransformer;

class  ResponbilityService implements ResponbilityServiceContract
{
    private ResponbilityRepository $responbilityRepository;
    private UserRepository $userRepository;
    private ResponbilityValidation $validator;

    public function __construct(ResponbilityRepository $responbility, UserRepository $user, ResponbilityValidation $validator)
    {
        $this->responbilityRepository = $responbility;
        $this->userRepository = $user;
        $this->validator = $validator;
    }

    public function getAllRespobilities(string $username): array
    {
        $query = $this->responbilityRepository->getListDataByUsername($username);
        $fractal = new Manager();
        $data = $fractal->createData(new Collection($query, new ResponbilitiesTransformer))->toArray();
        return $data;
    }

    public function newResponbilities(array $inputs, int $idUser): array
    {
        $this->validator->post($idUser, $inputs);
        $this->validator->validate($inputs, 'create');
        $findUser = $this->userRepository->findById($idUser);
        $findUser->role !== 'teknisi' ? abort(400, 'gagal tambah tanggung jawab karena pegawai ini bukan teknisi') : null;
        $this->responbilityRepository->create($inputs, $findUser->username);
        return [
            'success' => true,
            'message' => 'sukses tambah tanggung jawab'
        ];
    }

    public function deleteResponbilityById(int $id): string
    {
        $this->responbilityRepository->delete($id);
        return 'sukses hapus data tanggung jawab';
    }
}
