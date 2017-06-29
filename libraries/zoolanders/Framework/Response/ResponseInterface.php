<?php

namespace Zoolanders\Framework\Response;

/**
 * Interface ResponseInterface
 * @package Zoolanders\Framework\Response
 */
interface ResponseInterface
{
    /**
     * Set response header
     *
     * @param   $key
     * @param   $value
     *
     * @return  Response
     */
    public function setHeader ($key, $value);

    /**
     * Override all response headers
     *
     * @param   array
     *
     * @return  Response
     */
    public function setHeaders ($headers);

    /**
     * @return array
     */
    public function getHeaders();

    /**
     * @param $key string
     * @return string
     */
    public function getHeader($key);

    /**
     * Set content
     *
     * @param   Content
     *
     * @return  Response
     */
    public function setContent ($content);

    /**
     * Get the current Content
     * @return mixed
     */
    public function getContent();

    /**
     * Send prepared response to user agent
     *
     * @return  void
     */
    public function send ();
}
