<?php
namespace AppBundle\Controller;

/*to extend Controller and use render() method*/
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/*to parse annotations*/
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/*to load the class LoginForm.php*/
use AppBundle\Form\LoginForm;

/*Every login looks about the same:
 * Code copied from:
 * http://symfony.com/doc/current/security/form_login_setup.html
 * Notice, one thing is immediately weird: 
 * there's no form processing code inside of here. 
 * Welcome to the strangest part of Symfony's security. 
 * We will build the login form here, 
 * but some other magic layer will actually handle the form submit. 
 * We'll build that layer next (LoginFormAuthenticator.php). 
 * But thanks to the handy security.authentication_utils service, 
 * we can at least grab any authentication error 
 * that may have just happened in that magic layer 
 * as well as the last username that was typed in, 
 * which will actually be an email address for us.
 */
class SecurityController extends Controller
{
    /**
     * @Route("/login", name="security_login")
     */
    public function loginAction()
    {
        
        /*Behind the scenes, the authenticator 
         * communicates to your SecurityController 
         * by storing things in the session. 
         * That's what the security.authentication_utils helps us with.
         * The login form is automatically setting 
         * the authentication error to the session for us. 
         * But, it is not setting the last username on the session:
         * Fix it in LoginFormAuthenticator.php, getCredentials method.
         */
        $authenticationUtils = $this->get('security.authentication_utils');
        
        /*this reads a Security::AUTHENTICATION_ERROR string key 
         * from the session*/
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        
        /*
        return $this->render(
            'security/login.html.twig',
            array(
                // last username entered by the user
                'last_username' => $lastUsername,
                'error'         => $error,
            )
        );*/
        
        $form = $this->createForm(LoginForm::class, [
            '_username' => $lastUsername,
        ]);
    
        return $this->render(
            'security/login.html.twig',
            array(
                'form' => $form->createView(),
                'error' => $error,
            )
        );
    }
    
    /*The controller will do nothing. 
     * Instead, Symfony will intercept any requests to 
     * /logout and take care of everything for us. 
     * To activate it, open security.yml 
     * and add a new key under your firewall
     * If you don't have a route that matches /logout, 
     * then the 404 page is triggered 
     * before Symfony has a chance to log the user out.
     */
    /**
     * @Route("/logout", name="security_logout")
     */
    public function logoutAction()
    {
        throw new \Exception('this should not be reached!');
    }
}
