<?php

namespace ZFTests\Data;

use Zoolanders\Framework\Data\Json;

/**
 * Class JsonTest
 * Json data tests
 *
 * @package ZFTests\Data
 */
class JsonTest extends DataTest
{
    /**
     * Make dataset for forward testing:
     */
    protected function makeDataSet($dataset){

        $this->object = new Json($dataset);

        return $this->object;
    }
}
