<?php

namespace SMS\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * RegistrationType
 *
 * @ORM\Table(name="registration_type")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"registrationTypeName", "establishment"})
 * @ORM\Entity(repositoryClass="SMS\PaymentBundle\Repository\RegistrationTypeRepository")
 */
class RegistrationType
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
     * @ORM\Column(name="registrationTypeName", type="string", length=100, unique=true)
     */
    private $registrationTypeName;

    /**
     * @var int
     *
     * @ORM\Column(name="registrationFee", type="integer")
     */
    private $registrationFee;

    /**
     * Many Registrations have One RegistrationType.
     * @ORM\OneToMany(targetEntity="Registration", mappedBy="registrationType",fetch="EXTRA_LAZY")
     */
    private $registrations;

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
     * One User has Many Payments.
     * @ORM\ManyToOne(targetEntity="SMS\UserBundle\Entity\User" ,fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * One establishment has Many RegistrationType.
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
     * Set registrationTypeName
     *
     * @param string $registrationTypeName
     *
     * @return RegistrationType
     */
    public function setRegistrationTypeName($registrationTypeName)
    {
        $this->registrationTypeName = $registrationTypeName;

        return $this;
    }

    /**
     * Get registrationTypeName
     *
     * @return string
     */
    public function getRegistrationTypeName()
    {
        return $this->registrationTypeName;
    }

    /**
     * Set registrationFee
     *
     * @param integer $registrationFee
     *
     * @return RegistrationType
     */
    public function setRegistrationFee($registrationFee)
    {
        $this->registrationFee = $registrationFee;

        return $this;
    }

    /**
     * Get registrationFee
     *
     * @return int
     */
    public function getRegistrationFee()
    {
        return $this->registrationFee;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->registrations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return RegistrationType
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
     * @return RegistrationType
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
     * Add registration
     *
     * @param \SMS\PaymentBundle\Entity\Registration $registration
     *
     * @return RegistrationType
     */
    public function addRegistration(\SMS\PaymentBundle\Entity\Registration $registration)
    {
        $this->registrations[] = $registration;

        return $this;
    }

    /**
     * Remove registration
     *
     * @param \SMS\PaymentBundle\Entity\Registration $registration
     */
    public function removeRegistration(\SMS\PaymentBundle\Entity\Registration $registration)
    {
        $this->registrations->removeElement($registration);
    }

    /**
     * Get registrations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegistrations()
    {
        return $this->registrations;
    }

    /**
     * Set user
     *
     * @param \SMS\UserBundle\Entity\User $user
     *
     * @return RegistrationType
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
     * @return RegistrationType
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
