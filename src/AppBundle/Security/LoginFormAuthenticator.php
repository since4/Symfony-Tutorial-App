<?php

namespace AppBundle\Security;

/*used libraries to build this class*/
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Security;

/*used for the http Request object which holds the session data*/
use Symfony\Component\HttpFoundation\Request;

/*used in getCredentials() to hold the LoginForm.php*/
use Symfony\Component\Form\FormFactoryInterface;
use AppBundle\Form\LoginForm;

/*used to create EntityManager which is used to
 *  fetch the user table in db*/
use Doctrine\ORM\EntityManager;

/*used to set redirection route in 
 * getLoginUrl() and getDefaultSuccessRedirectUrl()*/
use Symfony\Component\Routing\RouterInterface;

/*used to check password in checkCredentials() 
 * in security.yml set:
 * encoders:
        AppBundle\Entity\User: bcrypt
 */
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

/*Needs to extend either AbstractGuardAuthenticator or 
 * if you're building some sort of login form, 
 * you can extend AbstractFormLoginAuthenticator instead
 * 
 *Symfony will call our authenticator on every single request. 
 * Our job is to: 
 * See if the user is submitting the login form, 
 * or if this is just some random request for some random page. 
 * Read the username and password from the request. 
 * Load the User object from the database.
 */
class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    private $formFactory;
    private $em;
    private $router;
    private $passwordEncoder;
    
    /*To create a form in the authenticator 
     * we use dependency injection to inject the form.factory service.*/
    public function __construct(FormFactoryInterface $formFactory, 
            EntityManager $em,
            RouterInterface $router,
            UserPasswordEncoder $passwordEncoder)
    {
        /*to hold the login form*/
        $this->formFactory = $formFactory;
        /*to fetch the user table from db*/
        $this->em = $em;
        /*to encode password*/
        $this->passwordEncoder = $passwordEncoder;
        /*to reroute*/
        $this->router = $router;    
    }
    
    /*Since this method is called on every request, 
     * we first need to see if the request is a login form submit. 
     * We setup our form so that it POSTs right back to /login. 
     * So if the URL is /login and the HTTP method is POST, 
     * our authenticator should spring into action. Otherwise, 
     * it should do nothing.*/
    public function getCredentials(Request $request)
    {
        
        $isLoginSubmit = $request->getPathInfo() == '/login' 
                && $request->isMethod('POST');
        
        if (!$isLoginSubmit) {
            // skip authentication
            return;
        }
        
        /*if the URL is /login and the HTTP method is POST
         * the user has just submitted the login form.
         */
        $form = $this->formFactory->create(LoginForm::class);
        $form->handleRequest($request);
        $data = $form->getData();
        
        /*setting the last username on the session
         * to prefil the form field _username in case of error
         */
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $data['_username']
        );
        
        /*Since our form is not bound to a class, 
         * this returns an associative array with _username and _password.
         * Here's the deal: if you return null from 
         * getCredentials(), 
         * authentication is skipped. 
         * But if you return anything else, Symfony calls 
         * getUser()
         */
        return $data;
    }
    
    /*Returns a User object. 
     * Since our Users are stored in the database, 
     * we'll query for them via the entity manager*/
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /*The $credentials argument is equal 
         * to what we return in getCredentials()
         */
        $username = $credentials['_username'];
        
        /*returns User record from db:
         * If this returns null, 
         * guard authentication will fail 
         * and the user will see an error. 
         * But if we do return a User object, 
         * then on we march! 
         * Guard calls checkCredentials() 
         */
        return $this->em->getRepository('AppBundle:User')
            ->findOneBy(['email' => $username]);
    }
    
    /* Verifies the user's password 
     * if they have one 
     * or do any other last-second validation. 
     * Returns true if the user should be logged in*/
    public function checkCredentials($credentials, UserInterface $user)
    {
        /*The $credentials argument is equal 
         * to what we return in getCredentials()
         */
        $password = $credentials['_password'];
        
        /*global hard coded password*/
        //if ($password == 'x') {
        //    return true;
        //}
        
        /*That'll take care of securely checking things*/
        if ($this->passwordEncoder->isPasswordValid($user, $password)) {
            return true;
        }
        
        return false;
    }
    
    /*When authentication fails, 
     * we need to redirect the user back to the login form. 
     * That will happen automatically - 
     * we just need to fill in getLoginUrl() 
     * so the system knows where that is.
     * We'll need the router service.
     */
    protected function getLoginUrl()
    {
        return $this->router->generate('security_login');
    }
    
    /*When authentication is successful 
     * the user is automatically redirected back 
     * to the last page they tried to visit 
     * before being forced to login.
     * In case they go directly to /login 
     * and there is no previous URL to send them to, 
     * we need a backup plan. 
     * That's the purpose of this function.
     */
    
    protected function getDefaultSuccessRedirectUrl()
    {
        return $this->router->generate('app_main_homepage_');
    }
}

