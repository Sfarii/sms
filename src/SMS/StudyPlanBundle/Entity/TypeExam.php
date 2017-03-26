<?php

namespace SMS\StudyPlanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="type_exam_name", type="string", length=150)
     */
    private $typeExamName;

    /**
     * One TypeExam has Many Exams.
     * @ORM\OneToMany(targetEntity="Exam", mappedBy="typeExam")
     */
    private $exams;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
