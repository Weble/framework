<?php

namespace Zoolanders\Framework\Request;

use Zoolanders\Framework\Data\Data;
use Zoolanders\Framework\Response\ResponseInterface;

class MockRequest implements RequestInterface
{
    protected $data;
    protected $headers;

    public function __construct ()
    {
        $this->data = new Data();
        $this->headers = [];
    }

    public function get ($name, $default = null, $filter = 'cmd')
    {
        return $this->data->get($name, $default);
    }

    public function expectsJson ()
    {
        return false;
    }

    public function getExpectedResponse ()
    {
        return ResponseInterface::TYPE_HTML;
    }

    public function getHeaders ()
    {
        return $this->headers;
    }

    public function getHttpVerb ()
    {
        return 'GET';
    }

    public function isAjax ()
    {
        return false;
    }

    public function isJson ()
    {
        return false;
    }

    /**
     * Sets a value
     *
     * @param   string  $name   Name of the value to set.
     * @param   mixed   $value  Value to assign to the input.
     *
     * @return  void
     */
    public function set($name, $value)
    {
        $this->data->set($name, $value);
    }

    function __call ($name, $arguments)
    {
        if (substr($name, 0, 3) == 'get') {
            return call_user_func_array([$this, 'get'], $arguments);
        }
    }
}
