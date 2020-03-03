<?php


namespace AppBundle\Entity;

use Symfony\Component\Security\Core\Role;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User implements UserInterface
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string")
     */
    private $password;

    private $plainPassword;

    // This makes the array property hold an array of roles, but when we save Doctrine will automatically JSON encode that array and store it in a single field in the database.
    // When we query, it will decode the JSON back to the array. This means that we can store an array inside a single column in the database without having to worry about JSON encode or decode
    /**
     * @ORM\Column(type="json_array")
     */
    private $roles = [];

    public function getUsername() // This is used to show who is logged in when debugging
    {
        return $this->email;
    }

    public function getRoles()
    {
        $roles = $this->roles;
        if (!in_array('ROLE_USER', $roles)) { // if 'ROLE_USER' is not in the $roles array
            $roles[] = 'ROLE_USER'; // then add it to the array
        }

        return $roles; // if it is in the array then return the array
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null; // Security measure to make sure that the plain text password does not get accidentally saved anywhere
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }


    public function getPlainPassword()
    {
        return $this->plainPassword;
    }


    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        // by adding this line, the password will look like it has been changed
        // to Doctrine when changing plainPassword
        $this->password = null;
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    public function getEmail()
    {
        return $this->email;
    }


}