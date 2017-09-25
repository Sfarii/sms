<?php

namespace SMS\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderLine
 *
 * @ORM\Table(name="order_line")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="SMS\StoreBundle\Repository\OrderLineRepository")
 */
class OrderLine
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
     * @ORM\ManyToOne(targetEntity="Product" , inversedBy="usersOrders" ,fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

    /**
     * One order has Many OrederLine.
     * @ORM\ManyToOne(targetEntity="OrderUser", inversedBy="orderLines",fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $orders;

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
     * @param float $price
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
     * @return float
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
     * Set state
     *
     * @param boolean $state
     *
     * @return OrderLine
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
     * Set orders
     *
     * @param \SMS\StoreBundle\Entity\OrderUser $orders
     *
     * @return OrderLine
     */
    public function setOrders(\SMS\StoreBundle\Entity\OrderUser $orders = null)
    {
        $this->orders = $orders;

        return $this;
    }

    /**
     * Get orders
     *
     * @return \SMS\StoreBundle\Entity\OrderUser
     */
    public function getOrders()
    {
        return $this->orders;
    }
}
