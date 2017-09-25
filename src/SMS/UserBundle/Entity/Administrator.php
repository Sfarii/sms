<?php

namespace SMS\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Administrator
 * @Vich\Uploadable
 * @ORM\Table(name="administrator")
 * @ORM\Entity(repositoryClass="SMS\UserBundle\Repository\AdministratorRepository")
 */
class Administrator extends User
{

    /**
     * @var string
     *
     * @ORM\Column(name="firstName", type="string", length=50)
     *
     * @Assert\NotBlank(groups= {"Registration" , "SimpleRegistration" , "Edit"}),
     * @Assert\Regex(pattern="/^[a-z0-9 .\-]+$/i" ,match=true , groups= {"Registration" , "SimpleRegistration" , "Edit"})
     * @Assert\Length(min = 2, max = 40 , groups= {"Registration" , "SimpleRegistration" , "Edit"})
     *
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="string", length=50)
     *
     * @Assert\NotBlank(groups= {"Registration" , "SimpleRegistration" , "Edit"})
     * @Assert\Regex(pattern="/^[a-z0-9 .\-]+$/i" ,match=true, groups= {"Registration" , "SimpleRegistration" , "Edit"})
     * @Assert\Length(
     *    min = 2,
     *    max = 40,
     *    groups= {"Registration" , "SimpleRegistration" , "Edit"}
     *  )
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=20)
     * @Assert\Choice(choices = {"gender.male", "gender.female", "gender.other"} , groups= {"Registration" , "SimpleRegistration" , "Edit"})
     * @Assert\NotBlank(groups= {"Registration" , "SimpleRegistration" , "Edit"})
     */
    private $gender;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=20)
     *
     * @Assert\Regex( pattern="/\d/", groups= {"Registration" , "SimpleRegistration" , "Edit"}),
     * @Assert\NotBlank(groups= {"Registration" , "SimpleRegistration" , "Edit"}),
     * @Assert\Length(
     *    min = 8,
     *    max = 20,
     *    groups= {"Registration" , "SimpleRegistration" , "Edit"}
     * )
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=150)
     * @Assert\Regex(pattern="/^[a-z0-9 .\-]+$/i" ,match=true, groups= {"Registration" , "SimpleRegistration" , "Edit"})
     * @Assert\NotBlank(groups= {"Registration" , "SimpleRegistration" , "Edit"}),
     * @Assert\Length(
     *   min = 2,
     *   max = 150,
     *   groups= {"Registration" , "SimpleRegistration" , "Edit"}
     * )

     */
    private $address;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->roles = array(self::ROLE_ADMIN);
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return Administrator
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return Administrator
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return Administrator
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Administrator
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Administrator
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("%s %s",$this->getFirstName(),$this->getLastName());
    }
}
