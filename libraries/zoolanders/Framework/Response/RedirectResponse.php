<?php

namespace Zoolanders\Framework\Response;

/**
 * Class RedirectResponse
 * @package Zoolanders\Framework\Response
 */
class RedirectResponse extends Response
{
    /**
     * @var bool
     */
    public $type = false;

    /**
     * RedirectResponse constructor
     *
     * @param   $location
     * @param   int $code
     */
    public function __construct ($location = '/', $code = 301)
    {
        parent::__construct($location, $code);

        $this->setHeader('Location', $this->data);
    }

    /**
     * @inheritdoc
     */
    protected function sendContent ()
    {
        // Do nothing. It's redirect
        return;
    }
}
