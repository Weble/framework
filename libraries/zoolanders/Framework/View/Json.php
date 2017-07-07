<?php

namespace Zoolanders\Framework\View;

/**
 * Class Html
 * @package Zoolanders\Framework\View
 */
class Json extends View
{
    protected $type = 'json';

    /**
     * @inheritdoc
     */
    public function render ($data = [])
    {
        if (!empty($data)) {
            $this->data = $data;
        }

        return json_encode($this->data);
    }
}
