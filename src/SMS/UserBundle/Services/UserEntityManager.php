<?php

namespace SMS\UserBundle\Services;

use SMS\UserBundle\Entity\User;
use SMS\UserBundle\Entity\UserInterface;
use SMS\UserBundle\Entity\StudentParent;
use SMS\UserBundle\Entity\Student;
use SMS\UserBundle\Entity\Manager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManager;

/**
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2016, SMS
 * @package API\Services
 */
class UserEntityManager
{
    /**
    * @var EntityManager
    */
    private $_em;

		/**
     * @var TokenStorageInterface
     */
    private $_tokenStorage;

    /**
    * @var \PasswordEncoder
    */
    private $_passwordEncoder;

    /**
    * @var \Mailer
    */
    private $_mailer;

    /**
    * @var \Repository
    */
    private $_getUserRepository;


    /**
    * @param Doctrine\ORM\EntityManager $em
    * @param \PasswordEncoder $passwordEncoder
    */
    public function __construct(EntityManager $em, $passwordEncoder , TokenStorageInterface $tokenStorage)
    {
        $this->_em = $em;
        $this->_passwordEncoder = $passwordEncoder;
				$this->_tokenStorage = $tokenStorage;
        $this->_getUserRepository = $this->_em->getRepository(User::class);
    }

    /**
    * @param String $mailer
    */
    public function setMailer($mailer)
    {
        $this->_mailer = $mailer;
    }


    /**
     * @param SMS\UserBundle\Entity\UserInterface $user
     * @return void
     */
    public function addUser(UserInterface $user)
    {
        $autoUsername = false;
        if (empty($user->getPlainPassword()) && empty($user->getUsername())) {
          $autoUsername = true;
          //check and generate username and strong password
          $user = $this->checkAndGeneratePasswordAndUsername($user);
        }
        // password encode
        $password = $this->_passwordEncoder
                ->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password);
        // set the username and email for the log in
        $this->updateCanonicalizer($user);
				// set the Creator
				$user->setCreator($this->_tokenStorage->getToken()->getUser());
				// set establishment
        if (!$this->_tokenStorage->getToken()->getUser() instanceof Manager){
  				$user->setEstablishment($this->_tokenStorage->getToken()->getUser()->getEstablishment());
        }
        // saveUser the user in the database
        $this->saveUser($user);
        // unique Recorde Number
				$this->generateRecordeNumber($user);
        // send email
        if (!$autoUsername) {
          $this->_mailer->sendRegistrationEmail($user);
        }else{
          $this->_mailer->sendRegistrationEmailWithPassword($user);
        }
    }

    /**
    * enable multiple Users from the database
    * @param array $choices
    * @param boolean $boolean
    */
    public function enabledAll( $choices = array() , $boolean)
    {
        foreach ($choices as $choice) {
            $user = $this->_getUserRepository->find($choice['value']);
            if ($user) {
                $user->setEnabled($boolean);
            }
        }
        $this->_em->flush();
    }

    /**
     * add User
     * @param SMS\UserBundle\Entity\UserInterface $user
     * @return void
     */
    public function saveUserManager(UserInterface $user)
    {
        // password encode
        $password = $this->_passwordEncoder
                ->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password);
        // set the username and email for the log in
        $this->updateCanonicalizer($user);
        // saveUser the user in the database
        $this->saveUser($user);
    }

    /**
     * edit User
     * @param User $user
     * @return void
     */
    public function editUser(UserInterface $user)
    {
      $this->updateCanonicalizer($user);
      // saveUser the user in the database
      $this->_em->flush($user);
      $this->_mailer->sendRegistrationEmailWithPassword($user);
    }

    public function updateStudent(UserInterface $user)
    {
      $this->updateCanonicalizer($user);
      if ($user->getStudentType() == false){
        $user->setSection(null);
        $user->setStudentParent(null);
      }
      // saveUser the user in the database
      $this->_em->flush($user);
    }

    /**
     * @param SMS\UserBundle\Entity\UserInterface $user
     * @return void
     */
    public function generateRecordeNumber(UserInterface $user)
    {
        if ($user instanceof Student) {
            $recordeNumber = sprintf("%d-%02d-%06d", date("Y"), $user->getEstablishment()->getId() , $user->getId());
            $user->setRecordeNumber($recordeNumber);
            // saveUser the user in the database
            $this->saveUser($user);
        }

    }

    /**
     * @param SMS\UserBundle\Entity\UserInterface $user
     * @return void
     */
    public function checkAndGeneratePasswordAndUsername(UserInterface $user)
    {
        if ($user instanceof StudentParent) {
            $username = $this->generateUsername($user->getFatherName(), $user->getFamilyName());
        } else {
            $username = $this->generateUsername($user->getFirstName(), $user->getLastName());
        }

        $user->setUsername($username);
        $user->setPlainPassword($this->randomPassword());

        return $user;
    }

    /**
     * generate unique user name
     *
     * @param String $first_name
     * @param String $last_name
     * @param String $max_size
     * @return String
     */
    public function generateUsername($firstName, $lastName, $maxSize = 4)
    {
        $secondeString = mb_convert_case($lastName, MB_CASE_LOWER, "UTF-8");
        $firstString = mb_convert_case($firstName, MB_CASE_LOWER, "UTF-8");
        do {
            $username = substr($secondeString . $firstString , 0, $maxSize);
            $username .= trim(rand(0, 100));
            $user = $this->_getUserRepository->findUserByUsername($username);

            if (is_null($user)) {
                return $username ;
            }
        } while (true);
    }

    /**
     * generate unique password
     *
     * @param String $max_size
     * @return String
     */
    public function randomPassword($max_size = 8)
    {

        // define variables used within the function
        $symbols = array();
        $used_symbols = '';
        $pass = '';

        // an array of different character types
        $symbols["lower_case"] = 'abcdefghijklmnopqrstuvwxyz';
        $symbols["upper_case"] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $symbols["numbers"] = '1234567890';
        $symbols["special_symbols"] = '!?~@#-_+<>[]{}';

        foreach ($symbols as $key=>$value) {
            $used_symbols .= $value; // build a string with all characters
        }
        $symbols_length = strlen($used_symbols) - 1; //strlen starts from 0 so to get number of characters deduct 1

        $pass = '';
        for ($i = 0; $i < $max_size; $i++) {
            $n = rand(0, $symbols_length); // get a random character from the string with all characters
            $pass .= $used_symbols[$n]; // add the character to the password string
        }

        return $pass; // return the generated password
    }

    /**
     * @param SMS\UserBundle\Entity\UserInterface $user
     * @return void
     */
    public function saveUser(UserInterface $user)
    {
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @param String $email
     * @return boolean
     */
    public function validEmail($email)
    {
        $user = $this->_getUserRepository->findUserByEmail($email);

        return is_null($user) ? false : true;
    }

    /**
     * @param String $email
     * @return void
     */
    public function resettingPassword($email, $tokenLifetime)
    {
        $user = $this->_getUserRepository->findUserByEmail($email);

        if (!is_null($user) && !$user->isPasswordRequestNonExpired($tokenLifetime)) {
            // create confirmation token & request date
            $user->setConfirmationToken($this->uniqueToken());
            $user->setPasswordRequestedAt(new \DateTime());
            // send email to the user
            $this->_mailer->sendResettingEmail($user);
            // update the user
            $this->saveUser($user);
        }
    }

    /**
     * @param SMS\UserBundle\Entity\UserInterface $user
     * @return void
     */
    public function resettingNewPassword(UserInterface $user, $tokenLifetime)
    {
        if (!is_null($user) && !$user->isPasswordRequestNonExpired($tokenLifetime)) {
            // clear confirmation token & request date
            $user->setConfirmationToken(null);
            $user->setPasswordRequestedAt(null);
            // encode password
            $password = $this->_passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            // update the user
            $this->saveUser($user);
        }
    }

    /**
     * @return String
     */
    public function uniqueToken()
    {
        do {
            $token = $this->generateToken();
            $user = $this->_getUserRepository->findUserByToken($token);

            if (is_null($user)) {
                return $token ;
            }
        } while (true);
    }

    /**
     * @return String
     */
    public function uniqueActivationToken()
    {
        do {
            $token = $this->generateToken();
            $user = $this->_getUserRepository->findUserByActivationToken($token);

            if (is_null($user)) {
                return $token ;
            }
        } while (true);
    }

    /**
     * @return string
     */
    public function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

    /**
     * @param SMS\UserBundle\Entity\UserInterface $user
     * @return void
     */
    public function updateCanonicalizer(UserInterface $user)
    {
        $emailCanonical = mb_convert_case($user->getEmail(), MB_CASE_LOWER, "UTF-8");
        $usernameCanonical = mb_convert_case($user->getUsername(), MB_CASE_LOWER, "UTF-8");

        $user->setUsernameCanonical($usernameCanonical);
        $user->setEmailCanonical($emailCanonical);
    }
}
