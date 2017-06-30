<?php

namespace Zoolanders\Framework\Service;

use Zoolanders\Framework\Service\System\Dbo;

/**
 * Helper for the database operations
 *
 * @package Framework.Helpers
 */
class Database
{

    /**
     * The name of the database driver we're using
     *
     * @var string
     */
    public $name;

    /**
     * The database driver we're using to interact with the database
     *
     * @var \JDatabaseDriver
     */
    protected $_database;

    /**
     * Class Constructor
     */
    public function __construct (Dbo $dbo)
    {
        // set database
        $this->_database = $dbo;
        $this->name = $this->_database->name;
    }

    /**
     * Execute a query
     *
     * The result returned by this method is the same of the JDatabase::execute() method
     * Basically, it returns if the operation was successful or not. Generally used
     * by DELETE and UPDATE operations
     *
     * @param string $query The query to execute
     *
     * @return mixed The result of the query
     *
     * @throws \RuntimeException
     */
    public function query ($query)
    {
        // query database table
        $this->_database->setQuery($query);
        return $this->_database->execute();

    }

    /**
     * Execute a query to the database and load the result returned
     *
     * The result returned by this method is the same as the one returned by JDatabase::loadResult()
     * Basically, it returns the result (single field) of a database operation
     * Generally used for SELECT field operations
     *
     * @param string $query The query to be executed
     *
     * @return mixed The result of the query
     *
     * @throws \RuntimeException
     */
    public function queryResult ($query)
    {

        // query database table
        $this->_database->setQuery($query);
        return $this->_database->loadResult();

    }

    /**
     * Execute a query to the database and load the result returned as an object
     *
     * The result returned by this method is the same as the one returned by JDatabase::loadObject()
     * Basically, it returns the first result of a database operation as an object
     * Generally used for SELECT fields operations
     *
     * @param string $query The query to be executed
     * @param string Entity class name
     *
     * @return object The result of the query
     *
     * @throws \RuntimeException
     */
    public function queryObject ($query, $class = 'stdClass')
    {
        // query database table
        $this->_database->setQuery($query);
        return $this->_database->loadObject($class);

    }

    /**
     * Execute a query to the database and load the result returned as a list of objects
     *
     * The result returned by this method is the same as the one returned by JDatabase::loadObjectList()
     * Basically, it returns a list of results (objects) of a database operation
     * Generally used for SELECT operations
     *
     * @param string $query The query to be executed
     * @param string $key The class to be used to create objects (default: stdClass)
     * @param int   Offset
     * @param int   Limit
     *
     * @return array The results of the query
     *
     * @throws \RuntimeException
     */
    public function queryObjectList ($query, $key = '', $class = 'stdClass', $offset = 0, $limit = 0)
    {

        // query database table
        $this->_database->setQuery($query, $offset, $limit);
        return $this->_database->loadObjectList($key, $class);

    }

    /**
     * Execute a query to the database and  get an array of values from the <var>$numinarray</var> field in
     * each row of the result set from the database query.
     *
     * The result returned by this method is the same as the one returned by JDatabase::loadColumn()
     *
     * @param string $query The query to be executed
     * @param int $numinarray The offset of the result to get
     *
     * @return mixed The result of the query
     *
     * @throws \RuntimeException
     */
    public function queryResultArray ($query, $numinarray = 0)
    {

        // query database table
        $this->_database->setQuery($query);
        return $this->_database->loadColumn($numinarray);

    }

    /**
     * Execute a query to the database and get the first row of the result set from the database query as an associative array
     *
     * The result returned by this method is the same as the one returned by JDatabase::loadAssoc()
     * Generally used for SELECT operations
     *
     * @param string $query The query to be executed
     *
     * @return array The first row of the result of the query
     *
     * @throws \RuntimeException
     */
    public function queryAssoc ($query)
    {

        // query database table
        $this->_database->setQuery($query);
        return $this->_database->loadAssoc();

    }

    /**
     * Execute a query to the database and get an array of the result set rows from the database query where each row is an associative array
     *
     * The result returned by this method is the same as the one returned by JDatabase::loadAssocList()
     * Generally used for SELECT operations
     *
     * @param string $query The query to be executed
     * @param string $key The name of a field on which to key the result array
     * @param int   Offset
     * @param int   Limit
     *
     * @return array The results of the query
     *
     * @throws RuntimeException
     */
    public function queryAssocList ($query, $key = '', $offset = 0, $limit = 0)
    {

        // query database table
        $this->_database->setQuery($query, $offset, $limit);
        return $this->_database->loadAssocList($key);
    }

    /**
     * Insert an object into a table
     *
     * @param AppTable $table The table object in which to insert the object
     * @param object $object The object to insert
     * @param string $key The name of the primary key of the table
     *
     * @return boolean If the operation was successful
     *
     * @throws RuntimeException
     */
    public function insertObject ($table, $object, $key = null)
    {

        // insert object
        return $this->_database->insertObject($table, $object, $key);

    }

    /**
     * Update an object into a table
     *
     * @param AppTable $table The table object in which to uèdate the object
     * @param object $object The object to update
     * @param string $key The name of the primary key of the table
     * @param boolean $updatenulls If the null properties should be updated (default: true)
     *
     * @return boolean If the operation was successful
     *
     * @throws \RuntimeException
     */
    public function updateObject ($table, &$object, $key, $updatenulls = true)
    {

        // update object
        return $this->_database->updateObject($table, $object, $key, $updatenulls);

    }

    /**
     * Fetch a single row from a query result resource
     *
     * @param resource $result The resource pointing to the query result
     *
     * @return mixed The first row of the result
     */
    public function fetchRow ($result)
    {

        if ($this->name == 'mysqli') {
            return mysqli_fetch_row($result);
        }

        return mysql_fetch_row($result);
    }

    /**
     * Fetch an array of rows from a query result resource
     *
     * @param resource $result The resource pointing to the query result
     * @param constant $type The fetching type to be used (default: MYSQL_BOTH)
     *
     * @return array The list of rows
     */
    public function fetchArray ($result, $type = MYSQL_BOTH)
    {

        if ($this->name == 'mysqli') {
            return mysqli_fetch_array($result, $type);
        }

        return mysql_fetch_array($result, $type);
    }

    /**
     * Fetch a single object from a query result resource
     *
     * @param resource $result The resource pointing to the query result
     * @param string $class The class to be used to build the object
     *
     * @return object The first row of the result as an object
     */
    public function fetchObject ($result, $class = 'stdClass')
    {

        if ($this->name == 'mysqli') {
            return $class != 'stdClass' ? mysqli_fetch_object($result, $class) : mysqli_fetch_object($result);
        }

        return $class != 'stdClass' ? mysql_fetch_object($result, $class) : mysql_fetch_object($result);
    }

    /**
     * Free the memory from the query result
     *
     * @param resource $result The resource pointing to the query result
     *
     * @return boolean If the operation was successful
     */
    public function freeResult ($result)
    {

        if ($this->app->system->config->get('debug', 0)) {
            return;
        }

        if ($this->name == 'mysqli') {
            return mysqli_free_result($result);
        }

        return mysql_free_result($result);
    }

    /**
     * Magic method to map any other method to the JDatabase class
     *
     * @param string $method The method name
     * @param array $args The list of arguments to pass on to the JDatabase class
     *
     * @return mixed The result of the method
     */
    public function __call ($method, $args)
    {
        return call_user_func_array(array($this->_database, $method), $args);
    }

    /**
     * Quick utility method to replace the mysql prefix used in the joomla tables with the real prefix
     *
     * @param string $sql The sql query
     * @param string $prefix The prefix to search for (default: #__)
     *
     * @return string The query with the real prefix
     */
    public function replacePrefix ($sql, $prefix = '#__')
    {
        return preg_replace('/' . preg_quote($prefix) . '/', $this->_database->getPrefix(), $sql);
    }

}
