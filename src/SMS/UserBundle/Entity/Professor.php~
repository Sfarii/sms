<?php

namespace SMS\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Professor
 * @Vich\Uploadable
 * @ORM\Table(name="professor")
 * @ORM\Entity(repositoryClass="SMS\UserBundle\Repository\ProfessorRepository")
 */
class Professor extends User
{
    /**
     * @var string
     *
     * @ORM\Column(name="firstName", type="string", length=50)
     * @Assert\NotBlank(groups= {"Registration" , "SimpleRegistration" , "Edit"}),
     * @Assert\Regex(pattern="/^[a-z0-9 .\-]+$/i" ,match=true , groups= {"Registration" , "SimpleRegistration" , "Edit"})
     * @Assert\Length(min = 2, max = 40 , groups= {"Registration" , "SimpleRegistration" , "Edit"})
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="string", length=50)
     * @Assert\NotBlank(groups= {"Registration" , "SimpleRegistration" , "Edit"}),
     * @Assert\Regex(pattern="/^[a-z0-9 .\-]+$/i" ,match=true , groups= {"Registration" , "SimpleRegistration" , "Edit"})
     * @Assert\Length(min = 2, max = 40 , groups= {"Registration" , "SimpleRegistration" , "Edit"})
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
     * @var \DateTime
     *
     * @ORM\Column(name="birthday", type="date")
     * @Assert\NotBlank(groups= {"Registration" , "SimpleRegistration" , "Edit"})
     * @Assert\Date(groups= {"Registration" , "SimpleRegistration" , "Edit"})
     */
    private $birthday;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=20)
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
     * @var string
     *
     * @ORM\Column(name="grade", type="string", length=150)
     * @Assert\NotBlank(groups= {"Registration" , "SimpleRegistration" , "Edit"}),
     * @Assert\Length(
     *   min = 2,
     *   max = 150,
     *   groups= {"Registration" , "SimpleRegistration" , "Edit"}
     * )
     * @Assert\Regex(pattern="/^[a-z0-9 .\-]+$/i" ,match=true , groups= {"Registration" , "SimpleRegistration" , "Edit"})
     */
    private $grade;

    /**
     * One Professor has Many Schedules.
     * @ORM\OneToMany(targetEntity="SMS\StudyPlanBundle\Entity\Schedule", mappedBy="professor")
     */
    private $schedules;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->roles = array(self::ROLE_PROFESSOR);
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
     * @return Professor
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
     * @return Professor
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
     * @return Professor
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
     * @return Professor
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
     * Set phone
     *
     * @param string $phone
     * @return Professor
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
     * @return Professor
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
     * Set grade
     *
     * @param string $grade
     * @return Professor
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;

        return $this;
    }

    /**
     * Get grade
     *
     * @return string
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * Add schedules
     *
     * @param \SMS\StudyPlanBundle\Entity\Schedule $schedules
     * @return Professor
     */
    public function addSchedule(\SMS\StudyPlanBundle\Entity\Schedule $schedules)
    {
        $this->schedules[] = $schedules;

        return $this;
    }

    /**
     * Remove schedules
     *
     * @param \SMS\StudyPlanBundle\Entity\Schedule $schedules
     */
    public function removeSchedule(\SMS\StudyPlanBundle\Entity\Schedule $schedules)
    {
        $this->schedules->removeElement($schedules);
    }

    /**
     * Get schedules
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSchedules()
    {
        return $this->schedules;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Professor
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
     * @return Professor
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

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("%s  %s",$this->getFirstName(),$this->getLastName());
    }
}
