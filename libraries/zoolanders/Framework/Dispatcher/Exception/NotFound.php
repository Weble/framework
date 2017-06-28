<?php

namespace Zoolanders\Framework\Dispatcher\Exception;

class NotFound extends DispatcherException
{
    public function __construct ($message = "", $code = 0, \Throwable $previous = null)
    {
        $code = 404;
        $message = $message ? $message : 'Not Found';

        parent::__construct($message, $code, $previous);
    }
}
