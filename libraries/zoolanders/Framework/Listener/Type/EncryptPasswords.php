<?php

namespace Zoolanders\Framework\Listener\Type;

use Zoolanders\Framework\Listener\Listener;
use Zoolanders\Framework\Service\Crypt;

class EncryptPasswords extends Listener
{
    /**
     * @var Crypt
     */
    protected $crypt;

    /**
     * EncryptPasswords constructor.
     * @param Crypt $crypt
     */
    function __construct (Crypt $crypt)
    {
        $this->crypt = $crypt;
    }

    /**
     * @param \Zoolanders\Framework\Event\Type\Beforesave $event
     */
    public function handle (\Zoolanders\Framework\Event\Type\Beforesave $event)
    {
        $type = $event->getType();
        $elements = $type->config->get('elements');

        // search for decrypted passwords and encrypt
        array_walk_recursive($elements, function (&$item, $key) {
            $this->findAndEncrypt($item, $key);
        });

        // save result
        $type->config->set('elements', $elements);
    }

    /**
     * @param $item
     * @param $key
     */
    protected function findAndEncrypt (&$item, $key)
    {
        $matches = array();
        if (preg_match('/zl-decrypted\[(.*)\]/', $item, $matches)) {
            $item = 'zl-encrypted[' . $this->crypt->encrypt($matches[1]) . ']';
        }
    }
}
