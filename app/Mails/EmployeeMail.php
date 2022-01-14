<?php

namespace App\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeMail extends Mailable{
    use Queueable, SerializesModels;

    public string $username;
    public string $password;
    public function __construct(string $username, string $password){
        $this->username = $username;
        $this->password = $password;
    }

    public function build(){
        return $this->subject('username dan password akun tracking anda')->view('sendAccountInfo')->with(['username'=>$this->username,'password'=>$this->password]);
    }
}