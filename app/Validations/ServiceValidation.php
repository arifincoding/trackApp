<?php

namespace App\Validations;

use App\Validations\Validation;

class ServiceValidation extends Validation
{

    function __construct()
    {
        $this->rules = [
            'customer.name' => 'required|regex:/^[\pL\s\-]+$/u',
            'customer.telp' => 'required|nullable|numeric',
            'customer.is_whatsapp' => 'required|boolean',
            'product.name' => 'required',
            'product.category_id' => 'required|exists:categories,id',
            'complaint' => 'required',
            'need_approval' => 'boolean',
            'estimated_cost' => 'numeric',
            'down_payment' => 'numeric'
        ];
        $this->exceptionMessages = [
            'create' => 'could not create a single service data caused the given data is invalid',
            'update' => 'could not update a single service data caused the given data is invalid',
            'updateStatus' => 'could not update service status caused the given data is invalid',
            'updateConfirmation' => 'could not set service confirmation caused the given data is invalid',
            'updateWarranty' => 'could not update service warranty caused the given data is invalid'
        ];
    }

    function statusService()
    {
        $this->rules = [
            'status' => 'required'
        ];
    }

    function serviceWarranty()
    {
        $this->rules = [
            'warranty' => 'required'
        ];
    }

    function confirmation()
    {
        $this->rules = [
            'is_approved' => 'required|boolean'
        ];
    }
}
