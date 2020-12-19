<?php

namespace App\Libraries\IAI;

abstract class Auth
{
    private  $login;
    private $password;

    public function __construct()
    {
        $this->login = config('app.loginIai');
        $this->password = config('app.passwordIai');
    }

    public  function getLogin()
    {
        return  $this->login;
    }

    public  function getAuthenticatedKey()
    {
        return $this->authenticateKey();
    }

    private function authenticateKey()
    {
        return  sha1(date('Ymd') . sha1($this->password));
    }
}
