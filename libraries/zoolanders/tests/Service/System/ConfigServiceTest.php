<?php

namespace ZFTests\Service\System;

use ZFTests\Service\SystemServiceTestLayer;
use Zoolanders\Framework\Service\System\Config;

/**
 * Class ApplicationServiceTest
 * Config service test
 *
 * @package ZFTests\Service\System
 */
class ConfigServiceTest extends SystemServiceTestLayer
{
    protected $serviceClassNames = ['JConfig', 'Joomla\Registry\Registry'];

    /**
     * Service class instance provider
     */
    protected function getServiceInstance()
    {
        return new Config(self::$container);
    }
}
