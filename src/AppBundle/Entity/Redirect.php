<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zenstruck\RedirectBundle\Model\Redirect as BaseRedirect;

/**
 * @ORM\Entity
 * @ORM\Table(name="redirects")
 */
class Redirect extends BaseRedirect
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    public function getId()
    {
        return $this->id;
    }

}
