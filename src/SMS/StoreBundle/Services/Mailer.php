<?php

namespace SMS\StoreBundle\Services;

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
    private $_registrationTemplate;

  	/**
  	* @param Doctrine\ORM\EntityManager $em
  	*/
  	public function __construct($mailer , $templating)
    {
        $this->_mailer = $mailer;
        $this->_templating = $templating;
    }

    /**
    * Setter
    * @param String $paymentTemplate
    */
    public function setPaymentTemplatePath($paymentTemplate)
    {
      $this->_paymentTemplate = $paymentTemplate;
    }

    /**
    * Setter
    * @param String $paymentTemplate
    */
    public function setRegistrationTemplatePath($registrationTemplate)
    {
      $this->_registrationTemplate = $registrationTemplate;
    }

    /**
    * send Payment Email
    * @param SMS\UserBundle\Entity\User $user
    * @param SMS\PaymentBundle\Entity\Payment $payment
    */
    public function sendPaymentEmail($user , $payment)
    {
        $message = \Swift_Message::newInstance()
        ->setSubject('Payment')
        ->setFrom($this->_email)
        ->setTo($payment->getStudent()->getEmail())
        ->setBody(
            $this->_templating->render(
                $this->_paymentTemplate,
                array('user' => $user , 'payment' => $payment )
            )
        , 'text/html')
        ;
        $this->_mailer->send($message);
    }

    /**
    * send Registration Email
    * @param SMS\UserBundle\Entity\User $user
    * @param SMS\PaymentBundle\Entity\Registration $registration
    */
    public function sendRegistrationEmail($user , $registration)
    {
        $message = \Swift_Message::newInstance()
        ->setSubject('Registration')
        ->setFrom($this->_email)
        ->setTo($user->getEmail())
        ->setBody(
            $this->_templating->render(
                $this->_registrationTemplate,
                array('user' => $user , 'registration' => $registration )
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
