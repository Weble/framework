<?php

namespace ZFTests\Service\System;

use ZFTests\Service\SystemServiceTestLayer;
use Zoolanders\Framework\Service\System\Language;

/**
 * Class ApplicationServiceTest
 * Language service test
 *
 * @package ZFTests\Service\System
 */
class LanguageServiceTest extends SystemServiceTestLayer
{
    protected $serviceClassNames = ['JLanguage'];

    /**
     * Service class instance provider
     */
    protected function getServiceInstance()
    {
        return new Language(self::$container);
    }

    /**
     * Test translating
     *
     * @covers          Language::l()
     */
    public function testTranslation(){
        $lng = $this->getServiceInstance();
        $resource = parse_ini_file(JOOMLA_ENV_PATH . '/language/en-GB/en-GB.test.ini');

        if(empty($resource)){
            $this->markTestSkipped('No test resource file found');
        } else {
            $this->assertTrue($lng->load('test'));
            foreach($resource as $key => $value){
                $this->assertEquals($value, $lng->l($key));
            }
        }
    }
}
