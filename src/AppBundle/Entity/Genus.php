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


}