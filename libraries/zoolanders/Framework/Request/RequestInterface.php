<?php

namespace Zoolanders\Framework\Request;

use Zoolanders\Framework\Data\Data;

/**
 * Class Request
 * @package Zoolanders\Framework\Request
 */
interface RequestInterface
{
    /**
     * Get http request headers
     *
     * @return Data
     */
    public function getHeaders ();

    /**
     * isAjax
     *
     * @return bool True if an ajax call is being made
     */
    public function isAjax ();

    /**
     * if the request is in json format
     *
     * @return bool True if an ajax call is being made
     */
    public function isJson ();

    /**
     * if the request is in json format
     *
     * @return bool True if an ajax call is being made
     */
    public function expectsJson ();

    /**
     * get the expected response format
     * @return bool
     */
    public function getExpectedResponse ();

    /**
     * Determines the CRUD task to use based on the view name and HTTP verb used in the request.
     * @credits https://github.com/akeeba/fof/blob/development/fof/Controller/DataController.php
     *
     * @return  string  The CRUD task (browse, read, edit, delete)
     */
    public function getHttpVerb ();

    /**
     * Gets a value from the input data.
     *
     * @param   string $name Name of the value to get.
     * @param   mixed $default Default value to return if variable does not exist.
     * @param   string $filter Filter to apply to the value.
     *
     * @return  mixed  The filtered input value.
     */
    public function get ($name, $default = null, $filter = 'cmd');

    /**
     * Sets a value
     *
     * @param   string  $name   Name of the value to set.
     * @param   mixed   $value  Value to assign to the input.
     *
     * @return  void
     */
    public function set($name, $value);
}