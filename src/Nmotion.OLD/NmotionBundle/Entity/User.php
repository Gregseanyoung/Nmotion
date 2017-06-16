<?php

namespace Nmotion\NmotionBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 */
abstract class User extends BaseUser
{
    use EntityAux;

    const ROLE_RESTAURANT_GUEST = 'ROLE_RESTAURANT_GUEST';
    const ROLE_RESTAURANT_STAFF = 'ROLE_RESTAURANT_STAFF';
    const ROLE_RESTAURANT_ADMIN = 'ROLE_RESTAURANT_ADMIN';
    const ROLE_SOLUTION_ADMIN   = 'ROLE_SOLUTION_ADMIN';

    const REGISTRATION_ORIGIN_NMOTION  = 'Nmotion';
    const REGISTRATION_ORIGIN_FACEBOOK = 'Facebook';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var boolean
     */
    protected $registered = false;

    /**
     * @var string
     */
    protected $registrationOrigin;

    /**
     * @var string
     */
    protected $role;

    public function serialize()
    {
        return serialize(
            array(
                $this->firstName,
                $this->lastName,
                $this->registered,
                parent::serialize()
            )
        );
    }

    public function unserialize($data)
    {
        list(
            $this->firstName,
            $this->lastName,
            $this->registered,
            $parentData
        ) = unserialize($data);
        parent::unserialize($parentData);
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set registered
     *
     * @param boolean $registered
     *
     * @return $this
     */
    public function setRegistered($registered)
    {
        $this->registered = $registered;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isRegistered()
    {
        return $this->registered;
    }

    /**
     * @param string $registrationOrigin
     *
     * @return $this
     */
    public function setRegistrationOrigin($registrationOrigin)
    {
        $this->registrationOrigin = $registrationOrigin;

        return $this;
    }

    /**
     * @return string
     */
    public function getRegistrationOrigin()
    {
        return $this->registrationOrigin;
    }

    /**
     * @param string $role
     *
     * @return $this
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Generate password - not saved automatically
     *
     * @param int $length
     * @return string
     */
    public function generatePassword ($length = 8)
    {
        // start with a blank password
        $password = "";

        // define possible characters - any character in this string can be
        // picked for use in the password, so if you want to put vowels back in
        // or add special characters such as exclamation marks, this is where
        // you should do it
        $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";

        // we refer to the length of $possible a few times, so let's grab it now
        $maxlength = strlen($possible);

        // check for length overflow and truncate if necessary
        if ($length > $maxlength) {
            $length = $maxlength;
        }

        // set up a counter for how many characters are in the password so far
        $i = 0;

        // add random characters to $password until $length is reached
        while ($i < $length) {
            // pick a random character from the possible ones
            $char = substr($possible, mt_rand(0, $maxlength-1), 1);
            // have we already used this character in $password?
            if (!strstr($password, $char)) {
                // no, so it's OK to add it onto the end of whatever we've already got...
                $password .= $char;
                // ... and increase the counter by one
                $i++;
            }
        }

        // done!
        return $password;
    }

    public function generateAndSetPlainPassword()
    {
        return $this->setPlainPassword($this->generatePassword());
    }
}
