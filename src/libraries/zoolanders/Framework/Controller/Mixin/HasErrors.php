<?php

namespace Zoolanders\Framework\Controller\Mixin;

use Zoolanders\Framework\Collection\Collection;
use Zoolanders\Framework\Service\Alerts\Error;

/**
 * Class HasErrors
 * @package Zoolanders\Framework\Controller\Mixin
 */
trait HasErrors
{
    /**
     * @var Collection collection
     */
    protected $errors;

    /**
     * @var string  Errors scope name
     */
    protected $scope = '';

    /**
     * Return true if error pool not empty
     */
    protected function hasErrors ()
    {
        if (empty($this->errors)) {
            $this->errors = new Collection();
        }

        return !empty($this->errors) && $this->errors->has($this->scope) && !$this->errors->get($this->scope)->isEmpty();
    }

    /**
     * Set subarray error
     *
     * @param   subkey
     * @param   message
     */
    public function setError ($subkey, $message, $params = [])
    {
        if (empty($this->errors)) {
            $this->errors = new Collection();
        }

        if (!$this->errors->has($this->scope)) {
            $set = new Collection();
            $this->errors->put($this->scope, $set);
        }
        if (!$this->errors->get($this->scope)->has($subkey)) {
            $subset = new Collection();
            $this->errors->get($this->scope)->put($subkey, $subset);
        }

        $subset = $this->errors->get($this->scope)->get($subkey);

        if (!($message instanceof Error)) {
            $message = new Error($message, $params);
        }
        $subset->push($message);
    }

    /**
     * Get errors
     *
     * @return mixed
     */
    public function getErrors ()
    {
        if (empty($this->errors)) {
            $this->errors = new Collection();
        }

        return $this->errors->get($this->scope);
    }
}