<?php
namespace AppBundle\Entity;

/*used to contact db*/
use Doctrine\ORM\Mapping as ORM;

/*used sub class with OneToMany relation*/
use AppBundle\Entity\SubFamily;

/*used to store the notes*/
use Doctrine\Common\Collections\ArrayCollection;

/*Next, we'll add validation rules - 
 * called constraints - above each property.
 * You don't apply validation to your form. 
 * Instead, you add validation to the class 
 * that is bound to your form*/
use Symfony\Component\Validator\Constraints as Assert;


/*let Doctrine know that:
 * it should use our own repository class (GenusRepository.php)
 * this class should map to a db table called genus*/
/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GenusRepository")
 * @ORM\Table(name="genus")
*/

class Genus
{
    
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
     * @Assert\NotBlank()
     */
    private $name;
    
    /*old
     * ORM\Column(type="string")
     */
    //private $subFamily;
    
    /*many Genus one SubFamily*/
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SubFamily")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $subFamily;
    
    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\Range(min=0, minMessage="Negative species? Come on...!")
     */
    private $speciesCount;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $funFact;
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $isPublished = true;
      
    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank()
     */
    private $firstDiscoveredAt;

    
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
    
    /**/
    public function getUpdatedAt()
    {
        return new \DateTime('-'.rand(0, 100).' days');
    }
    
    function getId() {
        return $this->id;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function setName($name) {
        $this->name = $name;
    }
    
    public function getSubFamily() {
        return $this->subFamily;
    }
    
    public function setSubFamily(SubFamily $subFamily) {
        $this->subFamily = $subFamily;
    }

    public function getSpeciesCount() {
        return $this->speciesCount;
    }
    
    public function setSpeciesCount($speciesCount) {
        $this->speciesCount = $speciesCount;
    }

    public function getFunFact() {
        //return '**TEST** ' .$this->funFact;
        return $this->funFact;
    }
    
    public function setFunFact($funFact) {
        $this->funFact = $funFact;
    }

    public function getIsPublished() {
        return $this->isPublished;
    }

    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;
    }
    
    public function getFirstDiscoveredAt() {
        return $this->firstDiscoveredAt;
    }

    public function setFirstDiscoveredAt(\DateTime $firstDiscoveredAt = null) {
        $this->firstDiscoveredAt = $firstDiscoveredAt;
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