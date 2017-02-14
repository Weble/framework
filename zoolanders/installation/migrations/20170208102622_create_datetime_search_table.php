<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */


use Phinx\Migration\AbstractMigration;

class CreateDatetimeSearchTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('search_datetime');

        if (!$table->exists()) {
            $table
                ->addColumn('item_id', 'integer', ['null' => false])
                ->addColumn('element_id', 'string', ['null' => false])
                ->addColumn('value', 'datetime', ['null' => true]);

            $table
                ->addIndex(['item_id', 'element_id', 'value']);

            $table->create();
        }
    }
}
