<?php

namespace Nmotion\NmotionBundle\Security\User;

use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;

class UserPasswordEncoder extends BasePasswordEncoder
{
    public function encodePassword($raw, $salt = '')
    {
        return md5($raw);
    }

    public function isPasswordValid($encoded, $raw, $salt = '')
    {
        return $this->comparePasswords($encoded, $this->encodePassword($raw, $salt));
    }
}
