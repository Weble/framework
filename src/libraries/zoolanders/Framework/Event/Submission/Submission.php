<?php

namespace Zoolanders\Framework\Event\Submission;

use Zoolanders\Framework\Event\HasSubjectInterface;

class Submission extends \Zoolanders\Framework\Event\Event implements HasSubjectInterface
{
    /**
     * @var \Category
     */
    protected $submission;

    /**
     * Beforesave constructor.
     * @param \Submission $submission
     */
    public function __construct (\Submission $submission)
    {
        $this->submission = $submission;
    }

    /**
     * @return \Submission
     */
    public function getSubmission ()
    {
        return $this->submission;
    }
}
