<?php

namespace Zoolanders\Framework\Service;

use Zoolanders\Framework\Container\Container;
use Zoolanders\Framework\Service\System\Config;

class Crypt
{
    /**
     * @var \JCrypt
     */
    public $crypt;

    /**
     * Crypt constructor
     */
    public function __construct (Config $config)
    {
        $secret = $config->get('secret');

        $key = new \JCryptKey('simple', $secret, $secret);
        $this->crypt = new \JCrypt(null, $key);
    }

    /**
     * Encrypt text
     * @param $text
     */
    public function encrypt ($text)
    {
        return $this->crypt->encrypt($text);
    }

    /**
     * Decrypt text
     * @param $text
     */
    public function decrypt ($text)
    {
        return $this->crypt->decrypt($text);
    }

    /**
     * Password field decryption
     *
     * @param  string $pass The encrypted password to decrypt
     *
     * @return string The decrypted password
     */
    public function decryptPassword ($pass)
    {
        $matches = array();
        if (preg_match('/zl-encrypted\[(.*)\]/', $pass, $matches)) {
            return $this->crypt->decrypt($matches[1]);
        }

        // if no valid pass to decrypt, return orig pass
        return $pass;
    }
}
