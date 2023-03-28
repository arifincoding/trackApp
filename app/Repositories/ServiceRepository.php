<?php

namespace App\Repositories;

use App\Models\Service;
use App\Repositories\Repository;
use Illuminate\Support\Carbon;
use App\Repositories\Contracts\ServiceRepoContract;
use Illuminate\Database\Eloquent\Collection;

class ServiceRepository extends Repository implements ServiceRepoContract
{
    public function __construct(Service $model)
    {
        parent::__construct($model, 'service');
    }

    public function getListData(array $inputs = []): Collection
    {
        $data = $this->model->with('client', 'product')->orderByDesc('id');
        // filter status service
        if (isset($inputs['status'])) {
            $data->where('status', $inputs['status']);
        }
        // filter kategori produk
        if (isset($inputs['category'])) {
            $data->whereHas('product', function ($q) use ($inputs) {
                $q->where('name', $inputs['category']);
            });
        }
        //filter cari
        if (isset($inputs['search'])) {
            $data->where(function ($q) use ($inputs) {
                $q->orWhere('code', 'LIKE', '%' . $inputs['search'] . '%');
            });
            $data->orWhereHas('client', function ($q) use ($inputs) {
                $q->where('name', 'LIKE', '%' . $inputs['search'] . '%');
                $q->orWhere('telp', 'LIKE', '%' . $inputs['search'] . '%');
            });
            $data->orWhereHas('product', function ($q) use ($inputs) {
                $q->where('name', 'LIKE', '%' . $inputs['search'] . '%');
            });
        }
        return $data->get();
    }

    public function getDataWithRelationById(int $id): Service
    {
        return $this->model->with(['client', 'product', 'broken' => function ($q) {
            $q->orderByDesc('id');
        }])->where('id', $id)->firstOrFail();
    }

    public function getListDataQueue(?array $responbility = null, array $inputs = []): Collection
    {
        if ($responbility === null) {
            return collect([]);
        }
        $resp = [];
        foreach ($responbility as $item) {
            array_push($resp, $item['category']['name']);
        }
        $data = $this->model->with('product')->where('status', 'antri')->orderByDesc('id');
        $data->whereHas('product', function ($q) use ($resp) {
            $q->whereIn('name', $resp);
        });
        if (isset($inputs['category'])) {
            $data->whereHas('product', function ($q) use ($inputs) {
                $q->where('name', $inputs['category']);
            });
        }
        if (isset($inputs['search'])) {
            $data->where('code', 'LIKE', '%' . $inputs['search'] . '%');
            $data->orWhereHas('product', function ($q) use ($inputs) {
                $q->where('name', 'LIKE', '%' . $inputs['search'] . '%');
            });
        }
        return $data->get();
    }

    public function getListDataMyProgress(string $username, array $inputs = []): Collection
    {
        $data = $this->model->with('product')->where('tecnicion_username', $username)->orderByDesc('id');
        if (isset($inputs['status'])) {
            $data->where('status', $inputs['status']);
        }
        if (isset($inputs['category'])) {
            $data->whereHas('product', function ($q) use ($inputs) {
                $q->where('name', $inputs['category']);
            });
        }
        if (isset($inputs['search'])) {
            $data->where('code', 'LIKE', '%' . $inputs['search'] . '%');
            $data->orWhereHas('product', function ($q) use ($inputs) {
                $q->where('name', 'LIKE', '%' . $inputs['search'] . '%');
            });
        }
        return $data->get();
    }

    public function getDataByCode(string $code): ?Service
    {
        $data = $this->model->with(['product', 'broken' => function ($q) {
            $q->orderByDesc('id');
        }, 'history' => function ($q) {
            $q->orderByDesc('id');
        }])->where('code', $code)->first();
        return $data;
    }

    public function setCodeService(int $id): bool
    {
        $date = Carbon::now('GMT+7');
        $attributs = [
            'code' => $date->format('y') . $date->format('m') . $date->format('d') . sprintf("%03d", $id)
        ];
        $this->save($attributs, $id);
        return true;
    }
}
