<?php
namespace AppBundle\Doctrine;

use AppBundle\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;


/*Here's the idea: 
 * we'll create a function that Doctrine will call 
 * whenever any entity is inserted or updated. 
 * That'll let us to do some work before that happens.
 */
class HashPasswordListener implements EventSubscriber
{
    
    /*Symfony comes with a built-in service 
     * that's really good at encoding passwords. 
     * It's called security.password_encoder 
     * and if you looked it up on debug:container, 
     * its class is UserPasswordEncoder
     * We'll register this as a service.
     */
    private $passwordEncoder;
    public function __construct(UserPasswordEncoder $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    
    /*These are two event names that Doctrine makes available. 
     * prePersist is called right before an entity is originally inserted. 
     * preUpdate is called right before an entity is updated.*/
    public function getSubscribedEvents()
    {
        return ['prePersist', 'preUpdate'];
    }
    
    /*When Doctrine calls this, 
     * it will pass you an object called LifecycleEventArgs, 
     * from the ORM namespace. 
     * This method will be called before any entity is inserted. 
     * How do we know what entity is being saved? 
     * With $entity = $args->getEntity(). 
     * Now, if this is not an instanceof User, 
     * just return and do nothing:*/
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        
        if (!$entity instanceof User) {
            return;
        }
        
        $this->encodePassword($entity);
    }
    
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        
        if (!$entity instanceof User) {
            return;
        }
        
        $this->encodePassword($entity);
        
        // necessary to force the update to see the change
        $em = $args->getEntityManager();
        $meta = $em->getClassMetadata(get_class($entity));
        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $entity);
    }
    
    /**
     * @param User $entity
     */
    private function encodePassword(User $entity)
    {
        if (!$entity->getPlainPassword()) {
            return;
        }
        
        $encoded = $this->passwordEncoder->encodePassword(
            $entity,
            $entity->getPlainPassword()
        );
        
        $entity->setPassword($encoded);
    }
}

