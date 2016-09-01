<?php
namespace AppBundle\Form;

/*used libraries to build this class*/
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/*used libraries to customize form fields*/
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

/*used to sort subfamilies in dropdown list*/
use AppBundle\Entity\SubFamily;
use AppBundle\Repository\SubFamilyRepository;


/*Really, these classes are "form recipes".
 * Here's how it works: 
 * in buildForm(): start adding fields: 
 * $builder->add() and then name to create a "name" field:
 */
class GenusFormType extends AbstractType
{
     /*rules to build a form with the fields 
      * name, speciesCount, funFact*/
    public function buildForm(FormBuilderInterface $builder, array $options)
    {      
        /*As soon as you bind your form to a class,
         * - with configurOptions() below - 
         * name, speciesCount and funFact 
         * need to match property names inside of your class.
         * These properties are private, so the form component 
         * can't set them directly. In reality, it guesses 
         * a setter function for each field and call that: 
         * setName(), setSpeciesCount() and setFunFact()
         * 
         * the form system looks at each field and tries to guess 
         * what type of field it should be. For example, for subFamily, 
         * it sees that this is a ManyToOne relationship to SubFamily
         * So, it tries to render this as a select drop-down of sub families. 
         * That's amazing, because it's exactly what we want.
         * 
         * But, it needs to be able to turn a SubFamily object into a string
         * so it can render the text for each option in the select. 
         * That's the source of the error. To help it, 
         * add a public function __toString() to the SubFamily class:
         * 
         * A free drop-down with almost no work. 
         * It also noticed that isPublished should be a checkbox 
         * because that's a boolean field in Doctrine.
         * And since firstDiscoveredAt is a date, 
         * it rendered it with year-month-day drop-down boxes.
         * (Now, those three boxes are totally ugly and we'll fix it later)
         * 
         * Form type reference:
         * A description of all form types.
         * http://symfony.com/doc/current/reference/forms/types.html
         */
        $builder
            ->add('name')
            
            /*Default for a Doctrine entity is EntityType::class
             * but without placeholder. Add one.
             * Set the second argument null and it
             * will be correctly guessed together with
             * the required options. Or do it explicitly.
             */
            ->add('subFamily', EntityType::class, array(
                // query choices from this entity
                'class' => 'AppBundle:SubFamily',
                'placeholder' => 'Choose a Sub Family',
                'query_builder' => function(SubFamilyRepository $repo) {
                    return $repo->createAlphabeticalQueryBuilder();
                }
                // used to render a select box, check boxes or radios
                //'multiple' => true,
                //'expanded' => true,
            )) 
                
            ->add('speciesCount')
                    
            ->add('funFact') 
                    
            /*Default for type boolean is CheckboxType::class. 
             * But instead, I'd rather have a select drop-down 
             * with "yes" and "no" options which is ChoiceType::class.*/    
            ->add('isPublished', ChoiceType::class, [
                'choices' => [
                    'Yes' => true,
                    'No' => false,
                ]
            ])
                    
            /*Default for a date is DateType::class
             * with widget option: 
             * the three select fields, which is lame 
             * In new.html.twig template we have to add
             * links to datepicker css and js.
             * Some browsers have this functionality
             * as a html5 built-in. (Not firefox)
             * Therefore disable it with html5=>false 
             */    
            ->add('firstDiscoveredAt', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'js-datepicker'],
                'html5' => false,
            ])
        ;
    }
    
    /*Only here the entity is mentioned:
     * To get a brand-new Genus object that's just waiting to be saved. 
     * Thanks to the data_class option, the form creates a new Genus 
     * object behind the scenes. And then it sets the data on it.*/
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Genus'
        ]);
    }
}