<?php

namespace ZFTests\Controller;

use ZFTests\TestCases\ZFTestCase;
use ZFTests\Classes\TestController;

/**
 * Class ControllerTest
 * Controller tests
 *
 * @package ZFTests\Controller
 */
class ControllerTest extends ZFTestCase
{
    /**
     * Test redirect flow
     *
     * @covers      Controller::setRedirect()
     * @covers      Controller::hasRedirect()
     */
    public function testRedirects(){
        $ctrl = new TestController(self::$container);

        $ctrl->setRedirect('/');

        $this->assertTrue($ctrl->hasRedirect());
    }

    /**
     * Actions and expected responses mapping set
     */
    public function actionMap(){
        return [
            ['ping', 'test'],
            ['_default', 'default']
        ];
    }
}
