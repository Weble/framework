<?php

namespace ZFTests\Service;

use Zoolanders\Framework\Service\System as SystemService;

/**
 * Class SystemServiceTest
 * System service test
 *
 * @package ZFTests\Service
 */
abstract class SystemServiceTestLayer extends ServiceTest
{
    protected $serviceClassNames = [];

    /**
     * Service class instance provider
     */
    abstract protected function getServiceInstance();

    /**
     * Get class test (should return)
     */
    public function testGetClass(){
        $system = $this->getServiceInstance();
        //echo get_class($system->getClass());
        $this->assertTrue(in_array(get_class($system->getClass()), $this->serviceClassNames));
    }
}
