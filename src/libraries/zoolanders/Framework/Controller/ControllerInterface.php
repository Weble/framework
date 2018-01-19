<?php

namespace Zoolanders\Framework\Controller;

/**
 * Basic Controller Class
 */
interface ControllerInterface
{
    /**
     * Default task names
     */
    const TASK_INDEX = 'index';
    const TASK_READ = 'read';
    const TASK_SAVE = 'save';
    const TASK_DELETE = 'remove';

    /**
     * @return string
     */
    public function getDefaultTask ();

    /**
     * Register the default task to perform if a mapping is not found.
     *
     * @param   string $method The name of the method in the derived class to perform if a named task is not found.
     *
     * @return  ControllerInterface  This object to support chaining.
     */
    public function registerDefaultTask ($method);

    /**
     * Method to get the model name
     * @return  string  The name of the model
     */
    public function getName ();
}