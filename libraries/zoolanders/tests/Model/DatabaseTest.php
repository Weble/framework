<?php

namespace ZFTests\Model;

use ZFTests\TestCases\ZFTestCaseFixtures;
use ZFTests\Classes\DatabaseModel;
use Zoolanders\Framework\Model\Database;
use ZFTests\Classes\CSVData;

/**
 * Class DatabaseTest
 * Database abstract class
 *
 * @package ZFTests\Model
 */
class DatabaseTest extends ZFTestCaseFixtures
{
    use CSVData;

    /**
     * @var string  Test cases source relative path
     */
    protected $_data_source = '/querying/database.csv';

    /**
     * Creates and returns instance of testing class
     */
    protected function getTestInstance(){

        return new DatabaseModel(self::$container->db, self::$container->zoo);
    }

    /**
     * Test fields specifying for query
     *
     * @covers          Database::fields()
     * @dataProvider    fieldsetProvider
     */
    public function testFields($fieldset, $expected){
        $dbm = $this->getTestInstance();
        $dbm->fields($fieldset);
        $dbm->buildQuery();

        $this->assertEquals($expected, str_replace("\n",'',$dbm->getQuery()->__toString()));
    }

    /**
     * Test query building methods, using test cases from data file
     */
    public function testQuerying(){
        $data = $this->loadTestDataCSV(FIXTURES_PATH . $this->_data_source);
        if(!empty($data)){
            foreach($data as $case){
                $methodName = array_shift($case);
                if(!empty($methodName)){
                    $args = @array_shift($case);
                    $expected = @array_shift($case);

                    if($args){
                        $args = eval(sprintf('return %s;', $args));
                    }

                    $dbm = $this->getTestInstance();

                    // Bind first where clause for alternatives
                    if(preg_match('/^or/iU', $methodName)){
                        $dbm->orWhere('id','=','1');
                    }

                    $reflection = new \ReflectionClass($dbm);
                    if($reflection->hasMethod($methodName)){
                        $methodReflection = $reflection->getMethod($methodName);
                        $methodReflection->invokeArgs($dbm, $args);
                        $dbm->buildQuery();

                        $this->assertEquals($expected, str_replace("\n", '', $dbm->getQuery()->__toString()), sprintf('Calling %s with provided params assertion failed', $methodName));
                    }
                }
            }
        }
    }

    /**
     * Get set table prefix methods test
     *
     * @covers          Database::setTablePrefix()
     * @covers          Database::getTablePrefix()
     *
     * @dataProvider    prefixDataProvider
     */
    public function testGetSetPrefix($prefix, $expected){
        $dbm = $this->getTestInstance();

        // Get set operations:
        $dbm->setTablePrefix($prefix);
        $this->assertEquals($prefix, $dbm->getTablePrefix($prefix));

        $dbm->where('id','=','1');
        $dbm->buildQuery();

        // Check if affects queries
        $this->assertEquals($expected, str_replace("\n", '', $dbm->getQuery()->__toString()));
    }

    /**
     * Fieldset provider
     */
    public function fieldsetProvider(){
        return [
            [ ['id'], "SELECT `id`FROM ``" ],
            [ ['id','alias'], "SELECT `id`,`alias`FROM ``" ]
        ];
    }

    /**
     * Test prefix data provider
     */
    public function prefixDataProvider(){
        return [
            ['a', 'SELECT `a`.*FROM `` AS `a`WHERE `a`.`id` = 1'],
            ['b', 'SELECT `b`.*FROM `` AS `b`WHERE `b`.`id` = 1'],
            ['c', 'SELECT `c`.*FROM `` AS `c`WHERE `c`.`id` = 1']
        ];
    }

}
