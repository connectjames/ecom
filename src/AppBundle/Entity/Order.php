<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OrderRepository")
 * @ORM\Table(name="order")
 */
class Order
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $_id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="orders")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $_user;

    /**
     * @ORM\Column(type="date")
     */
    private $_createdAt;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $_dispatchedAt;

    /**
     * @OneToOne(targetEntity="Basket")
     * @JoinColumn(name="basket_id", referencedColumnName="id")
     */
    private $_basket;

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     * @ORM\Column(type="string")
     */
    private $_email;

    /**
     * @OneToOne(targetEntity="AddressDetail")
     * @JoinColumn(name="addressDetail_id", referencedColumnName="id")
     */
    private $_invoiceAddress;

    /**
     * @ORM\ManyToOne(targetEntity="AddressDetail", inversedBy="orders")
     * @ORM\JoinColumn(name="addressDetail_id", referencedColumnName="id")
     */
    private $_deliveryAddress;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $_comment;

    /**
     * @ORM\ManyToOne(targetEntity="Status", inversedBy="orders")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    private $_status;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $_purchaseOrderNumber;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $_cardIdentifier;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $_token;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $_trackingNumber;

    public function getId()
    {
        return $this->_id;
    }

    public function setCreatedAt($createdAt)
    {
        $this->_createdAt = $createdAt;
    }

    public function getCreatedAt()
    {
        return $this->_createdAt;
    }

    public function setDispatchedAt($dispatchedAt)
    {
        $this->_dispatchedAt = $dispatchedAt;
    }

    public function getDispatchedAt()
    {
        return $this->_dispatchedAt;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->_email = $email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->_email;
    }

    /**
     * @param Delivery $deliveryAmount
     */
    public function setDeliveryAmount($deliveryAmount)
    {
        $this->deliveryAmount = $deliveryAmount;
    }

    /**
     * @return Delivery
     */
    public function getDeliveryAmount()
    {
        return $this->deliveryAmount;
    }

    public function getOrderAmount()
    {
        return $this->orderAmount;
    }

    public function setOrderAmount($orderAmount)
    {
        $this->orderAmount = $orderAmount;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    public function getFullName()
    {
        return trim($this->getFirstName().' '.$this->getLastName());
    }

    public function getInvoiceAddress()
    {
        return json_decode($this->invoiceAddress, true);
    }

    public function setInvoiceAddress($invoiceAddress)
    {
        $this->invoiceAddress = $invoiceAddress;
    }

    public function getDeliveryAddress()
    {
        return json_decode($this->deliveryAddress, true);
    }

    public function setDeliveryAddress($deliveryAddress)
    {
        $this->deliveryAddress = $deliveryAddress;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    public function getPurchaseOrderNumber()
    {
        return $this->purchaseOrderNumber;
    }

    public function setPurchaseOrderNumber($purchaseOrderNumber)
    {
        $this->purchaseOrderNumber = $purchaseOrderNumber;
    }

    public function getCardIdentifier()
    {
        return $this->cardIdentifier;
    }

    public function setCardIdentifier($cardIdentifier)
    {
        $this->cardIdentifier = $cardIdentifier;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function getTrackingNumber()
    {
        return $this->trackingNumber;
    }

    public function setTrackingNumber($trackingNumber)
    {
        $this->trackingNumber = $trackingNumber;
    }

    /**
     * Set user
     *
     * @param User $user
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set discount
     *
     * @param Discount $discount
     *
     * @return Order
     */
    public function setDiscount(\AppBundle\Entity\Discount $discount = null)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount
     *
     * @return \AppBundle\Entity\Discount
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set status
     *
     * @param \AppBundle\Entity\Status $status
     *
     * @return Order
     */
    public function setStatus(\AppBundle\Entity\Status $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \AppBundle\Entity\Status
     */
    public function getStatus()
    {
        return $this->status;
    }
}
