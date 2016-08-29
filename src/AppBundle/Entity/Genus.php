<?php
namespace AppBundle\Entity;

/*used to contact db*/
use Doctrine\ORM\Mapping as ORM;


/*let Doctrine know that:
 * it should use our own repository class (GenusRepository.php)
 * this class should map to a db table called genus*/
/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GenusRepository")
 * @ORM\Table(name="genus")
*/

class Genus
{
    
    /**
     * @ORM\Column(type="boolean")
     */   
    private $isPublished = true;
    
    /*let Doctrine know about column id, 
     * it basically says that id is the primary key*/
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /*let Doctrine know about column name*/
    /**
     * @ORM\Column(type="string")
     */
    private $name;
    
    /**
     * @ORM\Column(type="string")
     */
    private $subFamily;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $speciesCount;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $funFact;
    
    /*The inverse side of the relationship:
     * One to Many relationship of genus to genus_notes
     * mappedBy="genus" is the property in GenusNote 
     * But don't get confused: 
     * there's still only one relation in the database: 
     * but now there are two ways to access the data on it: 
     * $genusNote->getGenus() and now $genus->getNotes()
     * 
     * Notice I did not add a setNotes() method to Genus. 
     * That's because you cannot set data on the inverse side: 
     * you can only set it on the owning side. 
     * In other words, $genusNote->setGenus() will work, 
     * but $genus->setNotes() would not work:    */ 
    /**
     * @ORM\OneToMany(targetEntity="GenusNote", mappedBy="genus")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $notes;
    
    /*__construct() property is invoked with: new Genus() 
     * in superseded method newAction() OR the now used LoadFixtures.php/
     */
    public function __construct()
    {
        /*initialize the notes property to a new ArrayCollection*/
        $this->notes = new ArrayCollection();
    }
    
    function getName() {
        return $this->name;
    }
    
    function getSubFamily() {
        return $this->subFamily;
    }

    function getSpeciesCount() {
        return $this->speciesCount;
    }

    function getFunFact() {
        return $this->funFact;
    }
    
    function setName($name) {
        $this->name = $name;
    }

    function setSubFamily($subFamily) {
        $this->subFamily = $subFamily;
    }

    function setSpeciesCount($speciesCount) {
        $this->speciesCount = $speciesCount;
    }

    function setFunFact($funFact) {
        $this->funFact = $funFact;
    }

    public function getUpdatedAt()
    {
        return new \DateTime('-'.rand(0, 100).' days');
    }
    
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;
    }
    
    /*annotation not necessary*/
    /**
     * @return ArrayCollection|GenusNote[]
    */
    public function getNotes()
    {
        return $this->notes;
    }

}