<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Listener\Type;

use Zoolanders\Framework\Listener\Listener;

class EncryptPasswords extends Listener
{
    /**
     * @param \Zoolanders\Framework\Event\Type\Beforesave $event
     */
    public function handle(\Zoolanders\Framework\Event\Type\Beforesave $event)
    {
        $type = $event->getType();
        $elements = $type->config->get('elements');

        // search for decrypted passwords and encrypt
        array_walk_recursive($elements, function(&$item, $key){
            $this->findAndEncrypt($item, $key);
        });

        // save result
        $type->config->set('elements', $elements);
    }

    /**
     * @param $item
     * @param $key
     */
    protected function findAndEncrypt(&$item, $key)
    {
        $matches = array();
        if (preg_match('/zl-decrypted\[(.*)\]/', $item, $matches)) {
            $item = 'zl-encrypted[' . $this->container->encrypt($matches[1]) . ']';
        }
    }
}