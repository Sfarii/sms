<?php

namespace SMS\StudyPlanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Course
 *
 * @ORM\Table(name="course")
 * @ORM\Entity(repositoryClass="SMS\StudyPlanBundle\Repository\CourseRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"courseName", "grade" , "division" , "establishment"})
 */
class Course
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
     * @ORM\Column(name="courseName", type="string", length=150)
     * @Assert\NotBlank()
     * @Assert\Length(min = 2, max = 99)
     * @Assert\Regex(pattern="/^[a-z0-9 .\-]+$/i" ,match=true)
     */
    private $courseName;

    /**
     * @var float
     *
     * @ORM\Column(name="coefficient", type="float")
     * @Assert\NotBlank()
     * @Assert\Range(
     *      min = 0,
     *      max = 150)
     * @Assert\Regex("(\d+(?:,\d+)?)")
     */
    private $coefficient;

    /**
     * Many Courses have One Division.
     * @ORM\ManyToOne(targetEntity="SMS\EstablishmentBundle\Entity\Division",fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="division_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $division;

    /**
     * Many Courses have One Grade.
     * @ORM\ManyToOne(targetEntity="SMS\EstablishmentBundle\Entity\Grade" , inversedBy="courses",fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="grade_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $grade;

    /**
     * One Course has Many exams.
     * @ORM\OneToMany(targetEntity="Exam", mappedBy="course")
     */
    private $exams;

    /**
     * One Course has Many Schedule.
     * @ORM\OneToMany(targetEntity="Schedule", mappedBy="course")
     */
    private $schedules;

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
    protected $created;

    /**
     * @var datetime $updated
     *
     * @ORM\Column(type="datetime", nullable = true)
     */
    protected $updated;

    /**
     * One establishment has Many Courses.
     * @ORM\ManyToOne(targetEntity="SMS\EstablishmentBundle\Entity\Establishment" ,fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="establishment_id", referencedColumnName="id")
     */
    private $establishment;

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
     * Set courseName
     *
     * @param string $courseName
     * @return Course
     */
    public function setCourseName($courseName)
    {
        $this->courseName = $courseName;

        return $this;
    }

    /**
     * Get courseName
     *
     * @return string
     */
    public function getCourseName()
    {
        return $this->courseName;
    }

    /**
     * Set coefficient
     *
     * @param float $coefficient
     * @return Course
     */
    public function setCoefficient($coefficient)
    {
        $this->coefficient = $coefficient;

        return $this;
    }

    /**
     * Get coefficient
     *
     * @return float
     */
    public function getCoefficient()
    {
        return $this->coefficient;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->exams = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Course
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
     * @return Course
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
     * Set division
     *
     * @param \SMS\EstablishmentBundle\Entity\Division $division
     * @return Course
     */
    public function setDivision(\SMS\EstablishmentBundle\Entity\Division $division = null)
    {
        $this->division = $division;

        return $this;
    }

    /**
     * Get division
     *
     * @return \SMS\EstablishmentBundle\Entity\Division
     */
    public function getDivision()
    {
        return $this->division;
    }

    /**
     * Set grade
     *
     * @param \SMS\EstablishmentBundle\Entity\Grade $grade
     * @return Course
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

    /**
     * Add exams
     *
     * @param \SMS\StudyPlanBundle\Entity\Exam $exams
     * @return Course
     */
    public function addExam(\SMS\StudyPlanBundle\Entity\Exam $exams)
    {
        $this->exams[] = $exams;

        return $this;
    }

    /**
     * Remove exams
     *
     * @param \SMS\StudyPlanBundle\Entity\Exam $exams
     */
    public function removeExam(\SMS\StudyPlanBundle\Entity\Exam $exams)
    {
        $this->exams->removeElement($exams);
    }

    /**
     * Get exams
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getExams()
    {
        return $this->exams;
    }

    /**
     * Set user
     *
     * @param \SMS\UserBundle\Entity\User $user
     * @return Course
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
     * Set establishment
     *
     * @param \SMS\EstablishmentBundle\Entity\Establishment $establishment
     *
     * @return Course
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

}
