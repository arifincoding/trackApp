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
        $data = $this->model->with('klien', 'produk')->orderByDesc('id');
        // filter status service
        if (isset($inputs['status'])) {
            $data->where('status', $inputs['status']);
        }
        // filter kategori produk
        if (isset($inputs['kategori'])) {
            $data->whereHas('produk', function ($q) use ($inputs) {
                $q->where('kategori', $inputs['kategori']);
            });
        }
        //filter cari
        if (isset($inputs['cari'])) {
            $data->where(function ($q) use ($inputs) {
                $q->orWhere('kode', 'LIKE', '%' . $inputs['cari'] . '%');
            });
            $data->orWhereHas('klien', function ($q) use ($inputs) {
                $q->where('nama', 'LIKE', '%' . $inputs['cari'] . '%');
                $q->orWhere('noHp', 'LIKE', '%' . $inputs['cari'] . '%');
            });
            $data->orWhereHas('produk', function ($q) use ($inputs) {
                $q->where('nama', 'LIKE', '%' . $inputs['cari'] . '%');
            });
        }
        return $data->get();
    }

    public function getDataWithRelationById(int $id): Service
    {
        return $this->model->with(['klien', 'produk', 'kerusakan' => function ($q) {
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
            array_push($resp, $item['kategori']['nama']);
        }
        $data = $this->model->with('produk')->where('status', 'antri')->orderByDesc('id');
        $data->whereHas('produk', function ($q) use ($resp) {
            $q->whereIn('kategori', $resp);
        });
        if (isset($inputs['kategori'])) {
            $data->whereHas('produk', function ($q) use ($inputs) {
                $q->where('kategori', $inputs['kategori']);
            });
        }
        if (isset($inputs['cari'])) {
            $data->where('kode', 'LIKE', '%' . $inputs['cari'] . '%');
            $data->orWhereHas('produk', function ($q) use ($inputs) {
                $q->where('nama', 'LIKE', '%' . $inputs['cari'] . '%');
            });
        }
        return $data->get();
    }

    public function getListDataMyProgress(string $username, array $inputs = []): Collection
    {
        $data = $this->model->with('produk')->where('usernameTeknisi', $username)->orderByDesc('id');
        if (isset($inputs['status'])) {
            $data->where('status', $inputs['status']);
        }
        if (isset($inputs['kategori'])) {
            $data->whereHas('produk', function ($q) use ($inputs) {
                $q->where('kategori', $inputs['kategori']);
            });
        }
        if (isset($inputs['cari'])) {
            $data->where('kode', 'LIKE', '%' . $inputs['cari'] . '%');
            $data->orWhereHas('produk', function ($q) use ($inputs) {
                $q->where('nama', 'LIKE', '%' . $inputs['cari'] . '%');
            });
        }
        return $data->get();
    }

    public function getDataByCode(string $code): ?Service
    {
        $data = $this->model->with(['produk', 'kerusakan' => function ($q) {
            $q->orderByDesc('id');
        }, 'riwayat' => function ($q) {
            $q->orderByDesc('id');
        }])->where('kode', $code)->first();
        return $data;
    }

    public function setCodeService(int $id): bool
    {
        $date = Carbon::now('GMT+7');
        $attributs = [
            'kode' => $date->format('y') . $date->format('m') . $date->format('d') . sprintf("%03d", $id)
        ];
        $this->save($attributs, $id);
        return true;
    }
}