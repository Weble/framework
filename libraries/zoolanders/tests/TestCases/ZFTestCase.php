<?php

namespace ZFTests\TestCases;

use Joomla\Registry\Registry;
use PHPUnit\Framework\TestCase;
use Zoolanders\Framework\Container\Container;
use Zoolanders\Framework\Event\Dispatcher;
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

    /**
     * Overrides the parent setup method.
     *
     * @return  void
     *
     * @see     \PHPUnit\Framework\TestCase::setUp()
     * @since   11.1
     */
    protected function setUp()
    {
        $this->setExpectedError();
        parent::setUp();
    }

    public static function setUpBeforeClass ()
    {
        parent::setUpBeforeClass();

        $config = new Registry();
        $config->loadFile(FRAMEWORK_ROOT . '/config.json');

        self::$container = Container::getInstance();
        self::$container->loadConfig($config);

        self::$container->eventstack = EventStackService::getInstance();
        self::$container->share(self::$container->eventstack);
    }

    public static function tearDownAfterClass ()
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
    public function __get ($name)
    {
        if ($name == 'container') {
            return self::$container;
        }
    }

    /**
     * Tells the unit tests that a method or action you are about to attempt
     * is expected to result in JError::raiseSomething being called.
     *
     * If you don't call this method first, the test will fail.
     * If you call this method during your test and the error does not occur, then your test
     * will also fail because we assume you were testing to see that an error did occur when it was
     * supposed to.
     *
     * If passed without argument, the array is initialized if it hsn't been already
     *
     * @param   mixed  $error  The JException object to expect.
     *
     * @return  void
     *
     * @deprecated  13.1
     * @since       12.1
     */
    public function setExpectedError($error = null)
    {
        if (!is_array($this->expectedErrors))
        {
            $this->expectedErrors = array();

            // Handle optional usage of JError until removed.
            if (class_exists(\JError::class))
            {
                \JError::setErrorHandling(E_NOTICE, 'callback', array($this, 'expectedErrorCallback'));
                \JError::setErrorHandling(E_WARNING, 'callback', array($this, 'expectedErrorCallback'));
                \JError::setErrorHandling(E_ERROR, 'callback', array($this, 'expectedErrorCallback'));
            }
        }
        if (!is_null($error))
        {
            $this->expectedErrors[] = $error;
        }
    }

    /**
     * Callback receives the error from JError and deals with it appropriately
     * If a test expects a JError to be raised, it should call this setExpectedError first
     * If you don't call this method first, the test will fail.
     *
     * @param   \JException  $error  The JException object from JError
     *
     * @return  \JException
     *
     * @deprecated  13.1
     * @since       12.1
     */
    public function expectedErrorCallback($error)
    {
        foreach ($this->expectedErrors as $key => $err)
        {
            $thisError = true;
            foreach ($err as $prop => $value)
            {
                if ($error->get($prop) !== $value)
                {
                    $thisError = false;
                }
            }
            if ($thisError)
            {
                unset($this->expectedErrors[$key]);
                return $error;
            }
        }
        $this->fail('An unexpected error occurred - ' . $error->get('message'));
        return $error;
    }

    /**
     * Assert event was triggered
     *
     * @param $eventName
     * @param callable $callback
     * @param string $message
     */
    public function assertEventTriggered ($eventName, callable $callback, $message = '')
    {
        $eventStack = self::$container->eventstack;
        $offset = $eventStack->find($eventName);
        $this->assertThat(($offset !== false), new \PHPUnit_Framework_Constraint_IsTrue, $message);

        if ($offset !== false) {
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
    public function assertTableHasRow ($tablename, $params, $message = '')
    {
        $sql = $this->buildMatchQuery($tablename, $params);
        $db = self::$container->db;
        $db->setQuery($sql);

        $result = $db->loadObjectList();

        if ($db->getErrorNum()) {
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
    public function assertTableHasNoRow ($tablename, $params, $message = '')
    {
        $sql = $this->buildMatchQuery($tablename, $params);
        $db = self::$container->db;
        $db->setQuery($sql);

        $result = $db->loadObjectList();

        if ($db->getErrorNum()) {
            // Mark assertion as failed or incompleted
            $this->markTestIncomplete('DB query built with errors');
        } else {
            $this->assertThat(empty($result), new \PHPUnit_Framework_Constraint_IsTrue(), $message);
        }

    }
}
