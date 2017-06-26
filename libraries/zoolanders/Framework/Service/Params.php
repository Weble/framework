<?php

namespace Zoolanders\Framework\Service;

use Joomla\Registry\Registry;
use Zoolanders\Framework\Service\System\Dbo;

defined('_JEXEC') or die;

/**
 * A helper class to quickly get the component parameters
 */
class Params
{
    /**
     * Params indexed by component
     *
     * @var array of \Joomla\Registry\Registry
     */
    private $params = [];

    /**
     * Public constructor for the params object
     */
    public function __construct (Dbo $db, Registry $params, Data $data)
    {
        $this->db = $db;
        $this->params = $params;
        $this->data = $data;

        $this->reload();
    }

    /**
     * Reload the params
     */
    public function reload ($component = 'com_zoo')
    {
        $db = $this->db;

        $sql = $db->getQuery(true);
        $sql->select($db->qn('params'))
            ->from($db->qn('#__extensions'))
            ->where($db->qn('type') . " = " . $db->q('component'))
            ->where($db->qn('element') . " = " . $db->q($component));

        $json = $db->setQuery($sql)->loadResult();

        $this->params[$component] = $this->data->create($json, 'parameter');
    }

    /**
     * Returns the value of a component configuration parameter
     *
     * @param   string $key The parameter to get
     * @param   mixed $default Default value
     *
     * @return  mixed
     */
    public function get ($key, $default = null, $component = 'com_zoo')
    {
        if (!isset($this->params[$component])) {
            $this->reload($component);
        }

        return $this->data->create($this->params[$component])->get($key, $default);
    }

    /**
     * Returns a copy of the loaded component parameters as an array
     *
     * @return  array
     */
    public function getParams ($component = 'com_zoo')
    {
        if (!isset($this->params[$component])) {
            $this->reload($component);
        }

        return $this->params->toArray();
    }

    /**
     * Sets the value of a component configuration parameter
     *
     * @param   string $key The parameter to set
     * @param   mixed $value The value to set
     *
     * @return  void
     */
    public function set ($key, $value, $component = 'com_zoo')
    {
        $this->setParams(array($key => $value), $component);
    }

    /**
     * Sets the value of multiple component configuration parameters at once
     *
     * @param   array $params The parameters to set
     *
     * @return  void
     */
    public function setParams (array $params, $component = 'com_zoo')
    {
        if (!isset($this->params[$component])) {
            $this->reload($component);
        }

        foreach ($params as $key => $value) {
            $this->params[$component]->set($key, $value);
        }
    }

    /**
     * Actually Save the params into the db
     */
    public function save ()
    {
        $db = $this->db;
        foreach ($this->params as $component => $params) {
            $data = (string)$params;

            $sql = $db->getQuery(true)
                ->update($db->qn('#__extensions'))
                ->set($db->qn('params') . ' = ' . $db->q($data))
                ->where($db->qn('element') . ' = ' . $db->q($component))
                ->where($db->qn('type') . ' = ' . $db->q('component'));

            $db->setQuery($sql);

            try {
                $db->execute();
            } catch (\Exception $e) {
                // Don't sweat if it fails
            }
        }
    }
}
