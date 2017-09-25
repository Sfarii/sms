<?php

namespace SMS\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PurchaseLine
 *
 * @ORM\Table(name="purchase_line")
 * @ORM\Entity
 */
class PurchaseLine
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
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * Many Products have One OrederLines.
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="purchases" ,fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

    /**
     * One order has Many OrederLine.
     * @ORM\ManyToOne(targetEntity="Purchase", inversedBy="purchaseLines",fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="purchase_id", referencedColumnName="id")
     */
    private $purchase;

    /**
     * @var bool
     *
     * @ORM\Column(name="state", type="boolean")
     */
    private $state;


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
     * Set price
     *
     * @param integer $price
     *
     * @return OrderLine
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return integer
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
     * @return OrderLine
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set product
     *
     * @param \SMS\StoreBundle\Entity\Product $product
     *
     * @return OrderLine
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
     * Set purchase
     *
     * @param \SMS\StoreBundle\Entity\Purchase $purchase
     *
     * @return PurchaseLine
     */
    public function setPurchase(\SMS\StoreBundle\Entity\Purchase $purchase = null)
    {
        $this->purchase = $purchase;

        return $this;
    }

    /**
     * Get purchase
     *
     * @return \SMS\StoreBundle\Entity\Purchase
     */
    public function getPurchase()
    {
        return $this->purchase;
    }

    /**
     * Set state
     *
     * @param boolean $state
     *
     * @return PurchaseLine
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
}
