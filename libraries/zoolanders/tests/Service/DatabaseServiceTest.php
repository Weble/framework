<?php

namespace ZFTests\Service;

use Zoolanders\Framework\Service\Database;
use ZFTests\TestCases\ZFTestCaseFixtures;
use ZFTests\Classes\CSVData;
use ZFTests\Classes\Tag;

/**
 * Class DatabaseServiceTest
 * Database service test class
 *
 * @package ZFTests\Service
 */
class DatabaseServiceTest extends ZFTestCaseFixtures
{
    use CSVData;

    /**
     * @var string  Test cases source relative path
     */
    protected $_data_source = '/querying/database_service.csv';

    /**
     * Test db init
     */
    public function testDBInit(){
        $db = self::$container->db;

        $this->assertTrue(in_array($db->name, ['mysql', 'mysqli', 'postgresql', 'sqlite']));
    }

    /**
     * Test DB querying
     *
     * @covers      Database::query()
     */
    public function testDBQuery(){
        $db = self::$container->db;

        $sql = "INSERT INTO `#__zoo_tag` (`item_id`, `name`) VALUES(1, 'test')";
        $result = $db->query($sql);

        $this->assertNotFalse($result);
        $this->assertTableHasRow('zoo_tag', ['item_id'=>1, 'name'=>'test']);

        $sql = "DELETE FROM `#__zoo_tag` WHERE item_id=1 AND name='test'";
        $result = $db->query($sql);

        $this->assertNotFalse($result);
        $this->assertTableHasNoRow('zoo_tag', ['item_id'=>1, 'name'=>'test']);
    }

    /**
     * Run to check query against src data and expected result
     *
     * @covers          Database::queryResult()
     */
    public function testQuerying(){

        $data = $this->loadTestDataCSV(FIXTURES_PATH . $this->_data_source);
        if(!empty($data)){
            foreach($data as $case){
                $methodName = array_shift($case);
                if(!empty($methodName)){
                    $methodName = 'query' . ucfirst($methodName);
                    $args = @array_shift($case);
                    $expected = eval('return ' . @array_shift($case) . ';');

                    $db = self::$container->db;

                     $reflection = new \ReflectionClass($db);
                    if($reflection->hasMethod($methodName)){
                        $methodReflection = $reflection->getMethod($methodName);
                        $result = $methodReflection->invokeArgs($db, (array)$args);

                        $this->assertEquals($expected, $result, sprintf('Calling %s with provided params assertion failed', $methodName));
                    }
                }
            }
        }
    }

    /**
     * Test table pfx replace
     *
     * @covers          Database::replacePrefix()
     */
    public function testPrefixReplace(){
        $db = self::$container->db;
        $expected = $db->getPrefix() . 'zoo_item';

        $this->assertEquals($expected, $db->replacePrefix('#__zoo_item'));
    }
}
