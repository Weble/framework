<?php

namespace Zoolanders\Framework\Response\Error;

use Zoolanders\Framework\Response\ResponseInterface;

/**
 * Interface ResponseInterface
 * @package Zoolanders\Framework\Response
 */
interface ErrorResponseInterface extends ResponseInterface
{
    public function setException(\Exception $e);
}
