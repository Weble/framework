<?php

namespace ZFTests\Service;

use Zoolanders\Framework\Service\Link;

/**
 * Class LinkServiceTest
 * Linking service unit test
 *
 * @package ZFTests\Service
 */
class LinkServiceTest extends ServiceTest
{
    /**
     * Test link build method
     *
     * @covers          Link::link()
     *
     * @dataProvider    validLinksDataSet
     */
    public function testLink($params, $expected){
        $lnk = new Link(self::$container);
        $_SERVER['HTTP_HOST'] = 'test.com';

        $this->assertStringEndsWith($expected, $lnk->link($params));
    }

    /**
     * Test route method
     *
     * @depends         testLink
     * @covers          Link::route()
     *
     * @dataProvider    validLinksDataSet
     */
    public function testRoute($params, $expected){
        $lnk = new Link(self::$container);
        // Mocking env variables:
        $_SERVER['HTTP_HOST'] = 'test.com';

        // Prepare to canonical:
        $parts = explode('index.php', $lnk->route($params));
        $route = array_pop($parts);

        $this->assertEquals($expected, $route);
    }

    /**
     * Links testing data provider
     */
    public function validLinksDataSet(){
        return [
            [ ['component' => 'com_zoo', 'view' => 'item', 'id' => 123], '?component=com_zoo&view=item&id=123'],
            [ ['task' => 'edit', 'id' => 123], '?task=edit&id=123'],
            [ ['view' => 'frontpage', 'application_id' => 1], '?view=frontpage&application_id=1'],
        ];
    }
}
