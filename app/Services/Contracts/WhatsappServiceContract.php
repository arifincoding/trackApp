<?php

namespace App\Services\Contracts;

interface WhatsappServiceContract
{
    public function scanQr(): string;
    public function sendMessage(array $inputs, int $id): string;
}