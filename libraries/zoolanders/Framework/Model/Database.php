<?php

namespace Zoolanders\Framework\Model;

use Zoolanders\Framework\Collection\Collection;
use Zoolanders\Framework\Collection\Resources;
use Zoolanders\Framework\Data\Json;
use Zoolanders\Framework\Model\Database\Date;
use Zoolanders\Framework\Service\Zoo;
use Zoolanders\Framework\Utils\IsString;

defined('_JEXEC') or die;

abstract class Database extends Model
{
    use Date, IsString;

    /**
     * @var string Primary key column name
     */
    protected $primary_key = 'id';

    /**
     * @var string  Entity class name
     */
    protected $entityClass = 'stdClass';

    /**
     * @var \Zoolanders\Framework\Service\Database
     */
    protected $db;

    /**
     * @var Zoo
     */
    protected $zoo;

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var string
     */
    protected $tablePrefix = '';

    /**
     * @var string
     */
    protected $tableName = '';

    /**
     * @var string
     */
    protected $tableClassName = '';

    /**
     * @var \AppTable
     */
    protected $table;

    /**
     * @var array
     */
    protected $cast = [];

    /**
     * @var \JDatabaseQuery
     */
    protected $query;

    /**
     * List of where clauses to be build into an AND clause
     * @var array
     */
    protected $wheres = [];

    /**
     * List of where clauses to build into an OR clause
     * @var array
     */
    protected $orWheres = [];

    /**
     * List of tables to join (grouped by join type) with their desired aliases, if needed
     * @var array
     */
    protected $joins = [

    ];

    /**
     * Sort Ordering options e.g. ['name' => 'asc']
     *
     * @var array
     */
    protected $ordering = [

    ];

    /**
     * Database constructor.
     * @param \Zoolanders\Framework\Service\Database service
     */
    public function __construct(\Zoolanders\Framework\Service\Database $db, Zoo $zoo)
    {
        parent::__construct();

        $this->db = $db;
        $this->zoo = $zoo;
        $this->query = $this->db->getQuery(true);

        $this->zoo = $zoo;

        $this->table = $zoo->table->{$this->tableClassName};
    }

    /**
     * @return \AppTable
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Get list of columns to be selected
     */
    protected function getColumns()
    {
        return empty($this->columns) ? [$this->getPrefix() . '*'] : $this->columns;
    }

    /**
     * @param array $fields
     * @param null $prefix
     */
    public function fields($fields = ['*'], $prefix = null)
    {
        $prefix = $prefix ? $prefix : $this->tablePrefix;

        $fields = array_map(function ($item) use ($prefix) {
            $cell = '';
            if ($prefix) {
                $cell .= $this->getPrefix();
            }
            return $cell . ($item !== '*' ? $this->query->qn($item) : $item);
        }, $fields);

        $this->columns = array_merge($this->columns, $fields);
        $this->columns = array_unique($this->columns);
    }

    /**
     * @return \JDatabaseQuery
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return \JDatabaseQuery
     */
    public function buildQuery()
    {
        $query = $this->getQuery();

        // Prefix the table if necessary
        $prefix = (strlen($this->tablePrefix) > 0) ? ' AS ' . $this->query->qn($this->tablePrefix) : '';

        // Autoperform SELECT AND FROM statement
        $query->select($this->getColumns());
        $query->from($this->query->qn($this->tableName) . $prefix);


        if (count($this->joins)) {
            foreach ($this->joins as $type => $tables) {
                foreach ($tables as $sql) {
                    $query->join($type, $sql);
                }
            }
        }

        // At the end, merge ORs
        if (count($this->orWheres)) {
            $query->where('(' . implode(' OR ', $this->orWheres) . ')');
        }

        // and the ANDs
        foreach ($this->wheres as $where) {
            $query->where($where);
        }

        if (!empty($this->ordering)) {
            $order_columns = [];
            foreach ($this->ordering as $column => $order) {
                $order_columns[] = $this->getPrefix() . $this->query->qn($column) . ' ' . $order;
            }
            $query->order($order_columns);
        }

        return $query;
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @return Resources
     */
    public function get()
    {
        $query = $this->buildQuery();

        $models = $this->db->queryObjectList($query, $this->primary_key, $this->entityClass, $this->getState('offset', 0), $this->getState('limit', 0));

        foreach ($models as &$model) {
            $model = $this->castAttributes($model);
        }

        return Resources::make($models);
    }

    /**
     * @param $item
     * @return mixed
     */
    protected function castAttributes($item)
    {
        foreach ($item as $key => &$value) {
            $value = $this->castAttribute($key, $value);
        }

        return $item;
    }

    /**
     * @param $object
     * @param $data
     * @return bool
     */
    public function bind($object, $data)
    {
        if (!is_array($data)) {
            return $object;
        }

        foreach ($data as $key => $value) {
            $object->$key = $value;
        }

        return $object;
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    protected function castAttribute($key, $value)
    {
        $columns = $this->getTable()->getTableColumns();
        $columns = array_merge($columns, $this->cast);

        if (is_null($value)) {
            return $value;
        }

        switch (@$columns[$key]) {
            case 'int':
            case 'integer':
            case 'int unsigned':
                return (int)$value;
            case 'real':
            case 'float':
            case 'double':
                return (float)$value;
            case 'string':
            case 'varchar':
            case 'text':
                return (string)$value;
            case 'bool':
            case 'boolean':
                return (bool)$value;
            case 'collection':
                return Collection::make($value);
            case 'json':
                return new Json($value);
            /** case 'date':
             * case 'datetime':
             * case 'timestamp':
             * return \Zoolanders\Framework\Service\Date::create($value);**/

            default:
                return $value;
        }
    }

    /**
     * @param $fieldOrCallable
     * @param $operator
     * @param $value
     * @return $this
     */
    public function where($fieldOrCallable, $operator, $value)
    {
        if (is_callable($fieldOrCallable)) {
            call_user_func_array($fieldOrCallable, [&$this->query]);
            return $this;
        }

        $this->setupOperatorAndValue($operator, $value);

        $this->wheres[] = $this->getPrefix() . $this->query->qn($fieldOrCallable) . " " . $operator . " " . $value;

        return $this;
    }

    /**
     * @param $fieldOrCallable
     * @param $operator
     * @param $value
     * @return $this
     */
    public function orWhere($fieldOrCallable, $operator, $value)
    {
        if (is_callable($fieldOrCallable)) {
            call_user_func_array($fieldOrCallable, [&$this->query]);
            return $this;
        }

        $this->orWheres[] = $this->getPrefix() . $this->query->qn($fieldOrCallable) . " " . $operator . " " . $this->query->q($value);

        return $this;
    }

    /**
     * @param $field
     * @param $from
     * @param $to
     * @return $this
     */
    public function whereBetween($field, $from, $to)
    {
        $this->wheres[] = $this->getPrefix() . $this->query->qn($field) . " BETWEEN " . $this->query->q($from) . " AND " . $this->query->q($to);

        return $this;
    }

    /**
     * @param $field
     * @param $from
     * @param $to
     * @return $this
     */
    public function orWhereBetween($field, $from, $to)
    {
        $this->orWheres[] = $this->getPrefix() . $this->query->qn($field) . " BETWEEN " . $this->query->q($from) . " AND " . $this->query->q($to);

        return $this;
    }

    /**
     * @param $field
     * @param $operator
     * @param $value
     * @return $this
     */
    public function whereAny($field, $operator, $value)
    {
        if ($this->isString($value)) {
            $value = explode(" ", $value);
        }

        settype($value, 'array');

        $wheres = [];
        foreach ($value as $v) {
            $this->setupOperatorAndValue($operator, $v);
            $wheres[] = $this->getPrefix() . $this->query->qn($field) . " " . $operator . " " . $v;
        }

        $this->wheres[] = '(' . implode(" OR ", $wheres) . ')';

        return $this;
    }

    /**
     * @param $field
     * @param $operator
     * @param $value
     * @return $this
     */
    public function orWhereAny($field, $operator, $value)
    {
        if ($this->isString($value)) {
            $value = explode(" ", $value);
        }

        settype($value, 'array');

        $wheres = [];
        foreach ($value as $v) {
            $this->setupOperatorAndValue($operator, $v);
            $wheres[] = $this->getPrefix() . $this->query->qn($field) . " " . $operator . " " . $v;
        }

        $this->orWheres[] = '(' . implode(" OR ", $wheres) . ')';

        return $this;
    }

    /**
     * @param $sql
     * @return $this
     */
    public function whereRaw($sql)
    {
        $this->wheres[] = $sql;
        return $this;
    }

    /**
     * @param $sql
     * @return $this
     */
    public function orWhereRaw($sql)
    {
        $this->orWheres[] = $sql;
        return $this;
    }

    /**
     * Add prefix to the where statement given
     * @param $sql
     */
    public function wherePrefix($sql)
    {
        $this->wheres[] = $this->getPrefix() . $sql;
    }

    /**
     * @param $sql
     */
    public function orWherePrefix($sql)
    {
        $this->orWheres[] = $this->getPrefix() . $sql;
    }

    /**
     * Join a table with a defined alias
     * @param $table
     * @param bool $alias
     * @param $type
     */
    public function join($table, $condition, $alias = false, $type = 'LEFT')
    {
        $type = strtoupper($type);

        if (!$type) {
            $type = 'LEFT';
        }

        if (!isset($this->joins[$type])) {
            $this->joins[$type] = [];
        }

        $query = $this->getQuery();
        $table = $query->qn($table);

        if ($alias) {
            $table .= ' AS ' . $query->qn($alias);
        }

        $this->joins[$type][] = $table . ' ON ' . $condition;
    }

    /**
     * @param $ids
     * @return $this
     */
    public function filterIn($field, $ids)
    {
        settype($ids, 'array');

        if (count($ids)) {
            $this->wherePrefix($this->query->qn($field) . ' IN (' . implode(', ', $this->query->q($ids)) . ')');
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getTablePrefix()
    {
        return $this->tablePrefix;
    }

    /**
     * @param string $tablePrefix
     */
    public function setTablePrefix($tablePrefix)
    {
        $this->tablePrefix = $tablePrefix;
    }

    /**
     * @return string
     */
    protected function getPrefix()
    {
        $prefix = (isset($this->tablePrefix) && strlen($this->tablePrefix) > 0) ? $this->query->qn($this->tablePrefix) . '.' : '';
        return $prefix;
    }

    /**
     * @param $operator
     * @param $value
     */
    protected function setupOperatorAndValue(&$operator, &$value)
    {
        switch (strtolower($operator)) {
            case 'in':
                $value = $this->query->q($value);
                settype($value, 'array');
                $value = '(' . implode(",", $value) . ')';
                break;
            case 'like':
                $value = filter_var($value, FILTER_SANITIZE_STRING);
                $value = $this->query->q('%' . $value . '%');
                $value = "(" . $value . ")";
                break;
        }
    }

    /**
     * Find single record by id
     *
     * @param $key
     *
     * @return mixed
     */
    public function find($key)
    {
        $item = $this->table->get($key);

        return $this->castAttributes($item);
    }

    /**
     * Delete record by key
     *
     * @param $key
     *
     * @return bool
     */
    public function delete($key)
    {
        $query = $this->database->getQuery(true);

        $query->delete()
            ->from($query->qn($this->tableName))
            ->where([$query->qn($this->primary_key) . '=' . $query->escape($key)]);

        return $this->db->query($query);
    }

    /**
     * Save object data to db
     *
     * @param $record
     *
     * @return bool True on success
     */
    public function save($record)
    {
        $success = false;

        if ($record instanceof $this->entityClass) {
            $success = $this->table->save($record);
        }

        return $success;
    }

    /**
     * Set ordering options
     *
     * @param $ordering
     */
    public function orderBy($ordering)
    {
        $this->ordering = array_merge($this->ordering, (array)$ordering);
    }

    /**
     * Set pagination for the query
     *
     * @param $page
     * @param int $per_page
     *
     * @return self
     */
    public function paginate($page, $per_page)
    {

        $this->setState('limit', $per_page);
        $offset = ($page - 1) * $per_page;
        $this->setState('offset', ($offset >= 0) ? $offset : 0);

        return $this;
    }

    /**
     * Init the object adding the reference to the global app object
     *
     * @param object $object The object to init
     * @return object The object with an app property referencing the gobal app object
     */
    protected function initObject($object)
    {

        // add reference to related app instance
        if (property_exists($object, 'app')) {
            $object->app = $this->zoo->getApp();
        }

        return $object;
    }

    /**
     * Creates and returns a new entity instance
     *
     * @return mixed
     */
    public function createEntity()
    {

        return $this->zoo->object->create($this->entityClass);
    }
}
