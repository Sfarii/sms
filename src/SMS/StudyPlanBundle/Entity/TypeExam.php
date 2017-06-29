<?php

namespace SMS\StudyPlanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * TypeExam
 *
 * @ORM\Table(name="type_exam")
 * @ORM\Entity(repositoryClass="SMS\StudyPlanBundle\Repository\TypeExamRepository")
 * @UniqueEntity(fields={"typeExamName" , "establishment"})
 * @ORM\HasLifecycleCallbacks
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
     * @var float
     *
     * @ORM\Column(name="factor", type="float")
     * @Assert\NotBlank()
     * @Assert\Range(
     *      min = 0,
     *      max = 1000)
     * @Assert\Regex("(\d+(?:,\d+)?)")
     */
    private $factor;

    /**
     * One TypeExam has Many Exams.
     * @ORM\OneToMany(targetEntity="Exam", mappedBy="typeExam",fetch="EXTRA_LAZY")
     */
    private $exams;

    /**
     * One User has Many TypeExams.
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
     * One establishment has Many TypeExams.
     * @ORM\ManyToOne(targetEntity="SMS\EstablishmentBundle\Entity\Establishment" ,fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="establishment_id", referencedColumnName="id")
     */
    private $establishment;

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
     * Set establishment
     *
     * @param \SMS\EstablishmentBundle\Entity\Establishment $establishment
     *
     * @return TypeExam
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
