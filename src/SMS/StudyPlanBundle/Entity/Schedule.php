<?php

namespace SMS\StudyPlanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Schedule
 *
 * @ORM\Table(name="schedule")
 * @ORM\Entity(repositoryClass="SMS\StudyPlanBundle\Repository\ScheduleRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Schedule
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
     * Many Schedules have One Section.
     * @ORM\ManyToOne(targetEntity="SMS\EstablishmentBundle\Entity\Section",fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="section_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $section;

    /**
     * @var string
     *
     * @ORM\Column(name="day", type="string", length=150)
     * @Assert\Choice({"sunday", "monday", "tuesday","wednesday", "thursday", "friday","saturday"})
     * @Assert\NotBlank()
     */
    private $day;

    /**
     * Many Schedules have Many Sessions.
     * @ORM\ManyToMany(targetEntity="Session", inversedBy="schedules",fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="schedules_sessions",
     *      joinColumns={@ORM\JoinColumn(name="schedule_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="session_id", referencedColumnName="id")}
     *      )
     * @Assert\NotBlank()
     */
    private $sessions;

    /**
     * Many Schedules have One Course.
     * @ORM\ManyToOne(targetEntity="Course",fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $course;

    /**
     * Many Schedules have One Professor.
     * @ORM\ManyToOne(targetEntity="SMS\UserBundle\Entity\Professor" , inversedBy="schedules",fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="professor_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $professor;

    /**
     * One User has Many Divivsions.
     * @ORM\ManyToOne(targetEntity="SMS\UserBundle\Entity\User" ,fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var datetime $created
     *
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var datetime $updated
     * 
     * @ORM\Column(type="datetime", nullable = true)
     */
    private $updated;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sessions = new \Doctrine\Common\Collections\ArrayCollection();
    }

     /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->setUpdated(new \DateTime('now'));

        if ($this->getCreated() == null) {
            $this->setCreated(new \DateTime('now'));
        }
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
     * Set created
     *
     * @param \DateTime $created
     * @return Schedule
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
     * @return Schedule
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
     * Set section
     *
     * @param \SMS\EstablishmentBundle\Entity\Section $section
     * @return Schedule
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
     * Set day
     *
     * @param String $day
     * @return Schedule
     */
    public function setDay($day)
    {
        $this->day = $day;

        return $this;
    }

    /**
     * Get day
     *
     * @return \SMS\StudyPlanBundle\Entity\Day 
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Set course
     *
     * @param \SMS\StudyPlanBundle\Entity\Course $course
     * @return Schedule
     */
    public function setCourse(\SMS\StudyPlanBundle\Entity\Course $course = null)
    {
        $this->course = $course;

        return $this;
    }

    /**
     * Get course
     *
     * @return \SMS\StudyPlanBundle\Entity\Course 
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * Set professor
     *
     * @param \SMS\UserBundle\Entity\Professor $professor
     * @return Schedule
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
     * Set user
     *
     * @param \SMS\UserBundle\Entity\User $user
     * @return Schedule
     */
    public function setUser(\SMS\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \SMS\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add sessions
     *
     * @param \SMS\StudyPlanBundle\Entity\Session $sessions
     * @return Schedule
     */
    public function addSession(\SMS\StudyPlanBundle\Entity\Session $sessions)
    {
        $this->sessions[] = $sessions;

        return $this;
    }

    /**
     * Remove sessions
     *
     * @param \SMS\StudyPlanBundle\Entity\Session $sessions
     */
    public function removeSession(\SMS\StudyPlanBundle\Entity\Session $sessions)
    {
        $this->sessions->removeElement($sessions);
    }

    /**
     * Get sessions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSessions()
    {
        return $this->sessions;
    }

    /**
     * Set grade
     *
     * @param \SMS\EstablishmentBundle\Entity\Grade $grade
     * @return Section
     */
    public function setGrade(\SMS\EstablishmentBundle\Entity\Grade $grade = null)
    {
        $this->grade = $grade;

        return $this;
    }

    /**
     * Get grade
     *
     * @return \SMS\EstablishmentBundle\Entity\Grade 
     */
    public function getGrade()
    {
        return $this->grade;
    }
}
