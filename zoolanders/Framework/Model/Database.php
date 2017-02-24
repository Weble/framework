<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Model;

use Zoolanders\Framework\Collection\Resources;
use Zoolanders\Framework\Model\Database\Date;
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
    protected $entity_class = 'stdClass';

    /**
     * @var \Zoolanders\Framework\Service\Database
     */
    protected $db;

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
     * Database constructor.
     * @param \Zoolanders\Framework\Service\Database service
     */
    public function __construct(\Zoolanders\Framework\Service\Database $db)
    {
        parent::__construct();

        $this->db = $db;
        $this->query = $this->db->getQuery(true);
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

        return $query;
    }

    /**
     * Execute the query as a "select" statement.
     * @return Resources
     */
    public function get()
    {
        $query = $this->buildQuery();
        $models = $this->db->queryObjectList($query);

        return Resources::make($models);
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
        $value = $this->query->q($value);

        switch (strtolower($operator)) {
            case 'in':
                settype($value, 'array');
                $value = '(' . implode(",", $value) . ')';
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
    public function find($key){

        $this->where($this->primary_key, '=', $key);
        $this->buildQuery();

        $record = $this->db->queryObject($this->query, $this->entity_class);

        return $record;
    }

    /**
     * Delete record by key
     *
     * @param $key
     *
     * @return bool
     */
    public function delete($key){

        $query = $this->database->getQuery(true);

        $query->delete()
            ->from($query->qn($this->tableName))
            ->where([$query->qn($this->primary_key) . '=' . $query->escape($key)]);

        return $this->db->query($query);
    }
}
