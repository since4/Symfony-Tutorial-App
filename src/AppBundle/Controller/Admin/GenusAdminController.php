<?php
namespace AppBundle\Controller\Admin;

/*to use Genus.php model*/
use AppBundle\Entity\Genus;

/*to load the form GenusFormType.php*/
use AppBundle\Form\GenusFormType;

/*to parse annotations*/
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/*used for @security annotations */
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*to extend Controller and use render() method*/
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/*used for the http Request object which holds the session data*/
use Symfony\Component\HttpFoundation\Request;

/*to lock down an entire controller
 * put the @Security() line above the controller
 * As soon as you do that, all of these endpoints are locked down.
 */
/**
 * @Route("/admin")
 * @Security("is_granted('ROLE_MANAGE_GENUS')")
 */
class GenusAdminController extends Controller
{
    /**/
    /**
     * @Route("/genus", name="admin_genus_list")
     */
    public function indexAction()
    {
        /*authorisation old
         */
        //if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
        //    throw $this->createAccessDeniedException('Access denied!');
        //}
        
        /*authorisation*/
        //$this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $genuses = $this->getDoctrine()
            ->getRepository('AppBundle:Genus')
            ->findAll();
        return $this->render('admin/genus/list.html.twig', array(
            'genuses' => $genuses
        ));
    }
    
    /*Form reachable at http://localhost:8001/admin/genus/new
     * But:
     * where is the Request object comming from?
     * Not as parameter from the URL.
     */
    /**
     * @Route("/genus/new", name="admin_genus_new")
     */
    public function newAction(Request $request)
    {
        /*create a form from file AppBundle/Form/GenusFormType.php
         * This is a shortcut method in the base Controller 
         * that calls a method on the form.factory service.
         * (tested: form.factory has no method createForm)
         * The ::class syntax is new in PHP 5.5
         * File>ProjectProperties>Sources>PHPVersion: PHP5.5
         */
        $form = $this->createForm(GenusFormType::class);
        
        /*ONLY HANDLES DATA ON POST (NOT GET):
         * Inspect the HTML in the browser
         * and check out the <form> element. 
         * Notice: this does not have an action attribute. 
         * This means that the form will submit right back 
         * to the same route and controller that renders it. 
         * (You can totally change this, but we won't.) 
         * In other words, this single action method newAction()
         * will be responsible for rendering the form 
         * and processing it when the request method is POST.
         * Next, to actually handle the submit, 
         * call $form->handleRequest() 
         * and pass it the $request object:
         * 
         * The $form knows what fields it has on it. 
         * So $form->handleRequest() goes out 
         * and grabs the post data off of the $request object 
         * for those specific fields and processes them. 
         * The confusing thing is that this 
         * only does this for POST requests. 
         * If this is a GET request - 
         * like the user simply navigated to the form - 
         * the handleRequest() method does nothing 
         * and our form renders just like it did before.
         */
        $form->handleRequest($request);
        /*if the form was just submitted, 
         * then we'll want to do something with that information, 
         * like save a new Genus to the database. 
         * Add an if statement:
         * if this is a POST request and if the form passed all validation
         */
        if ($form->isSubmitted() && $form->isValid()) {
            /* It dumps and associative array with the three fields 
             * we added to the form. That's so simple! 
             * We added 3 fields to the form, rendered them in a template, 
             * and got those three values as an associative array.*/
            //dump($form->getData());die;
            
            /*store form data input as new record into db*/
            $genus = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($genus);
            $em->flush();
            
            /*Adding a friendly (flash) message
             * Rendering the Flash Message 
             * All we need to do now is render the flash message. 
             * And the best place for this is in your base template. 
             * Because then, you can set a flash message, 
             * redirect to any other page, and it'll always show up.
             */
            //$this->addFlash('success', 'Genus created!');
            $this->addFlash(
                'success',
                sprintf('Genus created by you: %s!', 
                        $this->getUser()->getEmail())
            );
            
            /*Next, we always redirect after a successful form submit - 
             * ya know, to make sure that the user can't just refresh 
             * and re-post that data. That'd be lame.
             * Because redirectToRoute() returns a RedirectResponse, 
             * we're done!
             */
            return $this->redirectToRoute('admin_genus_list');
        }
        
        /*ONLY HANDLES DATA ON GET (NOT POST)
         * Return the view:
         * new.html.twig extends base.html.twig 
         * and overriddes its body part*/
        return $this->render('admin/genus/new.html.twig', [
            'genusForm' => $form->createView()
        ]);
    }
    
    
    /*How could we add default data to the form? 
     * Well, it turns out answering that question 
     * is exactly the same as answering the question: 
     * How do we create an edit form?
    */
    /**
     * @Route("/genus/{id}/edit", name="admin_genus_edit")
     */
    public function editAction(Request $request, Genus $genus)
    {
        $form = $this->createForm(GenusFormType::class, $genus);
        
        // only handles data on POST
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $genus = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($genus);
            $em->flush();
            $this->addFlash('success', 'Genus updated!');
            return $this->redirectToRoute('admin_genus_list');
        }
        
        return $this->render('admin/genus/edit.html.twig', [
            'genusForm' => $form->createView()
        ]);
}
}

