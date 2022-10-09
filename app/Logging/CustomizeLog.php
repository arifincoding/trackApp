<?php

namespace App\Logging;

use ErrorException;
use Illuminate\Support\Facades\Auth;

class CustomizeLog
{
    /**
     * Customize the given logger instance.
     *
     * @param  \Illuminate\Log\Logger  $logger
     * @return void
     */
    public function __invoke($logger)
    {
        $logger->pushProcessor(function ($record) {
            $username = null;
            $name = 'anonymous';
            $role = 'pengguna';
            if (Auth::user()) {
                $username = Auth::payload()->get('username');
                $name = Auth::payload()->get('name');
                $role = Auth::payload()->get('role');
            }
            $record['extra']['user info'] = [
                'username' => $username,
                'name' => $name,
                'role' => $role
            ];
            return $record;
        });
    }
}