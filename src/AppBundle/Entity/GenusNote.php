<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\Genus;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GenusNoteRepository")
 * @ORM\Table(name="genus_note")
 */
class GenusNote
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string")
     */
    private $username;
    
    /**
     * @ORM\Column(type="string")
     */
    private $userAvatarFilename;
    /**
     * @ORM\Column(type="text")
     */
    private $note;
    
    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;
    
    /*Foreign Key Column ($genus)
     *Many to one relationship of GenusNote to Genus:
     * Whenever you have a relation: 
     * start by figuring out which entity should have the foreign key column 
     * and then add the ManyToOne relationship there first. 
     * This is the only side of the relationship that you must have - 
     * it's called the "owning" side. Mapping the other side - 
     * the OneToMany inverse side - is always optional. 
     * I don't map it until I need to - 
     * either because I want a cute shortcut like $genus->getNotes() 
     * or because I want to join in a query from Genus to GenusNote.
     * 
     * After making the relationship bi-directional in Genus, we added:
     * inversedBy="notes", which is the inverse property in Genus*/
    /**
     * @ORM\ManyToOne(targetEntity="Genus", inversedBy="notes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $genus;
    
    function getId() {
        return $this->id;
    }
    public function getUsername()
    {
        return $this->username;
    }
    public function setUsername($username)
    {
        $this->username = $username;
    }
    public function getUserAvatarFilename()
    {
        return $this->userAvatarFilename;
    }
    public function setUserAvatarFilename($userAvatarFilename)
    {
        $this->userAvatarFilename = $userAvatarFilename;
    }
    public function getNote()
    {
        return $this->note;
    }
    public function setNote($note)
    {
        $this->note = $note;
    }
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }
    function getGenus() {
        return $this->genus;
    }
    function setGenus(Genus $genus) {
        $this->genus = $genus;
    }


}

