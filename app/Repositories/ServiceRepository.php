<?php

namespace App\Repositories;

use App\Models\Service;
use App\Repositories\Repository;
use Illuminate\Support\Carbon;
use App\Repositories\Contracts\ServiceRepoContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ServiceRepository extends Repository implements ServiceRepoContract
{
    public function __construct(Service $model)
    {
        parent::__construct($model, 'service');
    }

    public function getListData(array $inputs = []): Collection
    {
        $search = $inputs['search'] ?? null;
        $status = $inputs['status'] ?? null;
        $category = $inputs['category'] ?? null;

        $searchAttributs = [
            'services.code' => '',
            'services.complaint' => '',
            'customers.name',
            'products.name' => '',
        ];
        $this->model->setToSearchableArray($searchAttributs);

        $data = $this->model->search($search)->query(function ($query) use ($status, $category) {

            $attributs = ['services.id as service_id', 'services.code', 'services.complaint', 'services.status', 'services.total_cost', 'services.is_take', 'services.is_approved', 'customers.name as customer_name', 'customers.telp', 'products.name as products_name', 'categories.name as category'];

            $query->select($attributs)->join('customers', 'services.customer_id', 'customers.id')->join('products', 'services.product_id', 'products.id')->join('categories', 'products.category_id', 'categories.id');

            $status ? $query->where('services.status', $status) : '';
            $category ? $query->where('categories.name', $category) : '';
            $query->orderByDesc('service_id');
        });
        return $data->get();
    }

    public function getDataWithRelationById(int $id): Service
    {
        return $this->model->with(['client', 'product', 'broken' => function ($q) {
            $q->orderByDesc('id');
        }])->where('id', $id)->firstOrFail();
    }

    public function getListDataQueue(string $username, array $inputs = []): Collection
    {
        $category = $inputs['category'] ?? null;
        $search = $inputs['search'] ?? null;
        $searchAttributs = [
            'services.code' => '',
            'services.complaint' => '',
            'products.name' => '',
        ];
        $this->model->setToSearchableArray($searchAttributs);
        $data = $this->model->search($search)->query(function ($query) use ($username, $category) {

            $attributs = ['services.id as service_id', 'services.code', 'services.complaint', 'services.status', 'services.is_approved', 'products.name as product_name', 'categories.name as category'];

            $query->select($attributs)->join('products', 'services.product_id', 'products.id')->join('categories', 'products.category_id', 'categories.id')->where('services.status', 'antri');

            if (!$category) {
                $query->whereIn('products.category_id', DB::table('responbilities')->select('category_id')->where('username', $username));
            } else {
                $query->where('categories.name', $category);
            }

            $query->orderByDesc('services.id');
        });

        return $data->get();
    }

    public function getListDataMyProgress(string $username, array $inputs = []): Collection
    {
        $search = $inputs['search'] ?? null;
        $status = $inputs['status'] ?? null;
        $category = $inputs['category'] ?? null;

        $searchAttributs = [
            'services.code' => '',
            'services.complaint' => '',
            'products.name' => '',
        ];
        $this->model->setToSearchableArray($searchAttributs);

        $data = $this->model->search($search)->query(function ($query) use ($username, $status, $category) {
            $attributs = ['services.id as service_id', 'sevices.code', 'services.complaint', 'services.status', 'services.is_approved', 'products.name as product_name', 'categories.name as category'];
            $query->select($attributs)->join('products', 'services.product_id', 'products.id')->join('categories', 'products.category_id', 'categories.id')->where('tecnician_username', $username);
            $status ? $query->where('services.status', $status) : '';
            $category ? $query->where('categories.name', $category) : '';
            $query->orderByDesc('services.id');
        });
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
