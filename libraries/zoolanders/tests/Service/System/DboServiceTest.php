<?php

namespace ZFTests\Service\System;

use ZFTests\Service\SystemServiceTestLayer;
use Zoolanders\Framework\Service\System\Dbo;

/**
 * Class ApplicationServiceTest
 * Dbo service test
 *
 * @package ZFTests\Service\System
 */
class DboServiceTest extends SystemServiceTestLayer
{
    protected $serviceClassNames = ['JDatabaseDriverMysqli', 'JDatabaseDriverMysql'];

    /**
     * Service class instance provider
     */
    protected function getServiceInstance()
    {
        return new Dbo(self::$container);
    }
}
