<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zenstruck\RedirectBundle\Model\NotFound as BaseNotFound;

/**
 * @ORM\Entity
 * @ORM\Table(name="not_found")
 */
class NotFound extends BaseNotFound
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
}
