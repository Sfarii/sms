<?php

namespace SMS\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * CatchUpLesson
 *
 * @ORM\Table(name="catch_up_lesson")
 * @UniqueEntity(fields={"typePaymentName" , "establishment"}, errorPath="catchUpLessonName")
 * @ORM\Entity(repositoryClass="SMS\PaymentBundle\Repository\CatchUpLessonRepository")
 */

class CatchUpLesson extends PaymentType
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank()
     * @Assert\Length(min = 2, max = 1000)
     * @Assert\Regex(pattern="/^[a-z0-9 .\-]+$/i" ,match=false)
     */
    private $description;

    /**
     * Many CatchUpLessons have One Professor.
     * @ORM\ManyToOne(targetEntity="SMS\UserBundle\Entity\Professor" , inversedBy="catchUpLessons" , cascade={"persist", "remove"},fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="professor_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $professor;

    /**
     * One CatchUpLessons has Many CatchUpLessonSechdule.
     * @ORM\OneToMany(targetEntity="CatchUpLessonSechdule", mappedBy="catchUpLesson" ,fetch="EXTRA_LAZY")
     */
    private $schedules;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->schedules = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set price
     *
     * @param float $price
     *
     * @return CatchUpLesson
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set registrationFee
     *
     * @param float $registrationFee
     *
     * @return CatchUpLesson
     */
    public function setRegistrationFee($registrationFee)
    {
        $this->registrationFee = $registrationFee;

        return $this;
    }

    /**
     * Get registrationFee
     *
     * @return float
     */
    public function getRegistrationFee()
    {
        return $this->registrationFee;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return CatchUpLesson
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
     *
     * @return CatchUpLesson
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
     * Set description
     *
     * @param string $description
     *
     * @return CatchUpLesson
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set professorPrice
     *
     * @param float $professorPrice
     *
     * @return CatchUpLesson
     */
    public function setProfessorPrice($professorPrice)
    {
        $this->professorPrice = $professorPrice;

        return $this;
    }

    /**
     * Get professorPrice
     *
     * @return float
     */
    public function getProfessorPrice()
    {
        return $this->professorPrice;
    }

    /**
     * Set establishment
     *
     * @param \SMS\EstablishmentBundle\Entity\Establishment $establishment
     *
     * @return CatchUpLesson
     */
    public function setEstablishment(\SMS\EstablishmentBundle\Entity\Establishment $establishment = null)
    {
        $this->establishment = $establishment;

        return $this;
    }

    /**
     * Get establishment
     *
     * @return \SMS\EstablishmentBundle\Entity\Establishment
     */
    public function getEstablishment()
    {
        return $this->establishment;
    }

    /**
     * Set author
     *
     * @param \SMS\UserBundle\Entity\User $author
     *
     * @return CatchUpLesson
     */
    public function setAuthor(\SMS\UserBundle\Entity\User $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \SMS\UserBundle\Entity\User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set professor
     *
     * @param \SMS\UserBundle\Entity\Professor $professor
     *
     * @return CatchUpLesson
     */
    public function setProfessor(\SMS\UserBundle\Entity\Professor $professor = null)
    {
        $this->professor = $professor;

        return $this;
    }

    /**
     * Get professor
     *
     * @return \SMS\UserBundle\Entity\Professor
     */
    public function getProfessor()
    {
        return $this->professor;
    }

    /**
     * Add schedule
     *
     * @param \SMS\PaymentBundle\Entity\CatchUpLessonSechdule $schedule
     *
     * @return CatchUpLesson
     */
    public function addSchedule(\SMS\PaymentBundle\Entity\CatchUpLessonSechdule $schedule)
    {
        $this->schedules[] = $schedule;

        return $this;
    }

    /**
     * Remove schedule
     *
     * @param \SMS\PaymentBundle\Entity\CatchUpLessonSechdule $schedule
     */
    public function removeSchedule(\SMS\PaymentBundle\Entity\CatchUpLessonSechdule $schedule)
    {
        $this->schedules->removeElement($schedule);
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
     * Add student
     *
     * @param \SMS\UserBundle\Entity\Student $student
     *
     * @return CatchUpLesson
     */
    public function addStudent(\SMS\UserBundle\Entity\Student $student)
    {
        $this->student[] = $student;

        return $this;
    }

    /**
     * Remove student
     *
     * @param \SMS\UserBundle\Entity\Student $student
     */
    public function removeStudent(\SMS\UserBundle\Entity\Student $student)
    {
        $this->student->removeElement($student);
    }

    /**
     * Get student
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStudent()
    {
        return $this->student;
    }
}
