<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="menu")
 */
class Menu
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $_id;

    /**
     * @ORM\Column(type="integer", options={"default":1})
     */
    private $_parent = 1;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $_name;

    /**
     * @ORM\Column(type="string")
     */
    private $_routeName;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param integer $parent
     */
    public function setParent($parent)
    {
        $this->_parent = $parent;
    }

    /**
     * @return integer
     */
    public function getParent()
    {
        return $this->_parent;
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
     * @param string $routeName
     */
    public function setRouteName($routeName)
    {
        $this->_routeName = $routeName;
    }

    /**
     * @return string
     */
    public function getRouteName()
    {
        return $this->_routeName;
    }

    /**
     * @return boolean
     */
    public function doesMenuItemHaveParents()
    {
        if ($this->_parent == 1) {

            return false;

        }

        return true;
    }
}
