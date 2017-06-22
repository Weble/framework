<?php

namespace Zoolanders\Framework\Item;

use Zoolanders\Framework\Container\Container;

class Indexer {


    /**
     * @var \Zoolanders\Framework\Element\Indexer
     */
    protected $elementIndexer;

    /**
     * @var \Zoolanders\Framework\Service\Database
     */
    protected $db;

    /**
     * Indexer constructor.
     * @param \Zoolanders\Framework\Service\Database $db
     * @param \Zoolanders\Framework\Element\Indexer $elementIndexer
     */
    public function __construct (\Zoolanders\Framework\Service\Database $db, \Zoolanders\Framework\Element\Indexer $elementIndexer) {
        $this->db = $db;
        $this->elementIndexer = $elementIndexer;
    }

    /**
     * @param \Item $item
     */
    public function index (\Item $item) {
        $this->cleanSearchIndex($item);

        $dataToSave = [];

        /** @var \Element $element * */
        foreach ($item->getElements() as $element) {
            $dataType = $this->elementIndexer->getDataTypeFromElement($element);
            $values = $this->elementIndexer->getValuesFromElement($element);

            $dataToSave[$dataType][$element->identifier] = $values;
        }

        foreach ($dataToSave as $dataType => $values) {
            // save into the right table
            $table = '#__zoo_zl_search_' . $dataType;

            /** @var \JDatabaseQuery $query */
            $query = $this->db->getQuery(true);

            $dataValues = [];
            foreach ($values as $element_id => $vs) {
                foreach ($vs as $v) {
                    $dataValues[] = implode(",", [$this->db->q($item->id), $this->db->q($element_id), $this->db->q($v)]);
                }
            }

            $query->insert($table)->columns(['item_id', 'element_id', 'value'])->values($dataValues);
            $this->db->setQuery($query);
            $this->db->execute();
        }
    }

    /**
     * @param \Item $item
     * @param array|null $dataTypes
     */
    public function cleanSearchIndex (\Item $item, $dataTypes = null) {
        if (!$dataTypes) {
            $dataTypes = \Zoolanders\Framework\Element\Indexer::getAvailableDataTypes();
        }

        foreach ($dataTypes as $dataType) {

            $table = '#__zoo_zl_search_' . $dataType;

            /** @var \JDatabaseQuery $query */
            $query = $this->db->getQuery(true);
            $query->delete()->from($table)->where('item_id = ' . (int)$item->id);
            $this->db->setQuery($query);
            $this->db->execute();
        }
    }
}
