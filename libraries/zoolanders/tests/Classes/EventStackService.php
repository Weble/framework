<?php

namespace ZFTests\Classes;

/**
 * Class EventStackService
 * @package ZFTests\Classes
 */
class EventStackService
{
    // Event stack
    private $stack = null;

    // Instance
    private static $instance = null;

    /**
     * Class constructor
     */
    private function __construct()
    {
        $this->stack = new \SplQueue();
    }

    /**
     * Clone method
     */
    private final function __clone(){
    }

    /**
     * Wakeup method
     */
    private final function __wakeUp(){
    }

    /**
     * Get instance
     */
    public static function getInstance(){
        if(empty(self::$instance)){
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Add event to the queue
     *
     * @param $eventName
     * @param null $event
     */
    public function push($eventName, $event = null){

        $this->stack->enqueue( new EventNode($eventName, $event) );
    }

    /**
     * Add event to the queue
     */
    public function pop(){

        return  $this->stack->dequeue();
    }

    /**
     * Find event by name
     *
     * @return mixed
     */
    public function find($eventName){
        $found = false;

        $this->stack->rewind();

        while($node = $this->stack->current()){
            if($node->name == $eventName){
                $found = $this->stack->key();
                break;
            }
            $this->stack->next();
        }

        return $found;
    }

    /**
     * Get element by index
     *
     * @param   $index
     * @return  mixed
     */
    public function get($index){
        $val = null;

        if($this->stack->offsetExists($index)){
            $val = $this->stack->offsetGet($index)->event;
        }

        return $val;
    }
}
