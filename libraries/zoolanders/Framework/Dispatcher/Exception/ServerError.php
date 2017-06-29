<?php

namespace Zoolanders\Framework\Dispatcher\Exception;

use Throwable;

class ServerError extends DispatcherException
{
    public function __construct ($message = '', $code = 0, Throwable $previous = null)
    {
        $code = 500;
        $message = $message ? $message : 'Server Error';

        parent::__construct($message, $code, $previous);
    }
}
