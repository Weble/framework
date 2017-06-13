<?php

namespace ZFTests\Service\System;

use ZFTests\Service\SystemServiceTestLayer;
use Zoolanders\Framework\Service\System\Application;

/**
 * Class ApplicationServiceTest
 * Application service test
 *
 * @package ZFTests\Service\System
 */
class ApplicationServiceTest extends SystemServiceTestLayer
{
    protected $serviceClassNames = ['JApplicationSite'];

    /**
     * Service class instance provider
     */
    protected function getServiceInstance()
    {
        return new Application(self::$container);
    }
}
