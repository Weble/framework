<?php

namespace Zoolanders\Framework\Event\Application;

use Zoolanders\Framework\Event\HasSubjectInterface;

class Application extends \Zoolanders\Framework\Event\Event implements HasSubjectInterface
{
    /**
     * @var \Application
     */
    protected $application;

    /**
     * Beforesave constructor.
     * @param \Application $application
     */
    public function __construct (\Application $application)
    {
        $this->application = $application;
    }

    /**
     * @return \Application
     */
    public function getApplication ()
    {
        return $this->application;
    }
}
