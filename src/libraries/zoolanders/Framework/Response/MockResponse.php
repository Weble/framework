<?php

namespace Zoolanders\Framework\Response;

use Zoolanders\Framework\Data\Data;
use Zoolanders\Framework\Response\ResponseInterface;

class MockResponse implements ResponseInterface
{
    protected $data;
    protected $headers;


    public function __construct ()
    {
        $this->data = new Data();
        $this->headers = new Data();
    }

    public function getContent ()
    {
        return $this->data;
    }

    public function getHeader ($key)
    {
        return $this->headers->get($key);
    }

    public function getHeaders ()
    {
        return $this->headers;
    }

    public function send ()
    {
        return $this->data;
    }

    public function setContent ($content)
    {
        $this->data = $content;
    }

    /**
     * @param Data $data
     */
    public function setData ($data)
    {
        $this->data = $data;
    }

    /**
     * @param Data $headers
     */
    public function setHeaders ($headers)
    {
        $this->headers = $headers;
    }

    public function setHeader ($key, $value)
    {
        $this->headers->set($key, $value);
    }
}
