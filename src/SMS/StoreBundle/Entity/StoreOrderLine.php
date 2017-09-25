<?php

namespace SMS\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
  * StoreOrderLine
  *
  * @ORM\Table(name="store_order_line")
  * @ORM\HasLifecycleCallbacks
  * @ORM\Entity(repositoryClass="SMS\StoreBundle\Repository\StoreOrderRepository")
  */
class StoreOrderLine
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
   * @ORM\ManyToOne(targetEntity="Product" , inversedBy="providersOrders" ,fetch="EXTRA_LAZY")
   * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
   */
  private $product;

  /**
   * One order has Many OrederLine.
   * @ORM\ManyToOne(targetEntity="OrderProvider", inversedBy="orderLines",fetch="EXTRA_LAZY")
   * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
   */
  private $orders;


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
     * @return StoreOrderLine
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
     * @return StoreOrderLine
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
     * @return StoreOrderLine
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
     * @param \SMS\StoreBundle\Entity\OrderProvider $orders
     *
     * @return StoreOrderLine
     */
    public function setOrders(\SMS\StoreBundle\Entity\OrderProvider $orders = null)
    {
        $this->orders = $orders;

        return $this;
    }

    /**
     * Get orders
     *
     * @return \SMS\StoreBundle\Entity\OrderProvider
     */
    public function getOrders()
    {
        return $this->orders;
    }
}
