<?php

namespace Zoolanders\Framework\Model\Database;

use Zoolanders\Framework\Collection\Resources;

/**
 * Class UniqueAlias
 * Contains tools to operate with aliases safely:
 * Check if alias exists, generate unique alias (for copies, etc).
 *
 * @package Zoolanders\Framework\Model\Database
 */
trait UniqueAlias
{
    /**
     * @var string  Alias column name (e.g. "alias", "slug")
     */
    protected $alias_column = 'alias';

    /**
     * Check if record with provided alias already exists in the DB table
     *
     * @param $alias
     *
     * @return bool
     */
    public function aliasExists($alias){

        $query = $this->db->getQuery(true);
        $query  ->select('*')
                ->from($query->qn($this->tableName))
                ->where([$query->qn($this->alias_column). '='. $query->q($alias)]);

        $records = Resources::make($this->db->queryObjectList($query));

        return (0 < $records->count());
    }

    /**
     * Builds safe alias for provided one
     *
     * @param   $alias
     *
     * @return  array alias, copy_number
     */
    public function generateAlias($alias){
        $copy_tail_pattern = '/(-copy)+(-(\d+))*$/';
        $start_index = 0;

        if(preg_match($copy_tail_pattern, $alias, $matches)){
            $start_index = isset($matches[3]) ? 1+(int)$matches[3] : 1;
            $alias = preg_replace('/' . $matches[0] . '$/', '-copy', $alias);
        } else {
            $alias = $alias . '-copy';
        }

        do{
            $safe_alias = $alias . ($start_index ? '-'.$start_index : '');
            $exists = $this->aliasExists($safe_alias);
            $start_index++;
        }while($exists);

        return $safe_alias;
    }
}
