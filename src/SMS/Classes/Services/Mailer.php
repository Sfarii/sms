<?php

namespace SMS\Classes\Services;

use SNS\Bundles\UserBundle\Entity\User;
use SNS\Bundles\UserBundle\Model\BaseUserInterface;

/**
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2016, SMS
 * @package SMS\Classes\Services
 */
class Mailer
{

    /**
    * @var Doctrine\ORM\EntityManager
    */
    private $_templating;

    /**
    * @var mailer
    */
    private $_mailer;

    /**
    * @var String
    */
    private $_email;

	/**
	* @param Doctrine\ORM\EntityManager $em
	*/
	public function __construct($mailer , $templating)
    {
        $this->_mailer = $mailer;
        $this->_templating = $templating;
    }
    
    public function sendResettingEmail($user)
    {
        $message = \Swift_Message::newInstance()
        //->setSubject('Resetting Password')
        ->setFrom($this->_email)
        ->setTo($user->getEmail())
        ->setBody(
            $this->_templating->render(
                'SNSBundlesUserBundle:Resetting:email.html.twig',
                array('user' => $user )
            )
        , 'text/html')
        ;
        $this->_mailer->send($message);
    }

    public function sendRegistrationEmail($user)
    {
        $message = \Swift_Message::newInstance()
        //->setSubject('Resetting Password')
        ->setFrom($this->_email)
        ->setTo($user->getEmail())
        ->setBody(
            $this->_templating->render(
                'SNSBundlesUserBundle:Registration:email.html.twig',
                array('user' => $user )
            )
        , 'text/html')
        ;
        $this->_mailer->send($message);
    }

    public function setEmail($email)
    {
        $this->_email = $email;
    }

}

