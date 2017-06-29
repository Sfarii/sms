<?php

namespace API\Services;

use SNS\Bundles\UserBundle\Entity\User;
use SNS\Bundles\UserBundle\Model\BaseUserInterface;

/**
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2016, SMS
 * @package API\Services
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
    * @var String
    */
    private $_paymentTemplate;


  	/**
  	* @param Doctrine\ORM\EntityManager $em
  	*/
  	public function __construct($mailer , $templating)
    {
        $this->_mailer = $mailer;
        $this->_templating = $templating;
    }

    /**
    * @param String $paymentTemplate
    */
    public function setPaymentTemplate($paymentTemplate)
    {
        $this->_paymentTemplate = $paymentTemplate;
    }

    public function sendResettingEmail($user)
    {
        $message = \Swift_Message::newInstance()
        //->setSubject('Resetting Password')
        ->setFrom($this->_email)
        ->setTo($user->getEmail())
        ->setBody(
            $this->_templating->render(
                'SMSUserBundle:user/resetting:email.html.twig',
                array('user' => $user )
            )
        , 'text/html')
        ;
        $this->_mailer->send($message);
    }

    public function sendPaymentEmail($user , $payment)
    {
        $message = \Swift_Message::newInstance()
        //->setSubject('Resetting Password')
        ->setFrom($this->_email)
        ->setTo($user->getEmail())
        ->setBody(
            $this->_templating->render(
                $this->_paymentTemplate,
                array('user' => $user , 'payment' => $payment )
            )
        , 'text/html')
        ;
        $this->_mailer->send($message);
    }

    public function sendRegistrationEmailWithPassword($user)
    {
        $message = \Swift_Message::newInstance()
        //->setSubject('Resetting Password')
        ->setFrom($this->_email)
        ->setTo($user->getEmail())
        ->setBody(
            $this->_templating->render(
                'SMSUserBundle:user/registration:emailV2.html.twig',
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
                'SMSUserBundle:user/registration:emailV1.html.twig',
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
