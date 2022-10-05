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
        $query = $this->brokenRepository->getListDataByIdService($id);
        $fractal = new Manager();
        $data = $fractal->createData(new Collection($query, new BrokensTransformer))->toArray();
        return $data;
    }

    public function newBrokenByIdService(array $inputs, int $idService): array
    {
        $this->brokenValidator->validate($inputs);
        $findService = $this->serviceRepository->findDataById($idService);
        $confirm = null;
        if ($findService->butuhPersetujuan === false) {
            $confirm = true;
        }
        $inputs += [
            'idService' => $idService,
            'disetujui' => $confirm,
        ];
        $data = $this->brokenRepository->save($inputs);
        return ['idKerusakan' => $data->id];
    }

    public function getBrokenById(int $id): array
    {
        $data = $this->brokenRepository->getDataById($id);
        return $data;
    }

    public function updateBroken(array $inputs, int $id): array
    {
        $this->brokenValidator->validate($inputs);
        $data = $this->brokenRepository->save($inputs, $id);
        return [
            'idKerusakan' => $data->id,
            'idService' => $data->idService
        ];
    }

    public function updateBrokenCost(array $inputs, int $id): array
    {
        $this->brokenValidator->cost();
        $this->brokenValidator->validate($inputs);
        $data = $this->brokenRepository->save($inputs, $id);
        return ['idKerusakan' => $data->id];
    }

    public function updateBrokenCofirmation(array $inputs, int $id): array
    {
        $this->brokenValidator->confirm();
        $this->brokenValidator->validate($inputs);
        $data = $this->brokenRepository->save($inputs, $id);
        return ['idKerusakan' => $data->id];
    }

    public function deleteBrokenById(int $id): string
    {
        $this->brokenRepository->deleteById($id);
        return 'sukses hapus data kerusakan';
    }
}