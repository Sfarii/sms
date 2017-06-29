<?php

namespace SMS\UserBundle\Services;

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
    private $_resettingTemplatePath;
    private $_registrationTemplatePathWithPassword;
    private $_registrationTemplatePathWithOutPassword;

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
    * @param String $resettingTemplatePath
    */
    public function setResettingTemplatePath($resettingTemplatePath)
    {
      $this->_resettingTemplatePath = $resettingTemplatePath;
    }

    /**
    * Setter
    * @param String $registrationTemplatePathWithPassword
    */
    public function setRegistrationTemplatePathWithPassword($registrationTemplatePathWithPassword)
    {
      $this->_registrationTemplatePathWithPassword = $registrationTemplatePathWithPassword;
    }

    /**
    * Setter
    * @param String $registrationTemplatePathWithOutPassword
    */
    public function setRegistrationTemplatePathWithOutPassword($registrationTemplatePathWithOutPassword)
    {
      $this->_registrationTemplatePathWithOutPassword = $registrationTemplatePathWithOutPassword;
    }

    /**
    * send Resetting Email
    * @param SMS\UserBundle\Entity\User $user
    */
    public function sendResettingEmail($user)
    {
        $message = \Swift_Message::newInstance()
        ->setSubject('Resetting Password')
        ->setFrom($this->_email)
        ->setTo($user->getEmail())
        ->setBody(
            $this->_templating->render(
                $this->_resettingTemplatePath,
                array('user' => $user )
            )
        , 'text/html')
        ;
        $this->_mailer->send($message);
    }

    /**
    * send Registration Email With Password
    * @param SMS\UserBundle\Entity\User $user
    */
    public function sendRegistrationEmailWithPassword($user)
    {
        $message = \Swift_Message::newInstance()
        ->setSubject('Registration')
        ->setFrom($this->_email)
        ->setTo($user->getEmail())
        ->setBody(
            $this->_templating->render(
                $this->_registrationTemplatePathWithPassword,
                array('user' => $user )
            )
        , 'text/html')
        ;
        $this->_mailer->send($message);
    }

    /**
    * send Registration Email without password
    * @param SMS\UserBundle\Entity\User $user
    */
    public function sendRegistrationEmail($user)
    {
        $message = \Swift_Message::newInstance()
        ->setSubject('Registration')
        ->setFrom($this->_email)
        ->setTo($user->getEmail())
        ->setBody(
            $this->_templating->render(
                $this->_registrationTemplatePathWithOutPassword,
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
