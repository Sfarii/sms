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
     * @Assert\NotBlank(groups= {"Registration" , "SimpleRegistration"}),
     * @Assert\Regex(pattern="/\d/",match=false , groups= {"Registration" , "SimpleRegistration"}),
     * @Assert\Length(min = 2, max = 40 , groups= {"Registration" , "SimpleRegistration"})
     * 
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="string", length=50)
     *
     * @Assert\NotBlank(groups= {"Registration" , "SimpleRegistration"}),
     * @Assert\Regex( pattern="/\d/",match=false, groups= {"Registration" , "SimpleRegistration"}),
     * @Assert\Length(
     *    min = 2,
     *    max = 40,
     *    groups= {"Registration" , "SimpleRegistration"}
     *  )
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=20)
     * @Assert\NotBlank(groups= {"Registration" , "SimpleRegistration"})
     */
    private $gender;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=20)
     *
     * @Assert\Regex( pattern="/\d/", groups= {"Registration" , "SimpleRegistration"}),
     * @Assert\NotBlank(groups= {"Registration" , "SimpleRegistration"}),
     * @Assert\Length(
     *    min = 8,
     *    max = 20,
     *    groups= {"Registration" , "SimpleRegistration"}
     * )
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=150)
     * 
     * @Assert\NotBlank(groups= {"Registration" , "SimpleRegistration"}),
     * @Assert\Length(
     *   min = 2,
     *   max = 150,
     *   groups= {"Registration" , "SimpleRegistration"}
     * )
     */
    private $address;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="user_image", fileNameProperty="imageName")
     * @Assert\Image()
     * @var File
     */
    protected $imageFile;


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
}
