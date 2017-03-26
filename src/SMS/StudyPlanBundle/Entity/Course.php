<?php

namespace SMS\StudyPlanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Course
 *
 * @ORM\Table(name="course")
 * @ORM\Entity(repositoryClass="SMS\StudyPlanBundle\Repository\CourseRepository")
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
     */
    private $courseName;

    /**
     * @var float
     *
     * @ORM\Column(name="coefficient", type="float")
     */
    private $coefficient;

    /**
     * Many Courses have One Division.
     * @ORM\ManyToOne(targetEntity="SMS\EstablishmentBundle\Entity\Division")
     * @ORM\JoinColumn(name="division_id", referencedColumnName="id")
     */
    private $division;

    /**
     * Many Courses have One Grade.
     * @ORM\ManyToOne(targetEntity="SMS\EstablishmentBundle\Entity\Grade")
     * @ORM\JoinColumn(name="grade_id", referencedColumnName="id")
     */
    private $grade;

    /**
     * One Course has Many exams.
     * @ORM\OneToMany(targetEntity="Exam", mappedBy="course")
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
}
