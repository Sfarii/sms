<?php

namespace SMS\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * CatchUpLessonSechdule
 *
 * @ORM\Table(name="catch_up_lesson_sechdule")
 * @ORM\Entity(repositoryClass="SMS\PaymentBundle\Repository\CatchUpLessonSechduleRepository")
 * @ORM\HasLifecycleCallbacks
 */
class CatchUpLessonSechdule
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
     * @ORM\Column(name="day", type="string", length=50)
     * @Assert\Choice({"Sunday", "Monday", "Tuesday","Wednesday", "Thursday", "Friday","Saturday"})
     * @Assert\NotBlank()
     */
    private $day;

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
     * @Assert\Time()
     */
     private $endTime;

     /**
      * Many CatchUpLessonSechdule have One CatchUpLesson.
      * @ORM\ManyToOne(targetEntity="CatchUpLesson", inversedBy="schedules" ,fetch="EXTRA_LAZY")
      * @ORM\JoinColumn(name="schedule_id", referencedColumnName="id")
      * @Assert\NotBlank()
      */
     private $catchUpLesson;


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
     * Set day
     *
     * @param string $day
     *
     * @return CatchUpLessonSechdule
     */
    public function setDay($day)
    {
        $this->day = $day;

        return $this;
    }

    /**
     * Get day
     *
     * @return string
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Set startTime
     *
     * @param \DateTime $startTime
     *
     * @return CatchUpLessonSechdule
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
     * @return CatchUpLessonSechdule
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
     * Set catchUpLesson
     *
     * @param \SMS\PaymentBundle\Entity\CatchUpLesson $catchUpLesson
     *
     * @return CatchUpLessonSechdule
     */
    public function setCatchUpLesson(\SMS\PaymentBundle\Entity\CatchUpLesson $catchUpLesson = null)
    {
        $this->catchUpLesson = $catchUpLesson;

        return $this;
    }

    /**
     * Get catchUpLesson
     *
     * @return \SMS\PaymentBundle\Entity\CatchUpLesson
     */
    public function getCatchUpLesson()
    {
        return $this->catchUpLesson;
    }
}
