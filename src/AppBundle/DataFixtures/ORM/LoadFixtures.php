<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Status;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;

class LoadFixtures implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $objects = Fixtures::load(
            __DIR__.'/fixtures.yml',
            $manager,
            [
                'providers' => [$this]
            ]
        );
    }

    public function status()
    {
        $status = [
            'Approved'
        ];

        $key = array_rand($status);

        return $status[$key];
    }
}