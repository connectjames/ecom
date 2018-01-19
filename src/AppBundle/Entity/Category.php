<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 * @ORM\Table(name="category")
 */
class Category
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $_id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=100)
     */
    private $_name;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $_sub;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $_description;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $_metaTitle;

    /**
     * @ORM\Column(type="string", length=160, nullable=true)
     */
    private $_metaDescription;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $_metaKeywords;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Gedmo\Slug(fields={"_name"})
     */
    private $_slug;

    /**
     * @ORM\Column(type="boolean", options={"default":true})
     */
    private $_display = true;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $_imageName;

    /**
     * @ORM\OneToMany(targetEntity="RelationCategoryProduct", mappedBy="category", fetch="EXTRA_LAZY", orphanRemoval=true, cascade={"persist"})
     * @Assert\Valid()
     */
    private $_relationCategoriesProducts;

    public function __construct()
    {
        $this->_relationCategoriesProducts = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param string $sub
     */
    public function setSub($sub)
    {
        $this->_sub = $sub;
    }

    /**
     * @return string
     */
    public function getSub()
    {
        return $this->_sub;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->_description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * @param string $metaTitle
     */
    public function setMetaTitle($metaTitle)
    {
        $this->_metaTitle = $metaTitle;
    }

    /**
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->_metaTitle;
    }

    /**
     * @param string $metaDescription
     */
    public function setMetaDescription($metaDescription)
    {
        $this->_metaDescription = $metaDescription;
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->_metaDescription;
    }

    /**
     * @param string $metaKeywords
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->_metaKeywords = $metaKeywords;
    }

    /**
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->_metaKeywords;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->_slug = $slug;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->_slug;
    }

    /**
     * @param boolean $display
     */
    public function setDisplay($display)
    {
        $this->_display = $display;
    }

    /**
     * @return boolean
     */
    public function getDisplay()
    {
        return $this->_display;
    }

    /**
     * @param string $imageName
     */
    public function setImageName($imageName)
    {
        $this->_imageName = $imageName;
    }

    /**
     * @return string
     */
    public function getImageName()
    {
        return $this->_imageName;
    }

    /**
     * @return ArrayCollection|RelationCategoryProduct[]
     */
    public function getProducts()
    {
        return $this->_relationCategoriesProducts;
    }

    public function addProduct(RelationCategoryProduct $product)
    {
        if ($this->_relationCategoriesProducts->contains($product)) {
            return;
        }

        $this->_relationCategoriesProducts[] = $product;
        // needed to update the owning side of the relationship!
        $product->setCategory($this);
    }

    public function removeProduct(RelationCategoryProduct $product)
    {
        if (!$this->_relationCategoriesProducts->contains($product)) {
            return;
        }
        $this->_relationCategoriesProducts->removeElement($product);

        // not needed for persistence, just keeping both sides in sync
        // needed to update the owning side of the relationship!
        $product->setCategory(null);
    }
}
