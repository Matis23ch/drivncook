<?php

class AuthController
{
    public static function login($email, $password)
    {
        // À connecter plus tard à la BDD
        if ($email === 'admin@drivncook.fr') {
            $_SESSION['role'] = 'admin';
            return true;
        }

        $_SESSION['role'] = 'franchisé';
        return true;
    }

    public static function logout()
    {
        session_destroy();
    }
}
