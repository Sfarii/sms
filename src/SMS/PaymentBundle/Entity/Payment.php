<?php

namespace SMS\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Payment
 *
 * @ORM\Table(name="payment")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="SMS\PaymentBundle\Repository\PaymentRepository")
 */
class Payment
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
     * @ORM\Column(name="reference", type="string", length=150)
     */
    protected $reference;

    /**
     * @var string
     *
     * @ORM\Column(name="month", type="string", length=2)
     * @Assert\Choice({"1" , "2" , "3" ,  "4" ,"5" , "6" , "7" ,"8" ,"9"  , "10"  ,"11"  , "12"})
     * @Assert\NotBlank()
     */
    private $month;

    /**
     * @var int
     *
     * @ORM\Column(name="price", type="float")
     * @Assert\NotBlank()
     * @Assert\Range(min = 0, max = 99999999999999)
     */
    private $price;

    /**
     * @var int
     *
     * @ORM\Column(name="credit", type="float" , nullable = true)
     */
    private $credit;

    /**
     * Many Payments have One PaymentType.
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="PaymentType", inversedBy="payments" ,fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="type_payment_id", referencedColumnName="id")
     */
    private $paymentType;

    /**
     * One Student has Many Payments.
     * @ORM\ManyToOne(targetEntity="SMS\UserBundle\Entity\Student" ,fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="student_id", referencedColumnName="id")
     */
    private $student;

    /**
     * One User has Many Payments.
     * @ORM\ManyToOne(targetEntity="SMS\UserBundle\Entity\User" ,fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    private $author;

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
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set month
     *
     * @param string $month
     *
     * @return Payment
     */
    public function setMonth($month)
    {
        $this->month = $month;

        return $this;
    }

    /**
     * Get month
     *
     * @return string
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Set price
     *
     * @param float $price
     *
     * @return Payment
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
     * Set credit
     *
     * @param float $credit
     *
     * @return Payment
     */
    public function setCredit($credit)
    {
        $this->credit = $credit;

        return $this;
    }

    /**
     * Get credit
     *
     * @return float
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Payment
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
     * @return Payment
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
     * Set paymentType
     *
     * @param \SMS\PaymentBundle\Entity\PaymentType $paymentType
     *
     * @return Payment
     */
    public function setPaymentType(\SMS\PaymentBundle\Entity\PaymentType $paymentType = null)
    {
        $this->paymentType = $paymentType;

        return $this;
    }

    /**
     * Get paymentType
     *
     * @return \SMS\PaymentBundle\Entity\PaymentType
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }

    /**
     * Set student
     *
     * @param \SMS\UserBundle\Entity\Student $student
     *
     * @return Payment
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
     * Set author
     *
     * @param \SMS\UserBundle\Entity\User $author
     *
     * @return Payment
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
     * Set reference
     *
     * @param string $reference
     *
     * @return Purchase
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }
}
