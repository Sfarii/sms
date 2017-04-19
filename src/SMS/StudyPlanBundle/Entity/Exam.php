<?php

namespace SMS\StudyPlanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Exam
 *
 * @ORM\Table(name="exam")
 * @ORM\Entity(repositoryClass="SMS\StudyPlanBundle\Repository\ExamRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Exam
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
     * @ORM\Column(name="exam_name", type="string", length=150)
     * @Assert\NotBlank()
     * @Assert\Length(min = 2, max = 99)
     * @Assert\Regex(pattern="/^[a-z0-9 .\-]+$/i" ,match=true)
     */
    private $examName;

    /**
     * @var float
     *
     * @ORM\Column(name="factor", type="float")
     * @Assert\NotBlank()
     * @Assert\Range(
     *      min = 0,
     *      max = 150)
     * @Assert\Regex("(\d+(?:,\d+)?)")
     */
    private $factor;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateExam", type="date")
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    private $dateExam;

    /**
     * Many Exams have One TypeExam.
     * @ORM\ManyToOne(targetEntity="TypeExam", inversedBy="exams" ,fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="type_exam_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $typeExam;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startTime", type="time")
     * @Assert\NotBlank()
     * @Assert\Time()
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endTime", type="time")
     * @Assert\NotBlank()
     * @Assert\Expression(expression="this.getStartTime() < value")
     * @Assert\Time()
     */
    private $endTime;

    /**
     * Many Exams have Many Section.
     * @ORM\ManyToMany(targetEntity="SMS\EstablishmentBundle\Entity\Section" ,fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="exams_section",
     *      joinColumns={@ORM\JoinColumn(name="exam_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="section_id", referencedColumnName="id")}
     *      )
     * @Assert\NotBlank()
     */
    private $section;

    /**
     * Many Exams have One Course.
     * @ORM\ManyToOne(targetEntity="Course", inversedBy="exams",fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $course;

    /**
     * One Exam has Many Notes.
     * @ORM\OneToMany(targetEntity="Note", mappedBy="exam",fetch="EXTRA_LAZY")
     */
    private $notes;

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
     * Set factor
     *
     * @param float $factor
     * @return Exam
     */
    public function setFactor($factor)
    {
        $this->factor = $factor;

        return $this;
    }

    /**
     * Get factor
     *
     * @return float
     */
    public function getFactor()
    {
        return $this->factor;
    }

    /**
     * Set dateExam
     *
     * @param \DateTime $dateExam
     * @return Exam
     */
    public function setDateExam($dateExam)
    {
        $this->dateExam = $dateExam;

        return $this;
    }

    /**
     * Get dateExam
     *
     * @return \DateTime
     */
    public function getDateExam()
    {
        return $this->dateExam;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->notes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->section = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Exam
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
     * @return Exam
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
     * Set typeExam
     *
     * @param \SMS\StudyPlanBundle\Entity\TypeExam $typeExam
     * @return Exam
     */
    public function setTypeExam(\SMS\StudyPlanBundle\Entity\TypeExam $typeExam = null)
    {
        $this->typeExam = $typeExam;

        return $this;
    }

    /**
     * Get typeExam
     *
     * @return \SMS\StudyPlanBundle\Entity\TypeExam
     */
    public function getTypeExam()
    {
        return $this->typeExam;
    }

    /**
     * Set course
     *
     * @param \SMS\StudyPlanBundle\Entity\Course $course
     * @return Exam
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
     * Add notes
     *
     * @param \SMS\StudyPlanBundle\Entity\Note $notes
     * @return Exam
     */
    public function addNote(\SMS\StudyPlanBundle\Entity\Note $notes)
    {
        $this->notes[] = $notes;

        return $this;
    }

    /**
     * Remove notes
     *
     * @param \SMS\StudyPlanBundle\Entity\Note $notes
     */
    public function removeNote(\SMS\StudyPlanBundle\Entity\Note $notes)
    {
        $this->notes->removeElement($notes);
    }

    /**
     * Get notes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set user
     *
     * @param \SMS\UserBundle\Entity\User $user
     * @return Exam
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
     * Set examName
     *
     * @param string $examName
     * @return Exam
     */
    public function setExamName($examName)
    {
        $this->examName = $examName;

        return $this;
    }

    /**
     * Get examName
     *
     * @return string
     */
    public function getExamName()
    {
        return $this->examName;
    }

    /**
     * Set startTime
     *
     * @param \DateTime $startTime
     *
     * @return Exam
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param \DateTime $endTime
     *
     * @return Exam
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Add section
     *
     * @param \SMS\EstablishmentBundle\Entity\Section $section
     *
     * @return Exam
     */
    public function addSection(\SMS\EstablishmentBundle\Entity\Section $section)
    {
        $this->section[] = $section;

        return $this;
    }

    /**
     * Remove section
     *
     * @param \SMS\EstablishmentBundle\Entity\Section $section
     */
    public function removeSection(\SMS\EstablishmentBundle\Entity\Section $section)
    {
        $this->section->removeElement($section);
    }

    /**
     * Get section
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSection()
    {
        return $this->section;
    }
}
