<?php

namespace ZFTests\Classes;


trait DBUtils
{
    /**
     * Build sql line ready to be executed
     *
     * @param $tablename
     * @param $params
     *
     * @return string
     */
    private function buildMatchQuery($tablename, $params){
        $sql = "SELECT * FROM #__$tablename ";
        if(!empty($params)){
            $sql .= "WHERE ";
            $tail = [];
            foreach($params as $column => $value){
                $tail[] = sprintf("`%s`=%s", $column, self::wrapParam($value));
            }
            $sql .= implode(' AND ', $tail);
        }

        return $sql;
    }

    /**
     * Wrap sql value in quotes or left as is, depending on type
     *
     * @param $value
     * @return string
     */
    private static function wrapParam($value){

        if(is_string($value)){
            $db = self::$container->db;
            $value = "'".$db->escape($value)."'";
        }

        return $value;
    }
}
