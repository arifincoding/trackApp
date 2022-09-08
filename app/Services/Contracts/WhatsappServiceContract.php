<?php

namespace App\Services\Contracts;

interface WhatsappServiceContract
{
    public function scan(): array;
    public function chat(array $inputs, int $id): string;
}