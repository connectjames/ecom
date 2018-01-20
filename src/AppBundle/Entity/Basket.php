<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="basket")
 */
class Basket
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $_id;

    /**
     * @ORM\ManyToOne(targetEntity="Discount", inversedBy="orders")
     * @ORM\JoinColumn(name="discount_id", referencedColumnName="id")
     */
    private $_discount;

    /**
     * @OneToOne(targetEntity="Delivery")
     * @JoinColumn(name="delivery_id", referencedColumnName="id")
     */
    private $_delivery;

    /**
     * @ORM\OneToMany(targetEntity="Product", mappedBy="basket")
     */
    private $_products;

    public function __construct()
    {
        $this->_products = new ArrayCollection();
    }

    public function getId()
    {
        return $this->_id;
    }

    /**
     * @return string
     */
    public function getDiscount()
    {
        return $this->_discount;
    }

    /**
     * @param string $discount
     */
    public function setDiscount($discount)
    {
        $this->_discount = $discount;
    }

    /**
     * @return Delivery
     */
    public function getDelivery()
    {
        return $this->_delivery;
    }

    /**
     * @param Delivery $delivery
     */
    public function setDeliveryAmount($delivery)
    {
        $this->_delivery = $delivery;
    }

    /**
     * Add product
     *
     * @param Product $product
     *
     * @return Basket
     */
    public function addProduct(Product $product)
    {
        $this->_products[] = $product;

        return $this;
    }

    /**
     * Remove product
     *
     * @param Product $product
     */
    public function removeProduct(Product $product)
    {
        $this->_products->removeElement($product);
    }

    /**
     * Get products
     *
     * @return Collection
     */
    public function getProducts()
    {
        return $this->_products;
    }

    // Method which will be most likely needed

    public function getDeliveryTotalExclTax()
    {

    }

    public function getDeliveryTotalInclTax()
    {

    }

    public function getOrderTotalExclTax()
    {

    }

    public function getOrderTotalInclTax()
    {

    }
}
