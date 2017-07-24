<?php

namespace SMS\SchoolBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Pricing
 *
 * @ORM\Table(name="pricing")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="SMS\SchoolBundle\Repository\PricingRepository")
 * @Gedmo\TranslationEntity(class="SMS\SchoolBundle\Entity\Translations\PricingTranslation")
 */
class Pricing
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
     * @var string
     *
     * @ORM\Column(name="unitPrice", type="string", length=100)
     * @Gedmo\Translatable
     */
    private $unitPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="pricingName", type="string", length=100)
     * @Gedmo\Translatable
     */
    private $pricingName;

    /**
     * One Pricing has Many Pricing Feature.
     * @ORM\OneToMany(targetEntity="PricingFeature", mappedBy="pricing")
     */
    private $pricingFeature;

    /**
     * One User has Many Pricing.
     * @ORM\ManyToOne(targetEntity="SMS\UserBundle\Entity\User" ,fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

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
     * @var ArrayCollection
     * * @Assert\Valid(deep = true)
     * @ORM\OneToMany(targetEntity="SMS\SchoolBundle\Entity\Translations\PricingTranslation", mappedBy="object", cascade={"persist", "remove"})
     */
    private $translations;

    public function __construct()
    {
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->pricingFeature = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Get translations.
     *
     * @return ArrayCollection
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Add translation.
     *
     * @param PostTranslation $translation
     *
     * @return $this
     */
    public function addTranslation($translation)
    {
        if (!$this->translations->contains($translation)) {
            $this->translations[] = $translation;
            $translation->setObject($this);
        }

        return $this;
    }

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
     * @return Pricing
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
     * Set unitPrice
     *
     * @param string $unitPrice
     *
     * @return Pricing
     */
    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    /**
     * Get unitPrice
     *
     * @return string
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     * Set pricingName
     *
     * @param string $pricingName
     *
     * @return Pricing
     */
    public function setPricingName($pricingName)
    {
        $this->pricingName = $pricingName;

        return $this;
    }

    /**
     * Get pricingName
     *
     * @return string
     */
    public function getPricingName()
    {
        return $this->pricingName;
    }

    /**
     * Set user
     *
     * @param \SMS\UserBundle\Entity\User $user
     *
     * @return Pricing
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
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Pricing
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
     * @return Pricing
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
     * Add pricingFeature
     *
     * @param \SMS\SchoolBundle\Entity\PricingFeature $pricingFeature
     *
     * @return Pricing
     */
    public function addPricingFeature(\SMS\SchoolBundle\Entity\PricingFeature $pricingFeature)
    {
        $this->pricingFeature[] = $pricingFeature;

        return $this;
    }

    /**
     * Remove pricingFeature
     *
     * @param \SMS\SchoolBundle\Entity\PricingFeature $pricingFeature
     */
    public function removePricingFeature(\SMS\SchoolBundle\Entity\PricingFeature $pricingFeature)
    {
        $this->pricingFeature->removeElement($pricingFeature);
    }

    /**
     * Get pricingFeature
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPricingFeature()
    {
        return $this->pricingFeature;
    }
}
