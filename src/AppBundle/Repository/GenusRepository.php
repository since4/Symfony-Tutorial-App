<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Genus;
use Doctrine\ORM\EntityRepository;

class GenusRepository extends EntityRepository
{
    /**
     * @return Genus[]
     */
    public function findAllPublishedOrderedBySize()
    {
        
        /* Because we're in the GenusRepository, 
         * the query already knows to select from that table. 
         * The 'genus' part is the table alias.
         * The :isPublished looks weird - it's a parameter, 
         * like a placeholder. To fill it in, 
         * add ->setParameter('isPublished', true)
         * We always set variables like this using parameters 
         * to avoid SQL injection attacks. 
         * Never concatenate strings in a query.
         */
        return $this->createQueryBuilder('genus')
            ->andWhere('genus.isPublished = :isPublished')
            ->setParameter('isPublished', true)
            ->orderBy('genus.speciesCount', 'DESC')
            ->getQuery()
            ->execute();
    }
}