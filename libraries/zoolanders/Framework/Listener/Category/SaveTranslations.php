<?php

namespace Zoolanders\Framework\Listener\Category;

use Zoolanders\Framework\Event\Category;
use Zoolanders\Framework\Listener\Listener;
use Zoolanders\Framework\Service\Database;

class SaveTranslations extends Listener {
    /**
     * @var Database
     */
    protected $db;

    /**
     * SaveTranslations constructor.
     * @param Database $db
     */
    function __construct (Database $db) {
        $this->db = $db;
    }

    /**
     * @param Category\Saved $event
     */
    public function handle (Category\Saved $event) {
        $category = $event->getCategory();
        $currentLanguage = \JFactory::getLanguage()->getTag();

        $values = [];

        $languages = array_keys(\JFactory::getLanguage()->getKnownLanguages(JPATH_SITE));
        foreach ($languages as $language) {
            $values[$language] = [
                'category_id' => $category->id,
                'language' => $language,
                'name' => '',
                'alias' => '',
                'enabled' => 1
            ];
        }

        $values[$currentLanguage]['name'] = $category->name;
        $values[$currentLanguage]['alias'] = $category->alias;

        $params = $category->getParams();

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
        $query->delete()->from('#__zoo_zl_category_languages')->where('category_id = ' . (int)$category->id);
        $db->setQuery($query);
        $db->execute();

        // Insert
        $query->insert('#__zoo_zl_category_languages')->columns(['category_id', 'language', 'name', 'alias', 'enabled'])->values($values);
        $db->setQuery($query);
        $db->execute();
    }

    /**
     * @param $values
     * @param $params
     * @return array
     */
    protected function setTranslationFromParams (&$values, $params, $param, $key) {
        $translations = $params->get($param, array());
        foreach ($translations as $language => $translation) {
            if ($translation) {
                $values[$language][$key] = $translation;
            }
        }
    }
}
