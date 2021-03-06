<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Geonames;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * LoadGeonames form.
 */
class LoadGeonames extends Fixture {

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em) {
        for ($i = 0; $i < 4; $i++) {
            $fixture = new Geonames();
            $fixture->setGeonameid($i);
            $fixture->setName('Name ' . $i);
            $fixture->setAsciiname('Asciiname ' . $i);
            $fixture->setAlternatenames('Alternatenames ' . $i);
            $fixture->setLatitude(40 + $i / 10);
            $fixture->setLongitude(50 + $i / 10);
            $fixture->setFclass('F');
            $fixture->setFcode('Fcode ' . $i);
            $fixture->setCountry('C' . $i);
            $fixture->setCc2('Cc2 ' . $i);
            $fixture->setAdmin1('Admin1 ' . $i);
            $fixture->setAdmin2('Admin2 ' . $i);
            $fixture->setAdmin3('Admin3 ' . $i);
            $fixture->setAdmin4('Admin4 ' . $i);
            $fixture->setPopulation(($i + 1) * 10000);
            $fixture->setElevation($i);
            $fixture->setGtopo30($i);
            $fixture->setTimezone('Z+' . $i);
            $fixture->setModdate(new DateTime());

            $em->persist($fixture);
            $this->setReference('geonames.' . $i, $fixture);
        }

        $em->flush();
    }

}
