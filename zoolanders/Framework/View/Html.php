<?php

namespace Zoolanders\Framework\View;

use Illuminate\Contracts\Support\Arrayable;
use Zoolanders\Framework\Event\Dispatcher;
use Zoolanders\Framework\Event\View\GetTemplatePath;
use Zoolanders\Framework\Request\Request;
use Zoolanders\Framework\Service\Assets\Css;
use Zoolanders\Framework\Service\Assets\Js;
use Zoolanders\Framework\Service\System;

/**
 * Class Html
 * @package Zoolanders\Framework\View
 */
class Html extends View
{
    /**
     * @var string
     */
    protected $type = 'html';

    /**
     * Layout name
     *
     * @var    string
     */
    protected $layout;

    /**
     * List of paths where to find templates
     *
     * @var array
     */
    protected $templatePaths = [];

    /**
     * @var Asset mgr
     */
    public $document;

    /**
     * HtmlView constructor.
     */
    public function __construct(Dispatcher $event, System $system, System\Document $document, Request $request)
    {
        parent::__construct($event);

        $this->system = $system;

        $this->document = $document;

        $this->layout = $request->getCmd('task', 'default');

        $this->addTemplatePath(JPATH_COMPONENT . '/View/' . ucfirst($this->getName()) . '/tmpl');
    }

    /**
     * Sets an entire array of search paths for templates or resources.
     *
     * @param   mixed $path The new search path, or an array of search paths.  If null or false, resets to the current
     *                      directory only.
     *
     * @return  void
     */
    protected function setTemplatePath($path)
    {
        // Clear out the prior search dirs
        $this->templatePaths = array();

        // Actually add the user-specified directories
        $this->addTemplatePath($path);

        // Set the alternative template search dir
        $templatePath = JPATH_THEMES;
        $fallback = $templatePath . '/' . $this->system->getTemplate() . '/html/com_zoolanders/' . $this->getName();
        $this->addTemplatePath($fallback);

        $this->triggerEvent(new GetTemplatePath($this));
    }

    /**
     * Adds to the search path for templates and resources.
     *
     * @param   mixed $path The directory or stream, or an array of either, to search.
     *
     * @return  void
     */
    public function addTemplatePath($path)
    {
        // Just force to array
        settype($path, 'array');

        // Loop through the path directories
        foreach ($path as $dir) {
            // No surrounding spaces allowed!
            $dir = trim($dir);

            // Add trailing separators as needed
            if (substr($dir, -1) != DIRECTORY_SEPARATOR) {
                // Directory
                $dir .= DIRECTORY_SEPARATOR;
            }

            // Add to the top of the search dirs
            array_unshift($this->templatePaths, $dir);
        }
    }

    /**
     * Escapes a value for output in a view script.
     *
     * @param   mixed $var The output to escape.
     *
     * @return  mixed  The escaped value.
     */
    public function escape($var)
    {
        return htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
    }

    /**
     * Get the layout.
     *
     * @return  string  The layout name
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Sets the layout name to use
     *
     * @param   string $layout The layout name or a string in format <template>:<layout file>
     *
     * @return  $this
     */
    public function setLayout($layout)
    {
        if (is_null($layout)) {
            $layout = $this->getLayout();
        }

        if (strpos($layout, ':') === false) {
            $this->layout = $layout;
        } else {
            // Convert parameter to array based on :
            $temp = explode(':', $layout);
            $this->layout = $temp[1];

            // Set layout template
            $this->layoutTemplate = $temp[0];
        }

        return $this;
    }

    /**
     * Loads a template given any path.
     *
     * @param string $tpl The layout name
     *
     * @return  string  The output of the template
     *
     * @throws  \Exception  When the layout file is not found
     */
    public function loadTemplate($tpl = null)
    {
        $tpl = empty($tpl) ? $this->getLayout() : $tpl;

        ob_start();

        // Extract forced parameters
        if (!empty($this->data)) {
            if ($this->data instanceof Arrayable) {
                extract($this->data->toArray());
            } else {
                extract($this->data);
            }
        }

        // @TODO: Add fallback to default if assumed tpl does not exist
        include($this->templatePaths[0] . $tpl . '.php');

        return ob_get_clean();
    }

    /**
     * @param null $tpl
     * @param array $data
     * @return string
     */
    public function render($data = [])
    {
        if(!empty($data)){
            $this->data = $data;
        }

        $templateResult = $this->loadTemplate();

        return $templateResult;
    }
}
