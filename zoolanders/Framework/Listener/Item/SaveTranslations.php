<?php

namespace Zoolanders\Framework\Listener\Item;

use Zoolanders\Framework\Event\Item;
use Zoolanders\Framework\Listener\Listener;
use Zoolanders\Framework\Service\Database;

class SaveTranslations extends Listener
{
    /**
     * @var Database
     */
    protected $db;
    /**
     * @var Indexer
     */
    protected $indexer;

    /**
     * SearchValues constructor.
     */
    function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param Item\Saved $event
     */
    public function handle(Item\Saved $event)
    {
        $item = $event->getItem();
        $currentLanguage = \JFactory::getLanguage()->getTag();

        $values = [];

        $languages = array_keys(\JFactory::getLanguage()->getKnownLanguages(JPATH_SITE));
        foreach ($languages as $language) {
            $values[$language] = [
                'item_id' => $item->id,
                'language' => $language,
                'name' => '',
                'alias' => '',
                'enabled' => 1
            ];
        }

        $values[$currentLanguage]['name'] = $item->name;
        $values[$currentLanguage]['alias'] = $item->alias;

        $params = $item->getParams();

        $this->setTranslationFromParams($values, $params, 'content.name_translation', 'name');
        $this->setTranslationFromParams($values, $params, 'content.alias_translation', 'alias');

        // Enabled for this language?
        $enabledLanguages = $params->get('content.language', array());

        // Empty list => all enabled
        if (!empty($enabledLanguages)) {
            foreach ($languages as $language) {
                if (!in_array($language, $enabledLanguages)) {
                    $values[$language]['enabled'] = 0;
                }
            }
        }

        $db = $this->db;

        foreach ($values as &$value) {
            $value = implode(",", $db->q($value));
        }

        // Clean
        /** @var \JDatabaseQuery $query */
        $query = $db->getQuery(true);
        $query->delete()->from('#__zoo_zl_item_languages')->where('item_id = ' . (int)$item->id);
        $db->setQuery($query);
        $db->execute();

        // Insert
        $query->insert('#__zoo_zl_item_languages')->columns(['item_id', 'language', 'name', 'alias', 'enabled'])->values(array_values($values));
        $db->setQuery($query);
        $db->execute();
    }

    /**
     * @param $values
     * @param $params
     * @return array
     */
    protected function setTranslationFromParams(&$values, $params, $param, $key)
    {
        $translations = $params->get($param, array());
        foreach ($translations as $language => $translation) {
            if ($translation) {
                $values[$language][$key] = $translation;
            }
        }
    }
}
