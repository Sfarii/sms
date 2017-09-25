<?php

namespace SMS\StoreBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"productName" , "establishment"} , errorPath="productName")
 * @Vich\Uploadable
 * @ORM\Entity(repositoryClass="SMS\StoreBundle\Repository\ProductRepository")
 */
class Product
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
     * @ORM\Column(name="productName", type="string", length=150)
     */
    private $productName;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=100, nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="sku", type="string", length=150 , nullable=true)
     */
    private $sku;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="active", type="boolean", length=150)
     */
    private $active;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var int
     *
     * @ORM\Column(name="stock", type="integer")
     */
    private $stock;
    private $quantity;



    /**
     * Many Products have One ProductType.
     * @ORM\ManyToOne(targetEntity="ProductType", inversedBy="products" ,fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="type_product_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $productType;

    /**
     * One establishment has Many Products.
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
     * One User has Many Payments.
     * @ORM\ManyToOne(targetEntity="SMS\UserBundle\Entity\User" ,fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $author;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="store_image", fileNameProperty="imageName")
     * @Assert\File(
     *     maxSize="1M",
     *     mimeTypes={"image/png", "image/jpeg", "image/pjpeg"}
     * )
     * @var File
     */
    protected $imageFile;

    /**
     * @ORM\Column(type="string", length=255 , nullable=true)
     *
     * @var string
     */
    protected $imageName;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * One Product has Many OrderLine.
     * @ORM\OneToMany(targetEntity="OrderLine", mappedBy="product",fetch="EXTRA_LAZY")
     */
    private $usersOrders;

    /**
     * One Product has Many StoreOrderLine.
     * @ORM\OneToMany(targetEntity="StoreOrderLine", mappedBy="product",fetch="EXTRA_LAZY")
     */
    private $providersOrders;

    /**
     * One Product has Many PurchaseLine.
     * @ORM\OneToMany(targetEntity="PurchaseLine", mappedBy="product",fetch="EXTRA_LAZY")
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
           $this->setActive(true);
           $this->setCreated(new \DateTime('now'));
       }
   }


    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return User
     */
    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @return File|null
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * {@inheritdoc}
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Product
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Product
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set sku
     *
     * @param string $sku
     *
     * @return Product
     */
    public function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Get sku
     *
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
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
     * Set productName
     *
     * @param string $productName
     *
     * @return Product
     */
    public function setProductName($productName)
    {
        $this->productName = $productName;

        return $this;
    }

    /**
     * Get productName
     *
     * @return string
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * Set price
     *
     * @param float $price
     *
     * @return Product
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
     * Set stock
     *
     * @param integer $stock
     *
     * @return Product
     */
    public function setStock($stock)
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * Get stock
     *
     * @return integer
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * Set Quantity
     *
     * @param integer $Quantity
     *
     * @return Product
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get Quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Product
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
     * @return Product
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
     * Set productType
     *
     * @param \SMS\StoreBundle\Entity\ProductType $productType
     *
     * @return Product
     */
    public function setProductType(\SMS\StoreBundle\Entity\ProductType $productType = null)
    {
        $this->productType = $productType;

        return $this;
    }

    /**
     * Get productType
     *
     * @return \SMS\StoreBundle\Entity\ProductType
     */
    public function getProductType()
    {
        return $this->productType;
    }

    /**
     * Set establishment
     *
     * @param \SMS\EstablishmentBundle\Entity\Establishment $establishment
     *
     * @return Product
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
     * @return Product
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
        $this->usersOrders = new \Doctrine\Common\Collections\ArrayCollection();
        $this->providersOrders = new \Doctrine\Common\Collections\ArrayCollection();
        $this->purchases = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add usersOrder
     *
     * @param \SMS\StoreBundle\Entity\OrderLine $usersOrder
     *
     * @return Product
     */
    public function addUsersOrder(\SMS\StoreBundle\Entity\OrderLine $usersOrder)
    {
        $this->usersOrders[] = $usersOrder;

        return $this;
    }

    /**
     * Remove usersOrder
     *
     * @param \SMS\StoreBundle\Entity\OrderLine $usersOrder
     */
    public function removeUsersOrder(\SMS\StoreBundle\Entity\OrderLine $usersOrder)
    {
        $this->usersOrders->removeElement($usersOrder);
    }

    /**
     * Get usersOrders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsersOrders()
    {
        return $this->usersOrders;
    }

    /**
     * Add providersOrder
     *
     * @param \SMS\StoreBundle\Entity\StoreOrderLine $providersOrder
     *
     * @return Product
     */
    public function addProvidersOrder(\SMS\StoreBundle\Entity\StoreOrderLine $providersOrder)
    {
        $this->providersOrders[] = $providersOrder;

        return $this;
    }

    /**
     * Remove providersOrder
     *
     * @param \SMS\StoreBundle\Entity\StoreOrderLine $providersOrder
     */
    public function removeProvidersOrder(\SMS\StoreBundle\Entity\StoreOrderLine $providersOrder)
    {
        $this->providersOrders->removeElement($providersOrder);
    }

    /**
     * Get providersOrders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProvidersOrders()
    {
        return $this->providersOrders;
    }

    /**
     * Add purchase
     *
     * @param \SMS\StoreBundle\Entity\PurchaseLine $purchase
     *
     * @return Product
     */
    public function addPurchase(\SMS\StoreBundle\Entity\PurchaseLine $purchase)
    {
        $this->purchases[] = $purchase;

        return $this;
    }

    /**
     * Remove purchase
     *
     * @param \SMS\StoreBundle\Entity\PurchaseLine $purchase
     */
    public function removePurchase(\SMS\StoreBundle\Entity\PurchaseLine $purchase)
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
