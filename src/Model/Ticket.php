<?php

namespace TicketQueue\Server\Model;

class Ticket
{
    private $sender;
    
    public function setSender(Profile $sender)
    {
        $this->sender = $sender;
    }
    
    public function getSender()
    {
        return $this->sender;
    }
    
    private $subject;
    
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }
    
    public function getSubject()
    {
        return $this->subject;
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
    
    private $queuekey;
    
    public function setQueueKey($queuekey)
    {
        $this->queuekey = $queuekey;
    }
    
    public function getQueueKey()
    {
        return $this->queuekey;
    }
    
    private $reference;
    
    public function setReference($reference)
    {
        $this->reference = $reference;
    }
    
    public function getReference()
    {
        return $this->reference;
    }
    
    private $commentcount;
    
    public function setCommentCount($count)
    {
        $this->commentcount = $count;
    }
    
    public function getCommentCount()
    {
        return $this->commentcount;
    }
    
    public function getReplyCount()
    {
        return $this->commentcount - 1;
    }
}
