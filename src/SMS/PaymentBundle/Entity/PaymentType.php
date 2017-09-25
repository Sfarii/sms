<?php

namespace SMS\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * PaymentType
 *
 * @ORM\Table(name="payment_type")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"typePaymentName" , "establishment"} , errorPath="typePaymentName")
 * @ORM\Entity(repositoryClass="SMS\PaymentBundle\Repository\PaymentTypeRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"payment_type" = "PaymentType", "catch_up_lesson" = "CatchUpLesson"})
 */
class PaymentType
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
     * @ORM\Column(name="typePaymentName", type="string", length=100)
     * @Assert\NotBlank()
     * @Assert\Length(min = 2, max = 99)
     * @Assert\Regex(pattern="/^[a-z0-9 .\-]+$/i" ,match=true)
     */
    protected $typePaymentName;

    /**
     * @var int
     *
     * @ORM\Column(name="price", type="float")
     * @Assert\NotBlank()
     * @Assert\Range(
     *      min = 0,
     *      max = 99999999999999)
     */
    protected $price;

    /**
     * @var int
     *
     * @ORM\Column(name="registrationFee", type="float")
     * @Assert\NotBlank()
     * @Assert\Range(
     *      min = 0,
     *      max = 99999999999999)
     */
    protected $registrationFee;

    /**
     * One PaymentType has Many Payments.
     * @ORM\OneToMany(targetEntity="Payment", mappedBy="paymentType",fetch="EXTRA_LAZY")
     */
    protected $payments;

    /**
     * One establishment has Many PaymentType.
     * @ORM\ManyToOne(targetEntity="SMS\EstablishmentBundle\Entity\Establishment" ,fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="establishment_id", referencedColumnName="id")
     */
    protected $establishment;


    /**
     * One User has Many Payments.
     * @ORM\ManyToOne(targetEntity="SMS\UserBundle\Entity\User" ,fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    protected $author;

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
     * One Payments has Many Users.
     * @ORM\ManyToMany(targetEntity="SMS\UserBundle\Entity\Student" , inversedBy="registrations" ,fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="payments_users",
     *      joinColumns={@ORM\JoinColumn(name="users_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="payments_id", referencedColumnName="id")}
     *      )
     */
    protected $student;

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
     * Constructor
     */
    public function __construct()
    {
        $this->payments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->student = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set typePaymentName
     *
     * @param string $typePaymentName
     *
     * @return PaymentType
     */
    public function setTypePaymentName($typePaymentName)
    {
        $this->typePaymentName = $typePaymentName;

        return $this;
    }

    /**
     * Get typePaymentName
     *
     * @return string
     */
    public function getTypePaymentName()
    {
        return $this->typePaymentName;
    }

    /**
     * Set price
     *
     * @param float $price
     *
     * @return PaymentType
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set registrationFee
     *
     * @param float $registrationFee
     *
     * @return PaymentType
     */
    public function setRegistrationFee($registrationFee)
    {
        $this->registrationFee = $registrationFee;

        return $this;
    }

    /**
     * Get registrationFee
     *
     * @return float
     */
    public function getRegistrationFee()
    {
        return $this->registrationFee;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return PaymentType
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
     * @return PaymentType
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
     * Add payment
     *
     * @param \SMS\PaymentBundle\Entity\Payment $payment
     *
     * @return PaymentType
     */
    public function addPayment(\SMS\PaymentBundle\Entity\Payment $payment)
    {
        $this->payments[] = $payment;

        return $this;
    }

    /**
     * Remove payment
     *
     * @param \SMS\PaymentBundle\Entity\Payment $payment
     */
    public function removePayment(\SMS\PaymentBundle\Entity\Payment $payment)
    {
        $this->payments->removeElement($payment);
    }

    /**
     * Get payments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * Set establishment
     *
     * @param \SMS\EstablishmentBundle\Entity\Establishment $establishment
     *
     * @return PaymentType
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

    /**
     * Set author
     *
     * @param \SMS\UserBundle\Entity\User $author
     *
     * @return PaymentType
     */
    public function setAuthor(\SMS\UserBundle\Entity\User $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \SMS\UserBundle\Entity\User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Add student
     *
     * @param \SMS\UserBundle\Entity\Student $student
     *
     * @return PaymentType
     */
    public function addStudent(\SMS\UserBundle\Entity\Student $student)
    {
        $this->student[] = $student;

        return $this;
    }

    /**
     * Remove student
     *
     * @param \SMS\UserBundle\Entity\Student $student
     */
    public function removeStudent(\SMS\UserBundle\Entity\Student $student)
    {
        $this->student->removeElement($student);
    }

    /**
     * Get student
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStudent()
    {
        return $this->student;
    }
}
