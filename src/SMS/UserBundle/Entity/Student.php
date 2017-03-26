<?php

namespace SMS\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
/**
 * Student
 *
 * @ORM\Table(name="student")
 * @ORM\Entity(repositoryClass="SMS\UserBundle\Repository\StudentRepository")
 */
class Student extends User
{
    /**
     * @var string
     *
     * @ORM\Column(name="recordeNumber", type="string", length=150)
     */
    private $recordeNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="firstName", type="string", length=50)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="string", length=50)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=30)
     */
    private $gender;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthday", type="date")
     */
    private $birthday;

    /**
     * Many Students have One Section.
     * @ORM\ManyToOne(targetEntity="SMS\EstablishmentBundle\Entity\Section")
     * @ORM\JoinColumn(name="section_id", referencedColumnName="id")
     */
    private $section;

    /**
     * Many Students have One Parent.
     * @ORM\ManyToOne(targetEntity="StudentParent" , inversedBy="students")
     * @ORM\JoinColumn(name="student_parent_id", referencedColumnName="id")
     */
    private $studentParent;


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
     * Set recordeNumber
     *
     * @param string $recordeNumber
     * @return Student
     */
    public function setRecordeNumber($recordeNumber)
    {
        $this->recordeNumber = $recordeNumber;

        return $this;
    }

    /**
     * Get recordeNumber
     *
     * @return string 
     */
    public function getRecordeNumber()
    {
        return $this->recordeNumber;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return Student
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
     * @return Student
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
     * @return Student
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
     * Set birthday
     *
     * @param \DateTime $birthday
     * @return Student
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime 
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set section
     *
     * @param \SMS\EstablishmentBundle\Entity\Section $section
     * @return Student
     */
    public function setSection(\SMS\EstablishmentBundle\Entity\Section $section = null)
    {
        $this->section = $section;

        return $this;
    }

    /**
     * Get section
     *
     * @return \SMS\EstablishmentBundle\Entity\Section 
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * Set studentParent
     *
     * @param \SMS\UserBundle\Entity\StudentParent $studentParent
     * @return Student
     */
    public function setStudentParent(\SMS\UserBundle\Entity\StudentParent $studentParent = null)
    {
        $this->studentParent = $studentParent;

        return $this;
    }

    /**
     * Get studentParent
     *
     * @return \SMS\UserBundle\Entity\StudentParent 
     */
    public function getStudentParent()
    {
        return $this->studentParent;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Student
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
     * @return Student
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
