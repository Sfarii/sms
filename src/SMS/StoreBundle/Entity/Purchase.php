<?php

namespace SMS\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Purchase
 *
 * @ORM\Table(name="purchase")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="SMS\StoreBundle\Repository\PurchaseRepository")
 */
class Purchase
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
     * @ORM\Column(name="state", type="boolean")
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=150)
     */
    protected $reference;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="purchaseDate", type="datetime")
     */
    private $purchaseDate;

    /**
     * Many Provider have One purchase.
     * @ORM\ManyToOne(targetEntity="Provider"  , inversedBy="purchases",fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="provider_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $provider;

    /**
     * One order has Many Purchase Lines.
     * @ORM\OneToMany(targetEntity="PurchaseLine", mappedBy="purchase" ,fetch="EXTRA_LAZY")
     */
    protected $purchaseLines;

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
        $this->purchaseLines = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set state
     *
     * @param boolean $state
     *
     * @return Purchase
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

    /**
     * Set purchaseDate
     *
     * @param \DateTime $purchaseDate
     *
     * @return Purchase
     */
    public function setPurchaseDate($purchaseDate)
    {
        $this->purchaseDate = $purchaseDate;

        return $this;
    }

    /**
     * Get purchaseDate
     *
     * @return \DateTime
     */
    public function getPurchaseDate()
    {
        return $this->purchaseDate;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Purchase
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
     * @return Purchase
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
     * Set provider
     *
     * @param \SMS\StoreBundle\Entity\Provider $provider
     *
     * @return Purchase
     */
    public function setProvider(\SMS\StoreBundle\Entity\Provider $provider = null)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * Get provider
     *
     * @return \SMS\StoreBundle\Entity\Provider
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Add purchaseLine
     *
     * @param \SMS\StoreBundle\Entity\PurchaseLine $purchaseLine
     *
     * @return Purchase
     */
    public function addPurchaseLine(\SMS\StoreBundle\Entity\PurchaseLine $purchaseLine)
    {
        $this->purchaseLines[] = $purchaseLine;

        return $this;
    }

    /**
     * Remove purchaseLine
     *
     * @param \SMS\StoreBundle\Entity\PurchaseLine $purchaseLine
     */
    public function removePurchaseLine(\SMS\StoreBundle\Entity\PurchaseLine $purchaseLine)
    {
        $this->purchaseLines->removeElement($purchaseLine);
    }

    /**
     * Get purchaseLines
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPurchaseLines()
    {
        return $this->purchaseLines;
    }

    /**
     * Set author
     *
     * @param \SMS\UserBundle\Entity\User $author
     *
     * @return Purchase
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
     * @return Purchase
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
