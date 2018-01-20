<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 * @ORM\Table(name="product")
 */
class Product
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string")
     */
    private $sku;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $metaTitle;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $metaDescription;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $metaKeywords;

    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    private $price;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Gedmo\Slug(fields={"name"})
     */
    private $slug;

    /**
     * @ORM\Column(type="integer", options={"default":1})
     */
    private $display = 1;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $imageName;

    /**
     * @ORM\Column(type="string", options={"default":"Video Available"})
     */
    private $videoTitle = "Video Available";

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $videoLink;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $shortDescription;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="integer")
     */
    private $weight;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private $featured = 0;

    /**
     * @ORM\Column(type="string", nullable=true, options={"default":"N/A"})
     */
    private $capacityTable;

    /**
     * @ORM\Column(type="string", nullable=true, options={"default":"N/A"})
     */
    private $contentsTable;

    /**
     * @ORM\Column(type="string", nullable=true, options={"default":"N/A"})
     */
    private $productCodeTable;

    /**
     * @ORM\Column(type="string", nullable=true, options={"default":"N/A"})
     */
    private $weightTable;

    /**
     * @ORM\Column(type="string", nullable=true, options={"default":"N/A"})
     */
    private $dimensionTable;

    /**
     * @ORM\Column(type="string", nullable=true, options={"default":"N/A"})
     */
    private $descriptionTable;

    /**
     * @ORM\Column(type="string", nullable=true, options={"default":"N/A"})
     */
    private $packQuantityTable;

    /**
     * @ORM\OneToMany(targetEntity="RelationCategoryProduct", mappedBy="product", fetch="EXTRA_LAZY", orphanRemoval=true, cascade={"persist"})
     * @Assert\Valid()
     */
    private $relationProductsCategories;

    /**
     * @ORM\ManyToOne(targetEntity="Dropshipper", inversedBy="products")
     * @ORM\JoinColumn(name="dropshipper_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $dropshipper;

    /**
     * @ORM\OneToMany(targetEntity="RelationInBetweenProduct", mappedBy="parentProduct", fetch="EXTRA_LAZY", orphanRemoval=true, cascade={"persist"})
     * @Assert\Valid()
     */
    private $relationParentProducts;

    /**
     * @ORM\OneToMany(targetEntity="RelationInBetweenProduct", mappedBy="childProduct", fetch="EXTRA_LAZY", orphanRemoval=true, cascade={"persist"})
     * @Assert\Valid()
     */
    private $relationChildrenProducts;

    /**
     * @ORM\ManyToOne(targetEntity="Basket", inversedBy="products")
     * @ORM\JoinColumn(name="basket_id", referencedColumnName="id")
     */
    private $_basket;

    public function __construct()
    {
        $this->relationProductsCategories = new ArrayCollection();
        $this->relationParentProducts = new ArrayCollection();
        $this->relationChildrenProducts = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
    }

    public function getSku()
    {
        return $this->sku;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;
    }

    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }

    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function getImageName()
    {
        return $this->imageName;
    }

    public function setImageName($imageName)
    {
        $this->imageName = $imageName;
    }

    public function getVideoTitle()
    {
        return $this->videoTitle;
    }

    public function setVideoTitle($videoTitle)
    {
        $this->videoTitle = $videoTitle;
    }

    public function getVideoLink()
    {
        return $this->videoLink;
    }

    public function setVideoLink($videoLink)
    {
        $this->videoLink = $videoLink;
    }

    public function setDisplay($display)
    {
        $this->display = $display;

        return $this;
    }

    public function getDisplay()
    {
        return $this->display;
    }

    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    public function getWeight()
    {
        return $this->weight;
    }

    public function getFeatured()
    {
        return $this->featured;
    }

    public function setFeatured($featured)
    {
        $this->featured = $featured;
    }

    public function getCapacityTable()
    {
        return $this->capacityTable;
    }

    public function setCapacityTable($capacityTable)
    {
        $this->capacityTable = $capacityTable;
    }

    public function getContentsTable()
    {
        return $this->contentsTable;
    }

    public function setContentsTable($contentsTable)
    {
        $this->contentsTable = $contentsTable;
    }

    public function getProductCodeTable()
    {
        return $this->productCodeTable;
    }

    public function setProductCodeTable($productCodeTable)
    {
        $this->productCodeTable = $productCodeTable;
    }

    public function getWeightTable()
    {
        return $this->weightTable;
    }

    public function setWeightTable($weightTable)
    {
        $this->weightTable = $weightTable;
    }

    public function getDimensionTable()
    {
        return $this->dimensionTable;
    }

    public function setDimensionTable($dimensionTable)
    {
        $this->dimensionTable = $dimensionTable;
    }

    public function getDescriptionTable()
    {
        return $this->descriptionTable;
    }

    public function setDescriptionTable($descriptionTable)
    {
        $this->descriptionTable = $descriptionTable;
    }

    public function getPackQuantityTable()
    {
        return $this->packQuantityTable;
    }

    public function setPackQuantityTable($packQuantityTable)
    {
        $this->packQuantityTable = $packQuantityTable;
    }

    /**
     * Get dropshipper
     *
     * @return \AppBundle\Entity\Dropshipper
     */
    public function getDropshipper()
    {
        return $this->dropshipper;
    }

    /**
     * Set dropshipper
     *
     * @param \AppBundle\Entity\Dropshipper $dropshipper
     *
     * @return Product
     */
    public function setDropshipper(Dropshipper $dropshipper = null)
    {
        $this->dropshipper = $dropshipper;

        return $this;
    }

    /**
     * @return ArrayCollection|RelationCategoryProduct[]
     */
    public function getCategories()
    {
        return $this->relationProductsCategories;
    }

    public function addCategory(RelationCategoryProduct $category)
    {
        if ($this->relationProductsCategories->contains($category)) {
            return;
        }

        $this->relationProductsCategories[] = $category;
        // needed to update the owning side of the relationship!
        $category->setProduct($this);
    }

    public function removeCategory(RelationCategoryProduct $category)
    {
        if (!$this->relationProductsCategories->contains($category)) {
            return;
        }
        $this->relationProductsCategories->removeElement($category);
        // not needed for persistence, just keeping both sides in sync
        // needed to update the owning side of the relationship!
        $category->setProduct(null);
    }

    /**
     * @return ArrayCollection|RelationInBetweenProduct[]
     */
    public function getParentProducts()
    {
        return $this->relationParentProducts;
    }

    public function addParentProduct(RelationInBetweenProduct $childProduct)
    {
        if ($this->relationChildrenProducts->contains($childProduct)) {
            return;
        }

        $this->relationChildrenProducts[] = $childProduct;
        // needed to update the owning side of the relationship!
        $childProduct->setParentProduct($this);
    }

    public function removeParentProduct(RelationInBetweenProduct $childProduct)
    {
        if (!$this->relationChildrenProducts->contains($childProduct)) {
            return;
        }
        $this->relationChildrenProducts->removeElement($childProduct);
        // not needed for persistence, just keeping both sides in sync
        // needed to update the owning side of the relationship!
        $childProduct->setParentProduct(null);
    }

    /**
     * @return ArrayCollection|RelationInBetweenProduct[]
     */
    public function getChildrenProducts()
    {
        return $this->relationChildrenProducts;
    }
}
