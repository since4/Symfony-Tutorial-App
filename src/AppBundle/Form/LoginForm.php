<?php

namespace AppBundle\Form;

/*used to build this class*/
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/*used to add a password field*/
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

/*Really, these classes are "form recipes".
 * Here's how it works: 
 * in buildForm(): start adding fields: 
 * $builder->add() and then _username to create a "_username" field:
 */
class LoginForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('_username')
            ->add('_password', PasswordType::class)
        ;
    }
}

