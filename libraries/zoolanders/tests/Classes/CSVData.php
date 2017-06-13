<?php

namespace ZFTests\Classes;

/**
 * Class CSVData
 * Csv loadable datasets
 *
 * @package ZFTests\Classes
 */
trait CSVData
{
    /**
     * Load and parse CSV file with test cases
     *
     * @param $filePath
     * @return mixed
     * @throws \Exception
     */
    protected function loadTestDataCSV($filePath){
        if(file_exists($filePath)){
            $data = [];
            $content = file_get_contents($filePath);
            $strings = explode("\n", $content);
            if(!empty($strings)){
                foreach ($strings as $str){
                    $data[] = str_getcsv ( $str );
                }
            }
            return $data;
        } else {
            throw new \Exception('CSV file ['.$filePath.'] with test data not found');
        }
    }
}
