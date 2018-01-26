<?php

namespace Zoolanders\Framework\Response\Error;

/**
 * Class JsonResponse
 * @package Zoolanders\Framework\Response
 */
class JsonResponse extends \Zoolanders\Framework\Response\JsonResponse implements ErrorResponseInterface
{
    /**
     * Set the exception occurred
     * @param \Exception $e
     */
    public function setException (\Exception $e)
    {
        $code = $e->getCode();

        $this->code = $code;
        $this->error = $e->getMessage();
    }
}
