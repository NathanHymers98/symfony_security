<?php


namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;

class LoginForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Creating two fields on the form. Using _username even though we are using an email because it is best practice
        $builder
            ->add('_username')
            ->add('_password', PasswordType::class)
            ;
    }
}