<?php

namespace SMS\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * OrderProvider
 *
 * @ORM\Table(name="order_provider")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="SMS\StoreBundle\Repository\OrderProviderRepository")
 */
class OrderProvider
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
     * Many Provider have One OrederProvider.
     * @ORM\ManyToOne(targetEntity="Provider" ,fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="provider_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $provider;

    /**
     * One User has Many Payments.
     * @ORM\ManyToOne(targetEntity="SMS\UserBundle\Entity\User" ,fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * One orderProvider has Many OrderLines.
     * @ORM\OneToMany(targetEntity="OrderLine", mappedBy="orderProvider",fetch="EXTRA_LAZY")
     */
    private $orderLines;

    /**
     * One establishment has Many OrederLines.
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
     * Constructor
     */
    public function __construct()
    {
        $this->orderLines = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return OrderProvider
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
     * @return OrderProvider
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
     * @return OrderProvider
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
     * Set user
     *
     * @param \SMS\UserBundle\Entity\User $user
     *
     * @return OrderProvider
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
     * Add orderLine
     *
     * @param \SMS\StoreBundle\Entity\OrderLine $orderLine
     *
     * @return OrderProvider
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
     * Set establishment
     *
     * @param \SMS\EstablishmentBundle\Entity\Establishment $establishment
     *
     * @return OrderProvider
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
