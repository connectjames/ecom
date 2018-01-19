<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OrderRepository")
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="orders")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\Column(type="date")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dispatchedAt;

    /**
     * @ORM\Column(type="text")
     */
    private $orderDescription;

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     * @ORM\Column(type="string")
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity="Discount", inversedBy="orders")
     * @ORM\JoinColumn(name="discount_id", referencedColumnName="id")
     */
    private $discount;

    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    private $deliveryAmount;

    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    private $orderAmount;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $lastName;

    /**
     * @ORM\Column(type="text")
     */
    private $invoiceAddress;

    /**
     * @ORM\Column(type="text")
     */
    private $deliveryAddress;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $purchaseOrderNumber;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $cardIdentifier;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $trackingNumber;

    /**
     * @ORM\ManyToOne(targetEntity="Status", inversedBy="orders")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    private $status;

    public function getId()
    {
        return $this->id;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getDispatchedAt()
    {
        return $this->dispatchedAt;
    }

    public function setDispatchedAt($dispatchedAt)
    {
        $this->dispatchedAt = $dispatchedAt;
    }

    public function getOrderDescription()
    {
        return json_decode($this->orderDescription, true);
    }

    public function setOrderDescription($orderDescription)
    {
        $this->orderDescription = $orderDescription;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getDeliveryAmount()
    {
        return $this->deliveryAmount;
    }

    public function setDeliveryAmount($deliveryAmount)
    {
        $this->deliveryAmount = $deliveryAmount;
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
     * @param \AppBundle\Entity\User $user
     *
     * @return Order
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set discount
     *
     * @param \AppBundle\Entity\Discount $discount
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
