<?php

namespace Zoolanders\Framework\Service\Alerts;

/**
 * Class Error
 * System error instance class
 * @package Zoolanders\Framework\Service\Alerts
 */
class Error
{
    /**
     * @var string  Error message
     */
    public $message = '';

    /**
     * Error constructor.
     * @param $text
     * @param array $args
     */
    public function __construct($text, $args = [])
    {
        $this->message = $text;

        if(!empty($args)){
            foreach($args as $key => $value){
                $this->{$key} = $value;
            }
        }
    }

    /**
     * Magic set
     *
     * @param $key
     * @param $value
     */
    public function __set($key, $value){
        $this->{$key} = $value;
    }

    /**
     * Magic get
     *
     * @param $key
     * @return null
     */
    public function __get($key){
        return isset($this->{$key}) ? $this->{$key} : null;
    }

    /**
     * Bind from exception
     *
     * @param \Exception $e
     */
    public function fromException(\Exception $e)
    {
        $this->message = $e->getMessage();
    }

    /**
     * To json method
     *
     * @return json
     */
    public function toJson(){

        return json_encode($this);
    }
}
