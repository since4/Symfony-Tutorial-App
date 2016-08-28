<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Genus;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;

#the functionality is called only via php bin/console
class LoadFixtures implements FixtureInterface
{
    
    #call the fixtures generation with: 
    #   php bin/console doctrine:fixtures:load 
    #check the new db entries with: 
    #   php bin/console doctrine:query:sql 'SELECT * FROM genus'
    public function load(ObjectManager $manager)
    {
        
        #$genus = new Genus();
        #$genus->setName('Octopus'.rand(1, 100));
        #$genus->setSubFamily('Octopodinae');
        #$genus->setSpeciesCount(rand(100, 99999));
        #$manager->persist($genus);
        #$manager->flush();
        
        #add a third argument - it's sort of an "options" array. 
        #Give it a key called providers - these will be additional objects 
        #that provide formatter functions - and set it to an array with $this      
        $objects = Fixtures::load(
            __DIR__.'/fixtures.yml',
            $manager,
            [
                'providers' => [$this]
            ]
        );       
    }
    
    #function called in fixtures.yml
    public function genus()
    {
        $genera = [
            'Octopus',
            'Balaena',
            'Orcinus',
            'Hippocampus',
            'Asterias',
            'Amphiprion',
            'Carcharodon',
            'Aurelia',
            'Cucumaria',
            'Balistoides',
            'Paralithodes',
            'Chelonia',
            'Trichechus',
            'Eumetopias'
        ];
        $key = array_rand($genera);
        return $genera[$key];
    }
}
