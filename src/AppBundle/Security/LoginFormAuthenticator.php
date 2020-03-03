<?php


namespace AppBundle\Security;


use AppBundle\Form\LoginForm;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator // Every custom authentication class needs to extend AbstractGuardAuthenticator apart from in certain cases like this
                                                                    // I am extending this other class because it is extends the other class we need, but gives us more stuff that we will be using
{

    private $formFactory;

    private $em;

    private $router;

    private $passwordEncoder;

    public function __construct(FormFactoryInterface $formFactory, EntityManager $em, RouterInterface $router, UserPasswordEncoder $passwordEncoder)
    {
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function getCredentials(Request $request) // This method is called on every request
    {
        $isLoginSubmit = $request->getPathInfo() == '/login' && $request->isMethod('POST'); // Gets the URL info and checks it is equal to '/login' and also checks that the HTTP method is POST
                                                                                                    // If the $isLoginSubmit variable is true, then the user has successfully submitted the form
        if (!$isLoginSubmit) { // if the $isLoginSubmit variable is false, then just return nothing
            return;
        }

        $form = $this->formFactory->create(LoginForm::class); // if $isLoginSubmit is true then create the form
        $form->handleRequest($request); // Gets the request that was just submitted via the form
        $data = $form->getData(); // Get the data from the submitted request

        $request->getSession()->set( // When the user fails to login, the email that they entered will stay in the field
            Security::LAST_USERNAME,
            $data['_username']
        );

        return $data; // since our form is not bound to a class, this will return an associative array with the keys '_username' and '_password' and their values for this request

    }

    public function getUser($credentials, UserProviderInterface $userProvider) // if we return null from getCredentials(), then authentication is skipped but if anything else is returned, then Symfony calls getUser()
    {
        // $credentials is what the getCredentials method returns.
        $username = $credentials['_username']; // Assigning the what was submitted in the '_username' field and using it to search for the user in the database

        return $this->em->getRepository('AppBundle:User') // if this returns null, then guard authentication will fail since it could not be found and give the user an error
                                                                    // If it returns a user object, then it will call the next method, which is checkCredentials() and pass it this user object
            ->findOneBy(['email' => $username]); // Even though it is called username everywhere, it is actually an email that the user enters, so we are using it to search for a matching email it the database
    }

    public function checkCredentials($credentials, UserInterface $user) // Our chance to check the users password, if they have one, or do any last second validation
    {
        $password = $credentials['_password']; // setting $password to the password that was submitted in the form

        if ($this->passwordEncoder->isPasswordValid($user, $password)) { // if the password is valid for the specific user
            return true; // then return true
        }
        return false; // if not, then return false. This is making sure that everyone shares a global password simply for development
    }

    protected function getLoginUrl() // When authentication fails, we need to redirect the user back to the login form. This will happen automatically and we just need to fill in this method so that the system knows where that is
    {
        return $this->router->generate('security_login');
    }

    protected function getDefaultSuccessRedirectUrl()
    {
        return $this->router->generate('homepage');
    }


}