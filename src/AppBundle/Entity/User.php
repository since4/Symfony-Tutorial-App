<?php
namespace AppBundle\Entity;

/*used to implement this interface*/
use Symfony\Component\Security\Core\User\UserInterface;

/*used to give users permissions*/
use Symfony\Component\Security\Core\Role\Role;

/*used for db access*/
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $email;
    
    /**
     * The encoded password
     *
     * @ORM\Column(type="string")
     */
    private $password;
    
    /**
     * A non-persisted field that's used to create the encoded password.
     *
     * @var string
     */
    private $plainPassword;
    
    // needed by the security system
    public function getUsername()
    {
        return $this->email;
    }
    
    public function getRoles()
    {
        return ['ROLE_USER'];
    }
    
    public function getPassword()
    {
        // leaving blank - in case I don't need/have a password!
        return $this->password;
    }
    
    public function getSalt()
    {
        // leaving blank - in case I don't need/have a password!
        /* we're going to use the bcrypt algorithm, 
         * which has a built-in mechanism to salt passwords.*/
    }
    
    public function eraseCredentials()
    {
        // leaving blank - in case I don't need/have a password!
        
        /*Symfony calls this after logging in, 
         * and it's just a minor security measure 
         * to prevent the plain-text password 
         * from being accidentally saved anywhere.*/
        $this->plainPassword = null;
    }
    
    public function getEmail()
    {
        return $this->email;
    }
    
    public function setEmail($email)
    {
        $this->email = $email;
    }
    
    public function setPassword($password)
    {
        $this->password = $password;
    }
    
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }
    
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        
        /*Soon, we'll use a Doctrine listener 
         * to read the plainPassword property, 
         * encode it, and update password. 
         * That means that password will be set to a value 
         * before it actually saves: it won't remain null. 
         * So why add this weird line if it basically does nothing? 
         * Because Doctrine listeners are not called 
         * if Doctrine thinks that an object has not been updated. 
         * If you eventually create a "change password" form, 
         * then the only property that will be updated is plainPassword. 
         * Since this is not persisted, 
         * Doctrine will think the object is "un-changed", or "clean". 
         * In that case, the listeners will not be called, 
         * and the password will not be changed. 
         * But by adding this line, 
         * the object will always look like it has been changed, 
         * and life will go on like normal.*/

        // forces the object to look "dirty" to Doctrine. Avoids
        // Doctrine *not* saving this entity, if only plainPassword changes
        $this->password = null;
    }
}