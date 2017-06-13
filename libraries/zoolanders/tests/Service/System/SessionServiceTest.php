<?php

namespace ZFTests\Service\System;

use ZFTests\Service\SystemServiceTestLayer;
use Zoolanders\Framework\Service\System\Session;

/**
 * Class ApplicationServiceTest
 * Session service test
 *
 * @package ZFTests\Service\System
 */
class SessionServiceTest extends SystemServiceTestLayer
{
    protected $serviceClassNames = ['JSession'];

    /**
     * Service class instance provider
     */
    protected function getServiceInstance()
    {
        return new Session(self::$container);
    }
}
