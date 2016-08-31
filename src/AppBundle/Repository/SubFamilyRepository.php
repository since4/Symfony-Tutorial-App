<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Genus;
use Doctrine\ORM\EntityRepository;

class SubFamilyRepository extends EntityRepository
{
    /*sort the records in ascending order 
     * for dropdown list in the form:
     * GenusFormType.php*/
    public function createAlphabeticalQueryBuilder()
    {
        return $this->createQueryBuilder('sub_family')
            ->orderBy('sub_family.name', 'ASC');
    }
}