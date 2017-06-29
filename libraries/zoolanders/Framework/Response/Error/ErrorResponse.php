<?php

namespace Zoolanders\Framework\Response\Error;
use Zoolanders\Framework\Response\Response;

/**
 * Class JsonResponse
 * HTTP Response helper
 */
class ErrorResponse extends Response implements ErrorResponseInterface
{
    /**
     * Set the exception occurred
     * @param \Exception $e
     */
    public function setException (\Exception $e)
    {
        $code = $e->getCode();
        $msg = $e->getMessage();

        $this->code = $code;
        $this->data = $e->getMessage();
    }
}
