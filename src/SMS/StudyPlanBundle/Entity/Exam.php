<?php

namespace SMS\StudyPlanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Exam
 *
 * @ORM\Table(name="exam")
 * @ORM\Entity(repositoryClass="SMS\StudyPlanBundle\Repository\ExamRepository")
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
     * @var float
     *
     * @ORM\Column(name="factor", type="float")
     */
    private $factor;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateExam", type="date")
     */
    private $dateExam;

    /**
     * Many Exams have One TypeExam.
     * @ORM\ManyToOne(targetEntity="TypeExam", inversedBy="exams")
     * @ORM\JoinColumn(name="type_exam_id", referencedColumnName="id")
     */
    private $typeExam;

    /**
     * Many Exams have One Session.
     * @ORM\ManyToOne(targetEntity="Session")
     * @ORM\JoinColumn(name="session_id", referencedColumnName="id")
     */
    private $session;

    /**
     * Many Exams have One Course.
     * @ORM\ManyToOne(targetEntity="Course", inversedBy="exams")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    private $course;

    /**
     * One Exam has Many Notes.
     * @ORM\OneToMany(targetEntity="Note", mappedBy="exam")
     */
    private $notes;


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
}
