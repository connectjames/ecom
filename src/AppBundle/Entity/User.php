<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Table(name="user")
 * @UniqueEntity(fields={"email"}, message="It looks like your already have an account!")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $createdAt;

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     * @ORM\Column(type="string", unique=true)
     */
    private $email;

    /**
     * The encoded password
     *
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * A non-persisted field that's used to create the encoded password.
     *
     * @Assert\NotBlank(groups={"Registration"})
     * @var string
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="json_array")
     */
    private $roles = [];

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=100)
     */
    private $firstName;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=100)
     */
    private $lastName;

    /**
     * @ORM\Column(type="text")
     */
    private $invoiceAddress;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $deliveryAddress;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="boolean", options={"default":1}, nullable=true)
     */
    private $newsletter;

    /**
     * @ORM\Column(type="decimal", scale=2, nullable=true)
     */
    private $discount;

    /**
     * @ORM\OneToMany(targetEntity="Order", mappedBy="user", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $orders;

    /**
     * @ORM\ManyToMany(targetEntity="Product")
     * @ORM\JoinTable(name="whishlist_products")
     * * @ORM\OrderBy({"name" = "ASC"})
     */
    private $whishlistProducts;


    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function getRoles()
    {
        $roles = $this->roles;

        // give everyone ROLE_USER!
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }

        return $roles;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        // forces the object to look "dirty" to Doctrine. Avoids
        // Doctrine *not* saving this entity, if only plainPassword changes
        $this->password = null;
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getId()
    {
        return $this->id;
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

    public function setInvoiceAddress($invoiceAddress)
    {
        $this->invoiceAddress = $invoiceAddress;

        return $this;
    }

    public function getInvoiceAddress()
    {
        return json_decode($this->invoiceAddress, true);
    }

    public function setDeliveryAddress($deliveryAddress)
    {
        $this->deliveryAddress = json_encode($deliveryAddress);
    }

    public function getDeliveryAddress()
    {
        return json_decode($this->deliveryAddress, true);
    }

    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getDiscount()
    {
        return $this->discount;
    }

    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }

    public function setNewsletter($newsletter)
    {
        $this->newsletter = $newsletter;

        return $this;
    }

    public function getNewsletter()
    {
        return $this->newsletter;
    }

    /**
     * Set delivery
     *
     * @param \AppBundle\Entity\Delivery $delivery
     *
     * @return User
     */
    public function setDelivery(Delivery $delivery = null)
    {
        $this->delivery = $delivery;

        return $this;
    }

    /**
     * Get delivery
     *
     * @return \AppBundle\Entity\Delivery
     */
    public function getDelivery()
    {
        return $this->delivery;
    }

    /**
     * Add order
     *
     * @param \AppBundle\Entity\Order $order
     *
     * @return User
     */
    public function addOrder(Order $order)
    {
        $this->orders[] = $order;

        return $this;
    }

    /**
     * Remove order
     *
     * @param \AppBundle\Entity\Order $order
     */
    public function removeOrder(Order $order)
    {
        $this->orders->removeElement($order);
    }

    /**
     * Get orders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * Add whishlistProduct
     *
     * @param \AppBundle\Entity\Product $whishlistProduct
     *
     * @return User
     */
    public function addWhishlistProduct(Product $product)
    {
        if ($this->whishlistProducts->contains($product)) {
            return;
        }

        $this->whishlistProducts[] = $product;

        return $this;
    }

    /**
     * Remove whishlistProduct
     *
     * @param \AppBundle\Entity\Product $whishlistProduct
     */
    public function removeWhishlistProduct(Product $product)
    {
        $this->whishlistProducts->removeElement($product);
    }

    /**
     * Get whishlistProducts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWhishlistProducts()
    {
        return $this->whishlistProducts;
    }
}
