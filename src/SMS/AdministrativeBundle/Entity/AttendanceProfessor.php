<?php

namespace SMS\AdministrativeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * AttendanceProfessor
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="attendance_professor")
 * @ORM\Entity(repositoryClass="SMS\AdministrativeBundle\Repository\AttendanceProfessorRepository")
 */
class AttendanceProfessor
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
     * @var bool
     *
     * @ORM\Column(name="status", type="string", length=200, nullable=true)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     * @Assert\Date()
     */
    private $date;

    /**
     * Many Attendances have One Professor.
     * @ORM\ManyToOne(targetEntity="SMS\UserBundle\Entity\Professor")
     * @ORM\JoinColumn(name="student_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $professor;

    /**
     * Many Attendances have One Session.
     * @ORM\ManyToOne(targetEntity="SMS\StudyPlanBundle\Entity\Session")
     * @ORM\JoinColumn(name="session_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $session;

    /**
     * Many Attendances have One Course.
     * @ORM\ManyToOne(targetEntity="SMS\StudyPlanBundle\Entity\Course",fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $course;

    /**
     * @var \DateTime $created
     *
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @var \DateTime $updated
     *
     * @ORM\Column(type="datetime", nullable = true)
     */
    protected $updated;

    /**
     * One User has Many Divivsions.
     * @ORM\ManyToOne(targetEntity="SMS\UserBundle\Entity\User" ,fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return AttendanceProfessor
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return AttendanceProfessor
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return AttendanceProfessor
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
     * @return AttendanceProfessor
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
     * Set student
     *
     * @param \SMS\UserBundle\Entity\Student $student
     *
     * @return AttendanceProfessor
     */
    public function setStudent(\SMS\UserBundle\Entity\Student $student = null)
    {
        $this->student = $student;

        return $this;
    }

    /**
     * Get student
     *
     * @return \SMS\UserBundle\Entity\Student
     */
    public function getStudent()
    {
        return $this->student;
    }

    /**
     * Set session
     *
     * @param \SMS\StudyPlanBundle\Entity\Session $session
     *
     * @return AttendanceProfessor
     */
    public function setSession(\SMS\StudyPlanBundle\Entity\Session $session = null)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Get session
     *
     * @return \SMS\StudyPlanBundle\Entity\Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Set user
     *
     * @param \SMS\UserBundle\Entity\User $user
     *
     * @return AttendanceProfessor
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
     * Set professor
     *
     * @param \SMS\UserBundle\Entity\Professor $professor
     *
     * @return AttendanceProfessor
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
     * Set course
     *
     * @param \SMS\StudyPlanBundle\Entity\Course $course
     *
     * @return AttendanceProfessor
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
}
