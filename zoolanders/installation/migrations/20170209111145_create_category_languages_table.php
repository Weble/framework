<?php

use Phinx\Migration\AbstractMigration;

class CreateCategoryLanguagesTable extends AbstractMigration
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
        $table = $this->table('category_languages');

        if (!$table->exists()) {
            $table
                ->addColumn('category_id', 'integer', ['null' => false])
                ->addColumn('language', 'string', ['null' => false])
                ->addColumn('name', 'string', ['null' => true])
                ->addColumn('alias', 'string', ['null' => true])
                ->addColumn('enabled', 'boolean', ['null' => false, 'default' => 1]);

            $table
                ->addIndex(['category_id', 'language'])
                ->addIndex(['category_id', 'language', 'enabled']);

            $table->create();
        }
    }
}
