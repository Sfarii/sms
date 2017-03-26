<?php

namespace SMS\StudyPlanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Schedule
 *
 * @ORM\Table(name="schedule")
 * @ORM\Entity(repositoryClass="SMS\StudyPlanBundle\Repository\ScheduleRepository")
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
     * @ORM\ManyToOne(targetEntity="SMS\EstablishmentBundle\Entity\Section")
     * @ORM\JoinColumn(name="section_id", referencedColumnName="id")
     */
    private $section;

    /**
     * Many Schedules have One Day.
     * @ORM\ManyToOne(targetEntity="Day")
     * @ORM\JoinColumn(name="day_id", referencedColumnName="id")
     */
    private $day;

    /**
     * Many Schedules have One Session.
     * @ORM\ManyToOne(targetEntity="Session")
     * @ORM\JoinColumn(name="session_id", referencedColumnName="id")
     */
    private $session;

    /**
     * Many Schedules have One Course.
     * @ORM\ManyToOne(targetEntity="Course")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    private $course;

    /**
     * Many Schedules have One Professor.
     * @ORM\ManyToOne(targetEntity="SMS\UserBundle\Entity\Professor" , inversedBy="schedules")
     * @ORM\JoinColumn(name="professor_id", referencedColumnName="id")
     */
    private $professor;


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
