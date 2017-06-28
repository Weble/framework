<?php

namespace Zoolanders\Framework\View;

/**
 * Interface ViewInterface
 * @package Zoolanders\Framework\View
 */
interface ViewInterface
{
    /**
     * Render / perform content method
     *
     * @param array $data
     *
     * @return mixed
     */
    public function display ($data = []);

    /**
     * Return the view type (html, json, pdf, etc)
     *
     * @return mixed
     */
    public function getType ();
}
