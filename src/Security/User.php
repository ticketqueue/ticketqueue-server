<?php

namespace TicketQueue\Server\Security;

use Symfony\Component\Security\Core\User\User as BaseUser;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

class User implements AdvancedUserInterface
{
    private $id;
    private $username;
    private $password;
    private $displayname;
    private $roles;
    private $email;
    private $avatarurl;

    private $enabled;
    private $accountNonExpired;
    private $credentialsNonExpired;
    private $accountNonLocked;

    public function __construct($id, $username, $password, $displayname, array $roles, $email, $avatarurl)
    {
        if (empty($username)) {
            throw new \InvalidArgumentException('The username cannot be empty.');
        }

        $this->id = $id;
        $this->displayname = $displayname;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->avatarurl = $avatarurl;
        $this->enabled = true;
        $this->accountNonExpired = true;
        $this->credentialsNonExpired = true;
        $this->accountNonLocked = true;
        $this->roles = $roles;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getDisplayName()
    {
        return $this->displayname;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getAvatarUrl()
    {
        return $this->avatarurl;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired()
    {
        return $this->accountNonExpired;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked()
    {
        return $this->accountNonLocked;
    }

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired()
    {
        return $this->credentialsNonExpired;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }
}
