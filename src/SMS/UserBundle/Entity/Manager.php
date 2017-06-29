<?php

namespace SMS\UserBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Manager
 *
 * @Vich\Uploadable
 * @ORM\Table(name="manager")
 * @ORM\Entity(repositoryClass="SMS\UserBundle\Repository\ManagerRepository")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2016, SMS
 */
class Manager extends User
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="firstName", type="string", length=50, nullable=true)
     * @Assert\NotBlank(groups= {"Registration" , "SimpleRegistration" , "Edit"}),
     * @Assert\Regex(pattern="/^[a-z0-9 .\-]+$/i" ,match=true , groups= {"Registration" , "SimpleRegistration" , "Edit"})
     * @Assert\Length(min = 2, max = 40 , groups= {"Registration" , "SimpleRegistration" , "Edit"})
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="string", length=50, nullable=true)
     * @Assert\NotBlank(groups= {"Registration" , "SimpleRegistration" , "Edit"})
     * @Assert\Regex(pattern="/^[a-z0-9 .\-]+$/i" ,match=true , groups= {"Registration" , "SimpleRegistration" , "Edit"})
     * @Assert\Length(min = 2, max = 40 , groups= {"Registration" , "SimpleRegistration" , "Edit"})
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=20, nullable=true)
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
     * @ORM\Column(name="address", type="string", length=150 , nullable=true)
     * @Assert\NotBlank(groups= {"Registration" , "SimpleRegistration" , "Edit"}),
     * @Assert\Length(
     *   min = 2,
     *   max = 150,
     *   groups= {"Registration" , "SimpleRegistration" , "Edit"}
     * )
     * @Assert\Regex(pattern="/^[a-z0-9 .\-]+$/i" ,match=true , groups= {"Registration" , "SimpleRegistration" , "Edit"})
     */
    private $address;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->roles = array(self::ROLE_MANAGER);
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Manager
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
     *
     * @return Manager
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
     * Set phone
     *
     * @param string $phone
     *
     * @return Manager
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
     *
     * @return Manager
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
        return sprintf("%s",$this->getUsername());
    }
}
