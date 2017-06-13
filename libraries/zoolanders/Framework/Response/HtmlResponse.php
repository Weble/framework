<?php

namespace Zoolanders\Framework\Response;

/**
 * Class HtmlResponse
 * @package Zoolanders\Framework\Response
 */
class HtmlResponse extends Response
{
    /**
     * @inheritdoc
     */
    public function send()
    {
        $this->sendHeaders();
        $this->sendContent();

        return;
    }
}
