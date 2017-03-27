<?php

namespace SMS\Classes\Services;

use SMS\UserBundle\Entity\User;
use SMS\UserBundle\Entity\UserInterface;
use SMS\UserBundle\Entity\StudentParent;

/**
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2016, SMS
 * @package SMS\Classes\Services
 */
class UserEntityManager
{
	/**
	* @var Doctrine\ORM\EntityManager
	*/
	private $_em;

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
	public function __construct($em , $passwordEncoder )
    {
        $this->_em = $em;
        $this->_passwordEncoder = $passwordEncoder;
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
     * @param User $user
     * @return void
     */
    public function addUser ($user){
    	if ($user instanceOf UserInterface){
            // password encode
            if (empty($user->getPlainPassword()) && empty($user->getUsername())){

                if ($user instanceOf StudentParent){
                    $username = $this->generateUsername($user->getFatherName(),$user->getFamilyName());
                }else{
                    $username = $this->generateUsername($user->getFirstName(),$user->getLastName());
                }
                
                $user->setUsername($username);
                $user->setPlainPassword($this->randomPassword());
            }
    		$password = $this->_passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            // set the username and email for the log in
            $this->updateCanonicalizer($user);
            // saveUser the user in the database
    		$this->saveUser($user);
            // send email 
            //$this->_mailer->sendRegistrationEmail($user);
    	}
    }

    /**
     * generate unique user name
     *
     * @param String $first_name
     * @param String $last_name
     * @param String $max_size
     * @return String 
     */
    function generateUsername($first_name, $last_name, $max_size = 8)
    {
        $i = 0;
        do {
            $first_string = substr($first_name, 0, mt_rand($i , strlen($first_name)));
            $seconde_string = mb_convert_case($last_name, MB_CASE_LOWER , "UTF-8");
            $first_string = mb_convert_case($first_string, MB_CASE_LOWER , "UTF-8");
            $username = substr($seconde_string.$first_string , 0 , $max_size);

            $i++;
            $user = $this->_getUserRepository->findUserByUsername(mb_convert_case($username, MB_CASE_LOWER , "UTF-8"));

            if (is_null($user)){
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
    function randomPassword($max_size = 8) {
     
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
     * @param User $user
     * @return void
     */
    public function accountActivation ($user)
    {
        if ($user instanceOf UserInterface){
            // create activation token & request date
            $user->setActivationToken(null);
            $user->setEnabled(true);
            $this->saveUser($user);
        }
    }

    /**
     * @param User $user
     * @return void
     */
    public function saveUser ($user){
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
     * @param User $user
     * @return void
     */
    public function resettingNewPassword($user , $plainPassword , $tokenLifetime)
    {
        if (!is_null($user) && !$user->isPasswordRequestNonExpired($tokenLifetime)) {
            // clear confirmation token & request date
            $user->setConfirmationToken(null);
            $user->setPasswordRequestedAt(null);
            // encode password
            $password = $this->_passwordEncoder->encodePassword($user, $plainPassword);
            $user->setPassword($password);
            // update the user
            $this->saveUser($user);
        }else{
            die($user->isPasswordRequestNonExpired(180));
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

            if (is_null($user)){
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

            if (is_null($user)){
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
     * @param User $user
     * @return void
     */
    public function updateCanonicalizer($user)
    {
        $emailCanonical = mb_convert_case($user->getEmail(), MB_CASE_LOWER , "UTF-8");
        $usernameCanonical = mb_convert_case($user->getUsername(), MB_CASE_LOWER , "UTF-8");

        $user->setUsernameCanonical($usernameCanonical);
        $user->setEmailCanonical($emailCanonical);
    }
    
}

