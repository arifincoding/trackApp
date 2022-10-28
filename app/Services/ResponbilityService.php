<?php

namespace App\Services;

use App\Services\Contracts\ResponbilityServiceContract;
use App\Repositories\ResponbilityRepository;
use App\Validations\ResponbilityValidation;
use App\Repositories\userRepository;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use App\Transformers\ResponbilitiesTransformer;
use Illuminate\Support\Facades\Log;

class  ResponbilityService implements ResponbilityServiceContract
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
        Log::info("trying to access all tecnician responbility data by username", ["username" => $username]);
        $data = [];
        $query = $this->responbilityRepository->getListDataByUsername($username);
        if ($query) {
            $fractal = new Manager();
            $data = $fractal->createData(new Collection($query, new ResponbilitiesTransformer))->toArray();
            Log::info("user is accessing all tecnician responbility data by username");
        } else {
            Log::warning("tecnician responbility data by username not found", ["username" => $username]);
        }
        return $data;
    }

    public function newResponbilities(array $inputs, int $idUser): array
    {
        Log::info("User is trying to create responbilities data by id user", ['data' => $inputs]);
        $this->responbilityValidator->post($idUser, $inputs);
        $this->responbilityValidator->validate($inputs, 'create');
        $findUser = $this->userRepository->findById($idUser);
        Log::info("user data found for creating responbilities data by id user", ["id user" => $findUser->id]);
        if ($findUser->peran !== 'teknisi') {
            Log::warning("responbilities could not be created caused this user role is not tecnician", ["id user" => $findUser->id]);
            return [
                'success' => false,
                'message' => 'gagal tambah tanggung jawab karena pegawai ini bukan teknisi'
            ];
        }
        $this->responbilityRepository->create($inputs, $findUser->username);
        Log::info("User create responbilities data by id user successfully");
        return [
            'success' => true,
            'message' => 'sukses tambah tanggung jawab'
        ];
    }

    public function deleteResponbilityById(int $id): string
    {
        Log::info("trying to deleting a single responbility data by id responbility", ["id responbility" => $id]);
        $this->responbilityRepository->delete($id);
        Log::info("User delete a single responbility data by id responbility successfully", ['id responbility' => $id]);
        return 'sukses hapus data tanggung jawab';
    }
}