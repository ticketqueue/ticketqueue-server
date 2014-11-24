<?php

namespace TicketQueue\Server\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\User as BaseUser;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use TicketQueue\Server\Security\User as CustomUser;
use RuntimeException;

class JsonFileUserProvider implements UserProviderInterface
{
    private $jsonfilepath;

    public function __construct($jsonfilepath)
    {
        $this->jsonfilepath = $jsonfilepath;
        if (!file_exists($jsonfilepath)) {
            throw new RuntimeException("jsonfilepath does not exist." . $jsonfilepath);
        }
    }

    public function loadUserByUsername($username)
    {
        $jsonfilename = $this->jsonfilepath . '/' . $username . '.json';
        if (!file_exists($jsonfilename)) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }
        $json = file_get_contents($jsonfilename);
        $config = json_decode($json, true);

        $id = $config['id'];
        $username = $username;
        $password = $config['password'];
        $displayname = $config['displayname'];
        $email = $config['email'];
        $avatarurl = $config['avatarurl'];
        $roles = $config['roles'];
        return new CustomUser($id, $username, $password, $displayname, $roles, $email, $avatarurl);
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {   echo $class;
        exit();
        return $class === 'Symfony\Component\Security\Core\User\User';
    }
}
