<?php

namespace Zoolanders\Framework\Dispatcher\Exception;

use Throwable;

class ControllerNotFound extends NotFound
{
    public function __construct ($message = '', $code = 0, Throwable $previous = null)
    {
        $message = $message ? $message : 'Controller Not Found';

        parent::__construct($message, $code, $previous);
    }
}
