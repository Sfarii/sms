<?php

namespace SMS\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Parent
 * @Vich\Uploadable
 * @ORM\Table(name="parent")
 * @ORM\Entity(repositoryClass="SMS\UserBundle\Repository\ParentRepository")
 */
class StudentParent extends User
{
    

    /**
     * @var string
     *
     * @ORM\Column(name="fatherName", type="string", length=50)
     * @Assert\NotBlank(groups= {"Registration" , "SimpleRegistration" , "Edit"})
     * @Assert\Regex(pattern="/^[a-z0-9 .\-]+$/i" ,match=true , groups= {"Registration" , "SimpleRegistration" , "Edit"})
     * @Assert\Length(min = 2, max = 40 , groups= {"Registration" , "SimpleRegistration" , "Edit"})
     */
    private $fatherName;

    /**
     * @var string
     *
     * @ORM\Column(name="motherName", type="string", length=50)
     *
     * @Assert\NotBlank(groups= {"Registration" , "SimpleRegistration" , "Edit"})
     * @Assert\Regex(pattern="/^[a-z0-9 .\-]+$/i" ,match=true , groups= {"Registration" , "SimpleRegistration" , "Edit"})
     * @Assert\Length(min = 2, max = 40 , groups= {"Registration" , "SimpleRegistration" , "Edit"})
     */
    private $motherName;

    /**
     * @var string
     *
     * @ORM\Column(name="familyName", type="string", length=50)
     * @Assert\NotBlank(groups= {"Registration" , "SimpleRegistration" , "Edit"})
     * @Assert\Regex(pattern="/^[a-z0-9 .\-]+$/i" ,match=true , groups= {"Registration" , "SimpleRegistration" , "Edit"})
     * @Assert\Length(min = 2, max = 40 , groups= {"Registration" , "SimpleRegistration" , "Edit"})
     */
    private $familyName;

    /**
     * @var string
     *
     * @ORM\Column(name="fatherProfession", type="string", length=100)
     * @Assert\NotBlank(groups= {"Registration" , "SimpleRegistration" , "Edit"}),
     * @Assert\Regex(pattern="/^[a-z0-9 .\-]+$/i" ,match=true , groups= {"Registration" , "SimpleRegistration" , "Edit"})
     * @Assert\Length(min = 2, max = 100 , groups= {"Registration" , "SimpleRegistration" , "Edit"})
     */
    private $fatherProfession;

    /**
     * @var string
     *
     * @ORM\Column(name="motherProfession", type="string", length=100)
     * @Assert\NotBlank(groups= {"Registration" , "SimpleRegistration" , "Edit"}),
     * @Assert\Regex(pattern="/\d/",match=false , groups= {"Registration" , "SimpleRegistration" , "Edit"}),
     * @Assert\Length(min = 2, max = 100 , groups= {"Registration" , "SimpleRegistration" , "Edit"})
     */
    private $motherProfession;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=150)
     * @Assert\NotBlank(groups= {"Registration" , "SimpleRegistration" , "Edit"})
     * @Assert\Length(
     *   min = 2,
     *   max = 150,
     *   groups= {"Registration" , "SimpleRegistration" , "Edit"}
     * )
     * @Assert\Regex(pattern="/^[a-z0-9 .\-]+$/i" ,match=true , groups= {"Registration" , "SimpleRegistration" , "Edit"})
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=20)
     * @Assert\Regex( pattern="/\d/", groups= {"Registration" , "SimpleRegistration" , "Edit"})
     * @Assert\NotBlank(groups= {"Registration" , "SimpleRegistration" , "Edit"}),
     * @Assert\Length(
     *    min = 8,
     *    max = 20,
     *    groups= {"Registration" , "SimpleRegistration" , "Edit"}
     * )
     */
    private $phone;

    /**
     * One Parent has Many Students.
     * @ORM\OneToMany(targetEntity="Student", mappedBy="studentParent")
     */
    private $students;

    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->roles = array(self::ROLE_PARENT);
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
     * Set fatherName
     *
     * @param string $fatherName
     * @return Parent
     */
    public function setFatherName($fatherName)
    {
        $this->fatherName = $fatherName;

        return $this;
    }

    /**
     * Get fatherName
     *
     * @return string 
     */
    public function getFatherName()
    {
        return $this->fatherName;
    }

    /**
     * Set motherName
     *
     * @param string $motherName
     * @return Parent
     */
    public function setMotherName($motherName)
    {
        $this->motherName = $motherName;

        return $this;
    }

    /**
     * Get motherName
     *
     * @return string 
     */
    public function getMotherName()
    {
        return $this->motherName;
    }

    /**
     * Set familyName
     *
     * @param string $familyName
     * @return Parent
     */
    public function setFamilyName($familyName)
    {
        $this->familyName = $familyName;

        return $this;
    }

    /**
     * Get familyName
     *
     * @return string 
     */
    public function getFamilyName()
    {
        return $this->familyName;
    }

    /**
     * Set fatherProfession
     *
     * @param string $fatherProfession
     * @return Parent
     */
    public function setFatherProfession($fatherProfession)
    {
        $this->fatherProfession = $fatherProfession;

        return $this;
    }

    /**
     * Get fatherProfession
     *
     * @return string 
     */
    public function getFatherProfession()
    {
        return $this->fatherProfession;
    }

    /**
     * Set motherProfession
     *
     * @param string $motherProfession
     * @return Parent
     */
    public function setMotherProfession($motherProfession)
    {
        $this->motherProfession = $motherProfession;

        return $this;
    }

    /**
     * Get motherProfession
     *
     * @return string 
     */
    public function getMotherProfession()
    {
        return $this->motherProfession;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Parent
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
     * Set phone
     *
     * @param string $phone
     * @return Parent
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
     * Add students
     *
     * @param \SMS\UserBundle\Entity\Student $students
     * @return StudentParent
     */
    public function addStudent(\SMS\UserBundle\Entity\Student $students)
    {
        $this->students[] = $students;

        return $this;
    }

    /**
     * Remove students
     *
     * @param \SMS\UserBundle\Entity\Student $students
     */
    public function removeStudent(\SMS\UserBundle\Entity\Student $students)
    {
        $this->students->removeElement($students);
    }

    /**
     * Get students
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStudents()
    {
        return $this->students;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return StudentParent
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return StudentParent
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }
}
