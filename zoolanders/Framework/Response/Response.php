<?php

namespace Zoolanders\Framework\Response;

use JHttpResponse;

/**
 * Class JsonResponse
 * HTTP Response helper
 */
class Response extends JHttpResponse implements ResponseInterface
{
    /**
     * @var string Data
     */
    public $data = null;

    /**
     * @var string  Content type
     */
    public $type = 'text/html';

    /**
     * @var array   Used HTTP states codes
     */
    protected static $status_codes = array(
        200 => 'OK',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        500 => 'Internal Server Error'
    );

    /**
     * Response constructor
     *
     * @param   int $code
     * @param   $data
     */
    public function __construct($data = '', $code = 200)
    {
        $this->code = $code;
        $this->data = $data;
    }

    /**
     * Set response header
     *
     * @param   $key
     * @param   $value
     *
     * @return  Response
     */
    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * Send HTTP headers
     *
     * @return void
     */
    protected function sendHeaders()
    {
        header($_SERVER["SERVER_PROTOCOL"] . " $this->code " . @self::$status_codes[$this->code]);
        $this->setHeader('Content-Type', $this->type);

        if (!empty($this->headers)) {
            foreach ($this->headers as $key => $value) {
                header(sprintf("%s: %s", $key, $value));
            }
        }
    }

    /**
     * Set content
     *
     * @param   $content
     * @return  Response
     */
    public function setContent($content)
    {
        $this->data = $content;
        return $this;
    }

    /**
     * Send content to the client
     *
     * @return void
     */
    protected function sendContent()
    {
        if (!empty($this->data)) {
            echo $this->data;
        } else if (@self::$status_codes[$this->code]) {
            echo @self::$status_codes[$this->code];
        }
    }

    /**
     * Set a root value
     *
     * @param $varname
     * @param $value
     *
     * @return object
     */
    public function set($varname, $value)
    {
        $this->{$varname} = $value;
        return $this;
    }

    /**
     * Send prepared response to user agent
     *
     * @return  mixed
     */
    public function send()
    {
        $this->sendHeaders();
        $this->sendContent();

        exit();
    }
}
