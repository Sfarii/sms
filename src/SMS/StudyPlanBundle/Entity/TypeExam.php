<?php

namespace SMS\StudyPlanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TypeExam
 *
 * @ORM\Table(name="type_exam")
 * @ORM\Entity(repositoryClass="SMS\StudyPlanBundle\Repository\TypeExamRepository")
 */
class TypeExam
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @var string
     *
     * @ORM\Column(name="type_exam_name", type="string", length=150)
     * @Assert\NotBlank()
     * @Assert\Length(min = 2, max = 149)
     * @Assert\Regex(pattern="/^[a-z0-9 .\-]+$/i" ,match=true)
     */
    private $typeExamName;

    /**
     * One TypeExam has Many Exams.
     * @ORM\OneToMany(targetEntity="Exam", mappedBy="typeExam",fetch="EXTRA_LAZY")
     */
    private $exams;

    /**
     * One User has Many Divivsions.
     * @ORM\ManyToOne(targetEntity="SMS\UserBundle\Entity\User" ,fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;


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
     * Constructor
     */
    public function __construct()
    {
        $this->exams = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set typeExamName
     *
     * @param string $typeExamName
     * @return TypeExam
     */
    public function setTypeExamName($typeExamName)
    {
        $this->typeExamName = $typeExamName;

        return $this;
    }

    /**
     * Get typeExamName
     *
     * @return string 
     */
    public function getTypeExamName()
    {
        return $this->typeExamName;
    }

    /**
     * Add exams
     *
     * @param \SMS\StudyPlanBundle\Entity\Exam $exams
     * @return TypeExam
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
     * @return TypeExam
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
}
