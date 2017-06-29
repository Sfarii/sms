<?php

namespace SMS\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * OrderUser
 *
 * @ORM\Table(name="order_user")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="SMS\StoreBundle\Repository\OrderUserRepository")
 */
class OrderUser
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
     * @var int
     *
     * @ORM\Column(name="price", type="integer")
     */
    private $price;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var bool
     *
     * @ORM\Column(name="state", type="boolean")
     */
    private $state;

    /**
     * Many Products have One OrederUser.
     * @ORM\ManyToOne(targetEntity="Product" ,fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $product;

    /**
     * One Student has Many Order.
     * @ORM\ManyToOne(targetEntity="SMS\UserBundle\Entity\Student" ,fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="student_id", referencedColumnName="id")
     */
    private $student;

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
     * One establishment has Many Users orders.
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
     * Set price
     *
     * @param integer $price
     *
     * @return OrderUser
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return OrderUser
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set state
     *
     * @param boolean $state
     *
     * @return OrderUser
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return bool
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return OrderUser
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
     * @return OrderUser
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
     * Set product
     *
     * @param \SMS\StoreBundle\Entity\Product $product
     *
     * @return OrderUser
     */
    public function setProduct(\SMS\StoreBundle\Entity\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \SMS\StoreBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set student
     *
     * @param \SMS\UserBundle\Entity\Student $student
     *
     * @return OrderUser
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
     * Set user
     *
     * @param \SMS\UserBundle\Entity\User $user
     *
     * @return OrderUser
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
     * @return OrderUser
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
