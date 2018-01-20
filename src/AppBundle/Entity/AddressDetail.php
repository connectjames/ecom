<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="address_detail")
 */
class AddressDetails
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $_id;

    /**
     * @ORM\Column(type="string")
     */
    private $_firstName;

    /**
     * @ORM\Column(type="string")
     */
    private $_lastName;

    /**
     * @ORM\Column(type="string")
     */
    private $_company;

    /**
     * @ORM\Column(type="string")
     */
    private $_addressFirstLine;

    /**
     * @ORM\Column(type="string")
     */
    private $_addressSecondLine;

    /**
     * @ORM\Column(type="string")
     */
    private $_city;

    /**
     * @ORM\Column(type="string")
     */
    private $_postcode;

    /**
     * @ORM\Column(type="string")
     */
    private $_phoneNumber;

    /**
     * @ORM\OneToMany(targetEntity="Order", mappedBy="address_detail")
     */
    private $orders;

    public function __construct()
    {
        $this->_orders = new ArrayCollection();
    }

    public function getId()
    {
        return $this->_id;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->_firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->_firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->_lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->_lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->_company;
    }

    /**
     * @param string $company
     */
    public function setCompany($company)
    {
        $this->_company = $company;
    }

    /**
     * @return string
     */
    public function getAddressFirstLine()
    {
        return $this->_addressFirstLine;
    }

    /**
     * @param string $addressFirstLine
     */
    public function setAddressFirstLine($addressFirstLine)
    {
        $this->_addressFirstLine = $addressFirstLine;
    }

    /**
     * @return string
     */
    public function getAddressSecondLine()
    {
        return $this->_addressSecondLine;
    }

    /**
     * @param string $addressSecondLine
     */
    public function setAddressSecondLine($addressSecondLine)
    {
        $this->_addressSecondLine = $addressSecondLine;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->_city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->_city = $city;
    }

    /**
     * @return string
     */
    public function getPostcode()
    {
        return $this->_postcode;
    }

    /**
     * @param string $postcode
     */
    public function setPostcode($postcode)
    {
        $postcode = strtoupper($postcode);

        $this->_postcode = $postcode;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->_phoneNumber;
    }

    /**
     * @param string $phoneNumber
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->_phoneNumber = $phoneNumber;
    }

    /**
     * Add order
     *
     * @param Order $order
     *
     * @return AddressDetails
     */
    public function addOrder(Order $order)
    {
        $this->_orders[] = $order;

        return $this;
    }

    /**
     * Remove order
     *
     * @param Order $order
     */
    public function removeOrder(Order $order)
    {
        $this->_orders->removeElement($order);
    }

    /**
     * Get orders
     *
     * @return Collection
     */
    public function getOrders()
    {
        return $this->_orders;
    }
}
