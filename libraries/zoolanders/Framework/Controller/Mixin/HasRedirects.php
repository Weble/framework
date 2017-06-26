<?php

namespace Zoolanders\Framework\Controller\Mixin;

use Zoolanders\Framework\Controller\Controller;
use Zoolanders\Framework\Response\RedirectResponse;

/**
 * Trait HasRedirects
 * @package Zoolanders\Framework\Controller\Mixin
 */
trait HasRedirects
{
    /**
     * URL for redirection.
     *
     * @var    string
     */
    protected $redirectUrl;

    /**
     * Redirect message.
     *
     * @var    string
     */
    protected $redirectMessage;

    /**
     * Redirect message type.
     *
     * @var    string
     */
    protected $redirectMessageType;

    /**
     * @var App instance
     */
    protected $app;

    /**
     * Returns true if there is a redirect set in the controller
     *
     * @return  boolean
     */
    public function hasRedirect ()
    {
        return !empty($this->redirectUrl);
    }

    /**
     * Sets the internal message that is passed with a redirect
     *
     * @param   string $text Message to display on redirect.
     * @param   string $type Message type. Optional, defaults to 'message'.
     *
     * @return  string  Previous message
     */
    public function setMessage ($text, $type = 'message')
    {
        $previous = $this->redirectMessage;
        $this->redirectMessage = $text;
        $this->redirectMessageType = $type;

        return $previous;
    }


    /**
     * Redirects the browser or returns false if no redirect is set.
     *
     * @return  RedirectResponse|boolean  False if no redirect exists.
     */
    public function redirect ()
    {
        if ($this->app && $this->redirectUrl) {
            $this->app->enqueueMessage($this->redirectMessage, $this->redirectMessageType);

            return new RedirectResponse($this->redirectUrl);
        }

        return false;
    }

    /**
     * Set a URL for browser redirection.
     *
     * @param   string $url URL to redirect to.
     * @param   string $msg Message to display on redirect. Optional, defaults to value set internally by controller, if any.
     * @param   string $type Message type. Optional, defaults to 'message' or the type set by a previous call to setMessage.
     *
     * @return  Controller   This object to support chaining.
     */
    public function setRedirect ($url, $msg = null, $type = null)
    {
        // Set the redirection
        $this->redirectUrl = $url;

        if ($msg !== null) {
            // Controller may have set this directly
            $this->redirectMessage = $msg;
        }

        // Ensure the type is not overwritten by a previous call to setMessage.
        if (empty($this->redirectMessageType)) {
            $this->redirectMessageType = 'message';
        }

        // If the type is explicitly set, set it.
        if (!empty($type)) {
            $this->redirectMessageType = $type;
        }

        return $this;
    }
}
