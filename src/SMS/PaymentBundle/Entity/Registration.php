<?php

namespace SMS\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Registration
 *
 * @ORM\Table(name="registration")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={ "registrationType" , "student", "establishment"}, errorPath="student")
 * @ORM\Entity(repositoryClass="SMS\PaymentBundle\Repository\RegistrationRepository")
 */
class Registration
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
     * @ORM\Column(name="registered", type="boolean")
     */
    private $registered;

    /**
     * Many Registrations have One RegistrationType.
     * @ORM\ManyToOne(targetEntity="RegistrationType", inversedBy="registrations" ,fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="type_registration_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $registrationType;

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
     * One User has Many Registrations.
     * @ORM\ManyToOne(targetEntity="SMS\UserBundle\Entity\User" ,fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * One Student has Many Registrations.
     * @ORM\ManyToOne(targetEntity="SMS\UserBundle\Entity\Student" ,fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="student_id", referencedColumnName="id")
     */
    private $student;

    /**
     * One establishment has Many Registration.
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set registered
     *
     * @param boolean $registered
     *
     * @return Registration
     */
    public function setRegistered($registered)
    {
        $this->registered = $registered;

        return $this;
    }

    /**
     * Get registered
     *
     * @return boolean
     */
    public function getRegistered()
    {
        return $this->registered;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Registration
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
     * @return Registration
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
     * Set registrationType
     *
     * @param \SMS\PaymentBundle\Entity\RegistrationType $registrationType
     *
     * @return Registration
     */
    public function setRegistrationType(\SMS\PaymentBundle\Entity\RegistrationType $registrationType = null)
    {
        $this->registrationType = $registrationType;

        return $this;
    }

    /**
     * Get registrationType
     *
     * @return \SMS\PaymentBundle\Entity\RegistrationType
     */
    public function getRegistrationType()
    {
        return $this->registrationType;
    }

    /**
     * Set user
     *
     * @param \SMS\UserBundle\Entity\User $user
     *
     * @return Registration
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
     * Set student
     *
     * @param \SMS\UserBundle\Entity\Student $student
     *
     * @return Registration
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
     * Set establishment
     *
     * @param \SMS\EstablishmentBundle\Entity\Establishment $establishment
     *
     * @return Registration
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
