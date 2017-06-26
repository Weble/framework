<?php
/**
 * Base stuff for the FOF3 Model
 * @package     FOF
 * @copyright   2010-2016 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 *
 * Refactored by ZOOlanders
 */

namespace Zoolanders\Framework\Model;

use Zoolanders\Framework\Data\Json;
use Zoolanders\Framework\Event\Triggerable;
use Zoolanders\Framework\Utils\NameFromClass;

defined('_JEXEC') or die;

class Model
{
    use Triggerable, NameFromClass;

    /**
     * A state object
     *
     * @var    string
     */
    protected $state;

    /**
     * Model constructor.
     */
    public function __construct()
    {
        $this->getName();
        $this->state = new Json();
    }

    /**
     * Get a filtered state variable
     *
     * @param   string $key The state variable's name
     * @param   mixed $default The default value to return if it's not already set
     * @param   string $filter_type The filter type to use
     *
     * @return  mixed  The state variable's contents
     */
    public function getState($key = null, $default = null, $filter_type = 'raw')
    {
        if (empty($key)) {
            return $this->state;
        }

        $value = $this->state->get($key, $default);

        if (strtoupper($filter_type) == 'RAW') {
            return $value;
        }

        $filter = new \JFilterInput();
        return $filter->clean($value, $filter_type);
    }

    /**
     * Method to set model state variables
     *
     * @param   string $property The name of the property.
     * @param   mixed $value The value of the property to set or null.
     *
     * @return  mixed  The previous value of the property or null if not set.
     */
    public function setState($property, $value = null)
    {
        $this->state->set($property, $value);
        return $this;
    }

    /**
     * Clears the model state, but doesn't touch the internal lists of records,
     * record tables or record id variables. To clear these values, please use
     * reset().
     *
     * @return  static
     */
    public function clearState()
    {
        $this->state = new Json();

        return $this;
    }

    /**
     * Magic getter; allows to use the name of model state keys as properties. Also handles magic properties:
     * $this->input  mapped to $this->container->input
     *
     * @param   string $name The state variable key
     *
     * @return  static
     */
    public function __get($name)
    {
        return $this->getState($name);
    }

    /**
     * Magic setter; allows to use the name of model state keys as properties
     *
     * @param   string $name The state variable key
     * @param   mixed $value The state variable value
     *
     * @return  static
     */
    public function __set($name, $value)
    {
        return $this->setState($name, $value);
    }

    /**
     * Magic caller; allows to use the name of model state keys as methods to
     * set their values.
     *
     * @param   string $name The state variable key
     * @param   mixed $arguments The state variable contents
     *
     * @return  static
     */
    public function __call($name, $arguments)
    {
        // filterAt, filterPublished, filterWhatever
        $filter = 'filter' . ucfirst($name);
        if (method_exists($this, $filter)) {
            call_user_func_array([$this, $filter], $arguments);
            return $this;
        }

        $arg1 = array_shift($arguments);
        $this->setState($name, $arg1);

        return $this;
    }
}
