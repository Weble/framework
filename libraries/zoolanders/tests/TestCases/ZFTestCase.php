<?php

namespace ZFTests\TestCases;

use Joomla\Registry\Registry;
use PHPUnit\Framework\TestCase;
use Zoolanders\Framework\Container\Container;
use Zoolanders\Framework\Service\Event;
use ZFTests\Classes\EventStackService;
use ZFTests\Classes\DBUtils;

/**
 * Class ZFTestCase
 * Extended TestCase class
 *
 * @package Zoolanders\Framework\TestCases
 */
class ZFTestCase extends TestCase
{
    use DBUtils;

    /**
     * @var DI container
     */
    protected static $container;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $config = new Registry();
        $config->loadFile(FRAMEWORK_ROOT.'/config.json');

        self::$container = Container::getInstance();
        self::$container->loadConfig($config);

        //self::$container['eventstack'] = EventStackService::getInstance();
        //self::$container['event'] = new Event(self::$container);
    }

    public static function tearDownAfterClass()
    {
        self::$container = null;

        parent::tearDownAfterClass();
    }

    /**
     * Magic getter for container
     *
     * @param   string
     *
     * @return  mixed
     */
    public function __get($name){
        if ($name == 'container') {
            return self::$container;
        } else {
            return parent::__get($name);
        }
    }

    /**
     * Assert event was triggered
     *
     * @param $eventName
     * @param callable $callback
     * @param string $message
     */
    public function assertEventTriggered($eventName, callable $callback, $message = ''){
        $eventStack = self::$container->eventstack;
        $offset = $eventStack->find($eventName);
        $this->assertThat(($offset !== false), new \PHPUnit_Framework_Constraint_IsTrue, $message);

        if($offset !== false)
        {
            call_user_func($callback, $eventStack->get($offset));
        }
    }

    /**
     * Assert DB table has row with provided column values
     *
     * @param   $tablename (without prefixes)
     * @param   $params
     * @param   string $message
     */
    public function assertTableHasRow($tablename, $params, $message = ''){
        $sql = $this->buildMatchQuery($tablename, $params);
        $db = self::$container->db;
        $db->setQuery($sql);

        $result = $db->loadObjectList();

        if($db->getErrorNum()){
            // Mark assertion as failed or incompleted
            $this->markTestIncomplete('DB query built with errors');
        } else {
            $this->assertThat(empty($result), new \PHPUnit_Framework_Constraint_IsFalse, $message);
        }

    }

    /**
     * Assert DB table has no rows with provided column values
     *
     * @param   $tablename (without prefixes)
     * @param   $params
     * @param   string $message
     */
    public function assertTableHasNoRow($tablename, $params, $message = ''){
        $sql = $this->buildMatchQuery($tablename, $params);
        $db = self::$container->db;
        $db->setQuery($sql);

        $result = $db->loadObjectList();

        if($db->getErrorNum()){
            // Mark assertion as failed or incompleted
            $this->markTestIncomplete('DB query built with errors');
        } else {
            $this->assertThat(empty($result), new \PHPUnit_Framework_Constraint_IsTrue(), $message);
        }

    }
}
