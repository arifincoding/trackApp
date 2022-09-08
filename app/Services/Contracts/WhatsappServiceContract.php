<?php

namespace App\Services\Contracts;

interface WhatsappServiceContract
{
    public function scanQr(): array;
    public function sendMessage(array $inputs, int $id): string;
}