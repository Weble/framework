<?php

namespace Zoolanders\Framework\Controller;

use Zoolanders\Framework\Container\Container;
use Zoolanders\Framework\Event\Triggerable;
use Zoolanders\Framework\Response\ResponseInterface;
use Zoolanders\Framework\Utils\NameFromClass;
use Zoolanders\Framework\Response\Response;
use Zoolanders\Framework\View\ViewInterface;

/**
 * Class Controller
 * Inspired by FOF3 Controller class by Nicholas K. Dionysopoulos / Akeeba Ltd (https://github.com/akeeba/fof/)
 */
class Controller
{
    use Triggerable, NameFromClass;

    /**
     * defaultTask
     *
     * @var    string
     */
    protected $defaultTask = 'index';

    /**
     * The current view name; you can override it in the configuration
     *
     * @var string
     */
    protected $view = '';

    /**
     * The current layout; you can override it in the configuration
     *
     * @var string
     */
    protected $layout = null;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->registerDefaultTask($this->defaultTask ? $this->defaultTask : 'index');
    }

    /**
     * @return string
     */
    public function getDefaultTask()
    {
        return $this->defaultTask;
    }

    /**
     * Default task. Assigns a model to the view and asks the view to render
     * itself.
     *
     * @param   string $tpl The name of the template file to parse
     *
     * @return  ResponseInterface
     */
    public function display($tpl = null)
    {
        $layout = $tpl ? $tpl : $this->getName();

        // Set the layout
        if (!is_null($this->layout)) {
            $layout .= ':' . $this->layout;
        }

        return $this->render($layout);
    }

    /**
     * Register the default task to perform if a mapping is not found.
     *
     * @param   string $method The name of the method in the derived class to perform if a named task is not found.
     *
     * @return  Controller  This object to support chaining.
     */
    public function registerDefaultTask($method)
    {
        $this->defaultTask = $method;

        return $this;
    }

    /**
     * Render view with provided data
     *
     * @param   string  Layout name: "viewname:tmplname"
     * @param   mixed   Playload data
     *
     * @return  Response
     */
    protected function render($layout, $data = array())
    {
        $buffer = explode(":", $layout);
        $viewName = array_shift($buffer);
        $tplName = @array_shift($buffer);

        $viewName = empty($viewName) ? $this->getName() : $viewName;
        $response = $this->container->factory->response($this->input);

        $view = $this->getView($viewName);
        $response->setContent($view->display($tplName, $data));

        return $response;
    }
}
