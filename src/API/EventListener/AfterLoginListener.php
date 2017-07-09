<?php

namespace API\EventListener;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class AfterLoginListener
{
    private $session;

    private $sessionName;

    public function __construct(Session $session , $sessionName)
    {
        $this->session = $session;
        $this->sessionName = $sessionName;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        if (!$user instanceof Manager ) {
        }

    }
}
