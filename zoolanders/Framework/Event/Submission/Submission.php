<?php

namespace Zoolanders\Framework\Event\Submission;

class Submission extends \Zoolanders\Framework\Event\Event
{
    /**
     * @var \Category
     */
    protected $submission;

    /**
     * Beforesave constructor.
     * @param \Submission $submission
     */
    public function __construct(\Submission $submission)
    {
        $this->submission = $submission;
    }

    /**
     * @return \Submission
     */
    public function getSubmission()
    {
        return $this->submission;
    }
}
