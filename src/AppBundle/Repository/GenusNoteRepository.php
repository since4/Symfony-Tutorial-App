<?php
namespace AppBundle\Repository;

use AppBundle\Entity\GenusNote;
use AppBundle\Entity\Genus;
use Doctrine\ORM\EntityRepository;


/*To keep things organized, custom queries to the GenusNote table
 * should live in a GenusNoteRepository.
 * Doctrine doesn't know about this new repository class yet, 
 * so go tell it! In GenusNote, find @ORM\Entity and add 
 * repositoryClass="AppBundle\Repository\GenusNoteRepository"
 * Use the new methods in GenusController.
 */
class GenusNoteRepository extends EntityRepository
{

    /**
     * @param Genus $genus
     * @return GenusNote[]
     */
    public function findAllRecentNotesForGenus(Genus $genus)
    {
        
        /* Because we're in the GenusNoteRepository, 
         * the query already knows to select from that table. 
         * The 'genus_note' part is the table alias.
         */
        return $this->createQueryBuilder('genus_note')
            ->andWhere('genus_note.genus = :genus')
            ->setParameter('genus', $genus)
            ->andWhere('genus_note.createdAt > :recentDate')
            ->setParameter('recentDate', new \DateTime('-3 months'))
            ->orderBy('genus_note.createdAt', 'DESC')
            ->getQuery()
            ->execute();
    }
}

