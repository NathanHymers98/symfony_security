<?php


namespace AppBundle\Controller;


use AppBundle\Form\LoginForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SecurityController extends Controller // This class will control the login and register pages on the website
{

    // Creating the route for this controller method
    /**
     * @Route("/login", name="security_login")
     */
    public function loginAction() // This method does not handle the form submit, that is done by
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(LoginForm::class, [ // Creating the LoginForm. Passing a second, default argument _username and setting it to $lastUsername so that when the user fails login, it will prepopulate the field with their last username.
            '_username' => $lastUsername
        ]);

        return $this->render(
            'security/login.html.twig',
            array(
                'form' => $form->createView(), // Passing the form into the template
                'error'         => $error,
            )
        );
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logoutAction() // We do not need to add anything here because Symfony will take care of logging out for us. Look in Security.yml
    {
        throw new \Exception('this should not be reached');
    }
}