<?php

namespace API\EventListener;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use SMS\UserBundle\Entity\Manager;
/**
 * @author Rami Sfari <rami2sfari@gmail.com>
 */
class AfterLoginListener
{
    /**
    * @var Symfony\Component\HttpFoundation\Session\Session $_session
    */
    private $_session;

    /**
    * @var Helper $_helper
    */
    private $_helper;

    /**
    * @param Symfony\Component\HttpFoundation\Session\Session $_session
    * @param Helper $_helper
    */
    public function __construct(Session $session , $helper)
    {
        $this->_session = $session;
        $this->_helper = $helper;
    }

    /**
    * @param Symfony\Component\Security\Http\Event\InteractiveLoginEvent $event
    */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        if (!$user instanceof Manager) {
            $this->_session->set('_logo' , $this->_helper->asset($user->getEstablishment(), 'imageFile') );
            $this->_session->set('_theme' , $user->getEstablishment()->getTheme() );
        }

    }
}
