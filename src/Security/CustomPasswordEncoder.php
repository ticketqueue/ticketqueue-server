<?php

namespace TicketQueue\Server\Security;

use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;

class CustomPasswordEncoder extends BasePasswordEncoder
{
    /**
     * Constructor.
     *
     * @param bool    $ignorePasswordCase Compare password case-insensitive
     */
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function encodePassword($raw, $salt)
    {
        if ($this->isPasswordTooLong($raw)) {
            throw new BadCredentialsException('Invalid password.');
        }

        return $this->mergePasswordAndSalt($raw, $salt);
    }

    /**
     * {@inheritdoc}
     */
    public function isPasswordValid($encoded, $raw, $salt)
    {
        if ($this->isPasswordTooLong($raw)) {
            return false;
        }

        $pass2 = $this->mergePasswordAndSalt($raw, $salt);
        return $this->comparePasswords($encoded, $pass2);
    }
}
