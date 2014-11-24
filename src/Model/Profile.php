<?php

namespace TicketQueue\Server\Model;

class Profile
{
    private $key;
    
    public function setKey($key)
    {
        $this->key = $key;
    }
    
    public function getKey()
    {
        return $this->key;
    }
    
    private $displayname;
    
    public function setDisplayName($displayname)
    {
        $this->displayname = $displayname;
    }
    
    public function getDisplayName()
    {
        return $this->displayname;
    }
    
    private $email;
    
    public function setEmail($email)
    {
        $this->email = $email;
    }
    
    public function getEmail()
    {
        return $this->email;
    }
    
    private $avatarurl;
    
    public function getAvatarUrl()
    {
        return $this->avatarurl;
    }
    
    public function setAvatarUrl($url)
    {
        $this->avatarurl = $url;
    }
}
