<?php

namespace Zoolanders\Framework\Controller\Mixin;

use Zoolanders\Framework\Controller\Exception\AccessForbidden;

trait NeedsCsrfProtection {
    /**
     * Provides CSRF protection through the forced use of a secure token. If the token doesn't match the one in the
     * session we return false.
     *
     * @return  bool
     *
     * @throws  \Exception
     */
    protected function csrfProtection () {
        $hasToken = false;
        $session = $this->container->system->getSession();

        // Joomla! 2.5+ (Platform 12.1+) method
        if (method_exists($session, 'getToken')) {
            $token = $session->getToken();
            $hasToken = $this->input->get($token, false, 'none') == 1;

            if (!$hasToken) {
                $hasToken = $this->input->get('_token', null, 'none') == $token;
            }
        }

        // Joomla! 2.5+ formToken method
        if (!$hasToken) {
            if (method_exists($session, 'getFormToken')) {
                $token = $session->getFormToken();
                $hasToken = $this->input->get($token, false, 'none') == 1;

                if (!$hasToken) {
                    $hasToken = $this->input->get('_token', null, 'none') == $token;
                }
            }
        }

        if (!$hasToken) {
            throw new AccessForbidden(403, \JText::_('COM_ZOOLANDERS_ACCESS_FORBIDDEN'));
        }

        return true;
    }
}
