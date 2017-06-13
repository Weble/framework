<?php

namespace ZFTests\Classes\Providers;

/**
 * Class KVProvider
 * Simple key-value data provider for
 *
 * @package ZFTests\Classes\Providers
 */
trait SimpleDataSetProvider
{
    /**
     * Simple Key-Value data set provider
     * Can be used for most of simple unit tests
     */
    public function simpleKeyValueProvider(){
        return [
            ['a', 'alpha'],
            ['b', 'bravo'],
            ['c', 'charlie']
        ];
    }
}
