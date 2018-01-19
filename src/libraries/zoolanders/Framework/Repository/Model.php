<?php

namespace Zoolanders\Framework\Repository;

use Zoolanders\Framework\Service\Zoo;

/**
 * Class TableRepository
 * Base table repository for any repository that needs to use the old ZOO table classes
 * @package Zoolanders\Repository
 */
abstract class Model extends Database
{
    /**
     * @var Zoo
     */
    protected $app;

    /**
     * @var \AppTable
     */
    protected $table;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * Table constructor.
     * @param string $tableName
     * @param string $className
     */
    public function __construct ($tableName, $className)
    {
        parent::__construct();

        $this->tableName = $tableName;
        $this->className = $className;

        $this->app = $this->container->zoo->getApp();
        $this->table = $this->app->table->$tableName;
    }

    public function create (array $attributes)
    {
        $object = $this->app->object->create($this->className);

        foreach ($attributes as $k => $v) {
            $object->$k = $v;
        }

        $this->table->save($object);

        return $object;
    }

    public function all ()
    {
        return $this->table->all();
    }

    public function get ($id)
    {
        return $this->table->get($id);
    }

    public function delete ($ids)
    {
        settype($ids, 'array');

        foreach ($ids as $id) {
            $object = $this->get($id);
            $this->table->delete($object);
        }
    }
}
