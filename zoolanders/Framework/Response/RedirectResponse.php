<?php

namespace Zoolanders\Framework\Response;

/**
 * Class RedirectResponse
 * @package Zoolanders\Framework\Response
 */
class RedirectResponse extends Response
{
    /**
     * RedirectResponse constructor
     *
     * @param   $location
     * @param   int $code
     */
    public function __construct($location = '/', $code = 301)
    {
        $this->code = $code;
        $this->location = $location;
    }

    /**
     * @inheritdoc
     */
    protected function sendHeaders()
    {
        header($_SERVER["SERVER_PROTOCOL"] . " $this->code " . @self::$status_codes[$this->code]);
        $this->setHeader('Location', @$this->location);

        if (!empty($this->headers)) {
            foreach ($this->headers as $key => $value) {
                header(sprintf("%s: %s", $key, $value));
            }
        }
    }

    /**
     * @inheritdoc
     */
    protected function sendContent(){
        // Do nothing. It's redirect
        return;
    }
}
