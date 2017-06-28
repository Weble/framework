<?php

namespace Zoolanders\Framework\Dispatcher\Exception;

use Throwable;

class AccessForbidden extends DispatcherException
{
    public function __construct ($message = "", $code = 0, Throwable $previous = null)
    {
        $code = 403;
        $message = $message ? $message : 'Access Denied';

        parent::__construct($message, $code, $previous);
    }
}
