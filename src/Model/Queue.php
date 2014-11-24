<?php

namespace TicketQueue\Server\Model;

class Queue
{
    private $name;
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    private $key;
    
    public function setKey($key)
    {
        $this->key = $key;
    }
    
    public function getKey()
    {
        return $this->key;
    }
    
    private $opencount = 0;
    
    public function setOpenCount($count)
    {
        $this->opencount = $count;
    }
    
    public function getOpenCount()
    {
        return $this->opencount;
    }
}
