<?php

namespace Jdm\Graffiti;

use CUser;

class SecurityService
{
    public function __construct()
    {
    }

    /**
     * @return string
     */
    protected function getClientIP() {
        $ip = $_SERVER['HTTP_CLIENT_IP']?$_SERVER['HTTP_CLIENT_IP']:($_SERVER['HTTP_X_FORWARDE‌​D_FOR']?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR']);

        return $ip;
      }

    /**
     * @return int
     */
    protected function getHashAlgorithm()
    {
        return PASSWORD_DEFAULT;
    }

    /**
     * @return int
     */
    protected function getEncodersCost()
    {
        return 10;
    }

    /**
     * @return string
     */
    public function generateSalt()
    {
        return uniqid();
    }

    /**
     * @param  string $password
     * @param  string $salt
     *
     * @return string
     */
    public function passwordHash($password, $salt)
    {
        $algo = $this->getHashAlgorithm();
        $cost = $this->getEncodersCost();

        $password = trim($password).$salt;

        return password_hash($password, $algo, ['cost' => $cost]);
    }

    /**
     * @param  string $password
     * @param  string $salt
     * @param  string $hash
     *
     * @return bool
     */
    public function passwordVerify($password, $salt, $hash)
    {
        $password = trim($password).$salt;

        return password_verify($password, $hash);
    }

    /**
     * @param  int    $id
     * @param  string $salt
     *
     * @return string
     */
    public function generateAccessToken($id, $salt)
    {
        $userId = session_id();
        $ip = $this->getClientIP();

        return md5(implode('|', [$id, $userId, $ip, $salt]));
    }

    /**
     * @param  int    $id
     * @param  string $salt
     * @param  string $token
     *
     * @return bool
     */
    public function accessTokenVerify($id, $salt, $token)
    {
        $userId = session_id();
        $ip = $this->getClientIP();

        $currentToken = md5(implode('|', [$id, $userId, $ip, $salt]));

        return $token == $currentToken;
    }
}
