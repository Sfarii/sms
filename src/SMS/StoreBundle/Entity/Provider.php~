<?php

namespace SMS\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Provider
 *
 * @ORM\Table(name="provider")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="SMS\StoreBundle\Repository\ProviderRepository")
 */
class Provider
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
     * @ORM\Column(name="socialReason", type="string", length=150)
     * @Assert\NotBlank()
     */
    private $socialReason;

    /**
     * @var int
     *
     * @ORM\Column(name="phone", type="string", length=30)
     * @Assert\NotBlank()
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=200)
     * @Assert\NotBlank()
     */
    private $address;

    /**
     * @var int*
     *
     * @ORM\Column(name="fax", type="string", length=50)
     * @Assert\NotBlank()
     */
    private $fax;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=150,  unique=true)
     * @Assert\NotBlank()
     */
    private $email;

    /**
     * One establishment has Many Providers.
     * @ORM\ManyToOne(targetEntity="SMS\EstablishmentBundle\Entity\Establishment" ,fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="establishment_id", referencedColumnName="id")
     */
    private $establishment;

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
     * One User has Many providers.
     * @ORM\ManyToOne(targetEntity="SMS\UserBundle\Entity\User" ,fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $author;

    /**
     * One Provider has Many OrderProviders.
     * @ORM\OneToMany(targetEntity="OrderProvider", mappedBy="provider",fetch="EXTRA_LAZY")
     */
    private $orders;

    /**
     * One Provider has Many Purchases.
     * @ORM\OneToMany(targetEntity="Purchase", mappedBy="provider",fetch="EXTRA_LAZY")
     */
    private $purchases;



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
     * Set socialReason
     *
     * @param string $socialReason
     *
     * @return Provider
     */
    public function setSocialReason($socialReason)
    {
        $this->socialReason = $socialReason;

        return $this;
    }

    /**
     * Get socialReason
     *
     * @return string
     */
    public function getSocialReason()
    {
        return $this->socialReason;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Provider
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Provider
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set fax
     *
     * @param integer $fax
     *
     * @return Provider
     */
    public function setFax($fax)
    {
        $this->fax = $fax;

        return $this;
    }

    /**
     * Get fax
     *
     * @return integer
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Provider
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Provider
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
     * @return Provider
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
     * Set establishment
     *
     * @param \SMS\EstablishmentBundle\Entity\Establishment $establishment
     *
     * @return Provider
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
     * @return Provider
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
     * Constructor
     */
    public function __construct()
    {
        $this->orders = new \Doctrine\Common\Collections\ArrayCollection();
        $this->purchases = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add order
     *
     * @param \SMS\StoreBundle\Entity\OrderProvider $order
     *
     * @return Provider
     */
    public function addOrder(\SMS\StoreBundle\Entity\OrderProvider $order)
    {
        $this->orders[] = $order;

        return $this;
    }

    /**
     * Remove order
     *
     * @param \SMS\StoreBundle\Entity\OrderProvider $order
     */
    public function removeOrder(\SMS\StoreBundle\Entity\OrderProvider $order)
    {
        $this->orders->removeElement($order);
    }

    /**
     * Get orders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * Add purchase
     *
     * @param \SMS\StoreBundle\Entity\Purchase $purchase
     *
     * @return Provider
     */
    public function addPurchase(\SMS\StoreBundle\Entity\Purchase $purchase)
    {
        $this->purchases[] = $purchase;

        return $this;
    }

    /**
     * Remove purchase
     *
     * @param \SMS\StoreBundle\Entity\Purchase $purchase
     */
    public function removePurchase(\SMS\StoreBundle\Entity\Purchase $purchase)
    {
        $this->purchases->removeElement($purchase);
    }

    /**
     * Get purchases
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPurchases()
    {
        return $this->purchases;
    }
}
