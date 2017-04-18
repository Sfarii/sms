<?php

namespace API\Services;

use SMS\UserBundle\Entity\User;
use SMS\UserBundle\Entity\UserInterface;
use SMS\UserBundle\Entity\StudentParent;
use SMS\UserBundle\Entity\Student;

/**
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2016, SMS
 * @package API\Services
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
    public function addUser (UserInterface $user){
        //check and generate username and strong password
        $user = $this->checkAndGeneratePasswordAndUsername($user);
        $user = $this->generateRecordeNumber($user);
        // password encode
		$password = $this->_passwordEncoder
                        ->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password);
        // set the username and email for the log in
        $this->updateCanonicalizer($user);
        // saveUser the user in the database
		$this->saveUser($user);
        // send email 
        $this->_mailer->sendRegistrationEmail($user);
    	
    }

    /**
     * @param User $user
     * @return void
     */
    public function generateRecordeNumber($user)
    {

        if ($user instanceOf Student){
            
            $gradeCode = $user->getSection()->getGrade()->getGradeCode();
            preg_match("/([a-zA-Z]+)(\\d+)/", $this->_em->getRepository(Student::class)->findLast()['recordeNumber'],$result);
            
            if (empty($result)){
                $recordeNumber = sprintf("%s%04d", $gradeCode , 1);
            }else{
                $number = intval($result[2]);
                $number = $number + 1;
                $recordeNumber = sprintf("%s%04d", $gradeCode , $number);
            }
        
            $user->setRecordeNumber($recordeNumber);
        
        }
        return $user;
    }

    /**
     * @param User $user
     * @return void
     */
    public function checkAndGeneratePasswordAndUsername($user)
    {
        if (empty($user->getPlainPassword()) && empty($user->getUsername())){

            if ($user instanceOf StudentParent){
                $username = $this->generateUsername($user->getFatherName(),$user->getFamilyName());
            }else{
                $username = $this->generateUsername($user->getFirstName(),$user->getLastName());
            }
            
            $user->setUsername($username);
            $user->setPlainPassword($this->randomPassword());
        }

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
    function generateUsername($firstName, $lastName , $maxSize = 5)
    {
        $secondeString = mb_convert_case($lastName, MB_CASE_LOWER , "UTF-8");
        $firstString = mb_convert_case($firstName, MB_CASE_LOWER , "UTF-8");
        do {
            $secondeStringSize = strlen($secondeString);
            $firstStringSize = mt_rand((strlen($firstString)/2) , strlen($firstString));
            if ($secondeStringSize >= $maxSize)
                $secondeStringSize = $secondeStringSize - ($firstStringSize/2);

            $username = substr(substr($secondeString, 0, $secondeStringSize).substr($firstString, 0, $firstStringSize) , 0 , $maxSize);

            $user = $this->_getUserRepository->findUserByUsername($username);

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

