<?php

namespace App\Services;

use App\Repositories\BrokenRepository;
use App\Validations\BrokenValidation;
use App\Repositories\ServiceRepository;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use App\Transformers\BrokensTransformer;
use App\Services\Contracts\BrokenServiceContract;

class BrokenService implements BrokenServiceContract
{

    private BrokenRepository $brokenRepository;
    private ServiceRepository $serviceRepository;
    private BrokenValidation $validator;

    public function __construct(BrokenRepository $broken, ServiceRepository $service, BrokenValidation $validator)
    {
        $this->brokenRepository = $broken;
        $this->serviceRepository = $service;
        $this->validator = $validator;
    }

    public function getListBrokenByIdService(int $idService): array
    {
        $this->serviceRepository->findById($idService);
        $query = $this->brokenRepository->getListDataByIdService($idService);
        $fractal = new Manager();
        $data = $fractal->createData(new Collection($query, new BrokensTransformer))->toArray();
        return $data;
    }

    public function newBrokenByIdService(array $inputs, int $idService): array
    {
        $this->validator->validate($inputs, 'create');
        $findService = $this->serviceRepository->findById($idService);
        $confirm = $findService->need_approval === false ? true : null;
        $inputs += [
            'service_id' => $idService,
            'is_approved' => $confirm,
        ];
        $data = $this->brokenRepository->save($inputs);
        return ['broken_id' => $data->id];
    }

    public function getBrokenById(int $id): array
    {
        $data = $this->brokenRepository->getDataById($id);
        return $data->toArray();
    }

    public function updateBroken(array $inputs, int $id): array
    {
        $this->validator->validate($inputs, 'update');
        $data = $this->brokenRepository->save($inputs, $id);
        return [
            'broken_id' => $data->id,
            'service_id' => $data->service_id
        ];
    }

    public function updateBrokenCost(array $inputs, int $id): array
    {
        $this->validator->cost();
        $this->validator->validate($inputs, "updateCost");
        $data = $this->brokenRepository->save($inputs, $id);
        return ['broken_id' => $data->id];
    }

    public function updateBrokenConfirmation(array $inputs, int $id): array
    {
        $this->validator->confirm();
        $this->validator->validate($inputs, 'updateConfirm');
        $data = $this->brokenRepository->save($inputs, $id);
        return ['broken_id' => $data->id];
    }

    public function deleteBrokenById(int $id): string
    {
        $this->brokenRepository->delete($id);
        return 'sukses hapus data kerusakan';
    }
}
