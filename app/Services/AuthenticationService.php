<?php


namespace App\Services;

use Firebase\Auth\Token\Exception\InvalidToken;

class AuthenticationService
{

    private $auth;

    public function __construct()
    {
        $this->auth = app('firebase.auth');
    }


    public function getNumberPhone($idTokenString)
    {
        try {
            $verifiedIdToken = $this->auth->verifyIdToken($idTokenString);
            $uid = $verifiedIdToken->getClaims()['sub']->getValue();
            $user = $this->auth->getUser($uid);
            return $user->phoneNumber;
        } catch (InvalidToken $e) {
            throw new \Exception( 'El token es invalido : '.$e->getMessage());
        } catch (\InvalidArgumentException $e) {
            throw new \Exception( 'El token no se pudo analizar : '.$e->getMessage());
        }
    }
}