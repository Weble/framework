<?php

namespace ZFTests\Service;

use Zoolanders\Framework\Service\Data;

/**
 * Class DataServiceTest
 * Data service tests
 *
 * @package ZFTests\Service
 */
class DataServiceTest extends ServiceTest
{
    /**
     * Test create data container
     *
     * @covers          Data::create()
     *
     * @dataProvider    dataFormatSet
     */
    public function testCreate($data, $format){
        $data_service = self::$container->data;
        $object = $data_service->create($data, $format);

        // Check interface(class)
        $this->assertInstanceOf('\\Zoolanders\\Framework\\Data\\' . ucfirst(strtolower($format)), $object);
        // Check accessibility:
        $this->assertTrue($object->has('a'));
        $this->assertEquals('alpha', $object->get('a'));
    }

    /**
     * Data set for test creating different data types
     */
    public function dataFormatSet(){
        return [
            [ array('a' => 'alpha'), 'json'],
            [ array('a' => 'alpha'), 'data'],
            [ array('a' => 'alpha'), 'parameter']
        ];
    }
}
