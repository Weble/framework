<?php

namespace Zoolanders\Framework\Request;

use Zoolanders\Framework\Data\Data;
use Zoolanders\Framework\Response\ResponseInterface;

/**
 * Class Request
 * @package Zoolanders\Framework\Request
 */
class Request extends \JInput implements RequestInterface
{
    /**
     * @var array|false Request headers
     */
    protected $headers;

    /**
     * @var boolean
     */
    protected $isJson;

    /**
     * Request constructor.
     */
    public function __construct ()
    {
        parent::__construct();

        // Capture HTTP Request headers:
        $this->getHeaders();

        $manager = new \Zoolanders\Framework\Migration\Manager();
        $manager->run();

        if ($this->isJson()) {
            $json = json_decode(@file_get_contents('php://input'), true);
            $this->data = array_merge($this->data, (array)$json);
        }
    }

    /**
     * Get http request headers
     *
     * @return Data
     */
    public function getHeaders ()
    {
        if (empty($this->headers)) {
            $headers = [];

            if (function_exists('getallheaders')) {
                $headers = getallheaders();
            } else {
                foreach ($_SERVER as $key => $value) {
                    if (substr($key, 0, 5) == "HTTP_") {
                        $key = str_replace(" ", "-", ucwords(strtolower(str_replace("_", " ", substr($key, 5)))));
                        $headers[$key] = $value;
                    } else {
                        $headers[$key] = $value;
                    }
                }
            }

            // add lowercase version
            foreach ($headers as $key => $value) {
                $headers[strtolower($key)] = $value;
            }

            $this->headers = new Data($headers);
        }

        return $this->headers;
    }

    /**
     * isAjax
     *
     * @return bool True if an ajax call is being made
     */
    public function isAjax ()
    {
        // Joomla way
        if (in_array($this->getCmd('format'), ['json', 'raw'])) {
            return true;
        }

        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    /**
     * if the request is in json format
     *
     * @return bool True if an ajax call is being made
     */
    public function isJson ()
    {
        // Accept json in the header
        if (strpos($this->getHeaders()->get('content-type'), 'application/json') !== false) {
            return true;
        }

        return false;
    }

    /**
     * if the request is in json format
     *
     * @return bool True if an ajax call is being made
     */
    public function expectsJson ()
    {
        return ($this->getExpectedResponse() == ResponseInterface::TYPE_JSON);
    }

    /**
     * get the expected response format
     * @return bool
     */
    public function getExpectedResponse ()
    {
        // Joomla way
        if (in_array($this->getCmd('format'), ['json', 'raw'])) {
            return ResponseInterface::TYPE_JSON;
        }

        // header based
        $acceptHeader = $this->getHeaders()->get('Accept');

        if (strpos($acceptHeader, ResponseInterface::TYPE_JSON) !== false) {
            return ResponseInterface::TYPE_JSON;
        }

        return ResponseInterface::TYPE_HTML;
    }

    /**
     * Determines the CRUD task to use based on the view name and HTTP verb used in the request.
     * @credits https://github.com/akeeba/fof/blob/development/fof/Controller/DataController.php
     *
     * @return  string  The CRUD task (browse, read, edit, delete)
     */
    public function getHttpVerb ()
    {
        // Get the request HTTP verb
        $requestMethod = 'GET';
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $requestMethod = strtoupper($_SERVER['REQUEST_METHOD']);
        }

        return $requestMethod;
    }
}
