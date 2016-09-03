<?php
namespace AppBundle\Form;

/*used to build this class*/
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/*used to make form fields*/
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

/*used to bind the form to User*/
use AppBundle\Entity\User;


class UserRegistrationForm extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*form fields*/
        $builder
            ->add('email', EmailType::class)
            #type the password twice (RepeatedType)
            #if the values don't match, validation will automatically fail.
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class
            ]);
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        
        /*binds the form to User class*/
        //$resolver->setDefaults([
        //    'data_class' => User::class
        //]);
        
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['Default', 'Registration']
        ]);
    }
}

