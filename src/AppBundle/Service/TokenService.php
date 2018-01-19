<?php

namespace AppBundle\Service;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TokenService extends Controller
{
    public function tokenCreation()
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $newToken = '';
        $max = strlen($characters) - 1;
        for ($i = 0; $i < 100; $i++) {
            $newToken .= $characters[mt_rand(0, $max)];
        }

        return $newToken;
    }
}