<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SubFamilyRepository")
 * @ORM\Table(name="sub_family")
 */
class SubFamily
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
    private $name;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    /* For the Form in GenusFormType.php:
     * to be able to turn a SubFamily object 
     * into a string so it can render the text 
     * for each option in the select.*/
    public function __toString()
    {
        return $this->getName();
    }
}