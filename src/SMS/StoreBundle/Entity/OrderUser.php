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
   * @var string
   *
   * @ORM\Column(name="reference", type="string", length=150)
   */
  protected $reference;

  /**
   * One order has Many OrderLine.
   * @ORM\OneToMany(targetEntity="OrderLine", mappedBy="orders",fetch="EXTRA_LAZY")
   */
  protected $orderLines;

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
   * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
   */
  protected $author;

  /**
   * One establishment has Many Delivery.
   * @ORM\ManyToOne(targetEntity="SMS\EstablishmentBundle\Entity\Establishment" ,fetch="EXTRA_LAZY")
   * @ORM\JoinColumn(name="establishment_id", referencedColumnName="id")
   */
  protected $establishment;

    /**
     * One Student has Many Order.
     * @ORM\ManyToOne(targetEntity="SMS\UserBundle\Entity\User" ,fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $userOrder;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="orderDate", type="datetime")
     */
    private $orderDate;

    /**
     * @var bool
     *
     * @ORM\Column(name="state", type="boolean")
     */
    private $state;

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
        $this->orderLines = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set reference
     *
     * @param string $reference
     *
     * @return OrderUser
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
     * Set orderDate
     *
     * @param \DateTime $orderDate
     *
     * @return OrderUser
     */
    public function setOrderDate($orderDate)
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    /**
     * Get orderDate
     *
     * @return \DateTime
     */
    public function getOrderDate()
    {
        return $this->orderDate;
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
     * @return boolean
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Add orderLine
     *
     * @param \SMS\StoreBundle\Entity\OrderLine $orderLine
     *
     * @return OrderUser
     */
    public function addOrderLine(\SMS\StoreBundle\Entity\OrderLine $orderLine)
    {
        $this->orderLines[] = $orderLine;

        return $this;
    }

    /**
     * Remove orderLine
     *
     * @param \SMS\StoreBundle\Entity\OrderLine $orderLine
     */
    public function removeOrderLine(\SMS\StoreBundle\Entity\OrderLine $orderLine)
    {
        $this->orderLines->removeElement($orderLine);
    }

    /**
     * Get orderLines
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrderLines()
    {
        return $this->orderLines;
    }

    /**
     * Set author
     *
     * @param \SMS\UserBundle\Entity\User $author
     *
     * @return OrderUser
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

    /**
     * Set userOrder
     *
     * @param \SMS\UserBundle\Entity\User $userOrder
     *
     * @return OrderUser
     */
    public function setUserOrder(\SMS\UserBundle\Entity\User $userOrder = null)
    {
        $this->userOrder = $userOrder;

        return $this;
    }

    /**
     * Get userOrder
     *
     * @return \SMS\UserBundle\Entity\User
     */
    public function getUserOrder()
    {
        return $this->userOrder;
    }
}
