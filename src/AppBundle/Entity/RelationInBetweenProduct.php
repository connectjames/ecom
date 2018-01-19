<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="relation_in_between_products")
 */
class RelationInBetweenProduct
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="relationParentProducts")
     * @ORM\JoinColumn(name="parent_product_id", referencedColumnName="id")
     */
    private $parentProduct;

    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="relationChildrenProducts")
     * @ORM\JoinColumn(name="child_product_id", referencedColumnName="id")
     */
    private $childProduct;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="integer")
     */
    private $position;

    public function getId()
    {
        return $this->id;
    }

    public function setParentProduct(Product $parentProduct = null)
    {
        $this->parentProduct = $parentProduct;

        return $this;
    }

    public function getParentProduct()
    {
        return $this->parentProduct;
    }

    public function setChildProduct(Product $childProduct = null)
    {
        $this->childProduct = $childProduct;

        return $this;
    }

    public function getChildProduct()
    {
        return $this->childProduct;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition($position)
    {
        $this->position = $position;
    }
}