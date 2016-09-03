<?php
namespace AppBundle\Controller;

/*used to extend class and to call render() method*/
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/*used to make variable with type User*/
use AppBundle\Entity\User;

/*used to create form with schema UserRegistrationForm.php*/
use AppBundle\Form\UserRegistrationForm;

/*used to reroute*/
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/*used for http request object*/
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/register", name="user_register")
     */
    public function registerAction(Request $request)
    {
        $form = $this->createForm(UserRegistrationForm::class);
        
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            
            /*type hint a variable*/
            /** 
             * @var User $user 
             */
            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Welcome '.$user->getEmail());
            
            /*reroute*/
            //return $this->redirectToRoute('app_main_homepage_');
            
            /*instead of redirecting to the home page
             * log the new user in after registration
             * The third argument is the authenticator 
             * app.security.login_form_authenticator in service.yml
             * whose success behavior we want to mimic.
             * The last argument 
             * "main"
             * is something called a "provider key". 
             * It's a fancy term for the name of your firewall
             * in security.yml
             */
            return $this->get('security.authentication.guard_handler')
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $this->get('app.security.login_form_authenticator'),
                    'main'
                );
        }
        
        return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
