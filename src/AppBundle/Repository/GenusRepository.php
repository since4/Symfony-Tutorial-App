<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Genus;
use Doctrine\ORM\EntityRepository;

/*To keep things organized, custom queries to the Genus table
 * should live in a GenusRepository.
 * Doctrine doesn't know about this new repository class yet, 
 * so go tell it! In Genus, find @ORM\Entity and add 
 * repositoryClass="AppBundle\Repository\GenusRepository"
 * Use the new methods in GenusController.
 */
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
    
    /**
     * @return Genus[]
     */
    public function findAllPublishedOrderedByRecentlyActive()
    {
        
        /*Joins and the Inverse Relation 
         * Remember, this is the optional, inverse side of the relationship: 
         * we added this for the convenience of being able 
         * to say $genus->getNotes().
         * The second reason you might decide to map 
         * the inverse side of the relation: 
         * it's required if you're doing a JOIN in this direction.
         * 
         * Actually, not true! it is possible to query over this join 
         * without mapping this side of the relationship, 
         * it just takes a little bit more work:
         * ->leftJoin(
         * 'AppBundle:GenusNote',
         * 'genus_note',
         * \Doctrine\ORM\Query\Expr\Join::WITH,
         * 'genus = genus_note.genus'
         * )
         * 
         * genus_note:
         * this is the alias we can use during the rest of the query 
         * to reference fields on the joined genus_note table.
         */
        return $this->createQueryBuilder('genus')
            ->andWhere('genus.isPublished = :isPublished')
            ->setParameter('isPublished', true)
            ->leftJoin('genus.notes', 'genus_note')
            ->orderBy('genus_note.createdAt', 'DESC')
            ->getQuery()
            ->execute();
    }
}