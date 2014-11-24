<?php

namespace TicketQueue\Server\Model;

class Comment
{
    private $message;
    
    public function setMessage($message)
    {
        $this->message = $message;
    }
    
    public function getMessage()
    {
        return $this->message;
    }
    
    private $datetime;

    public function getDatetime()
    {
        return $this->datetime;
    }
    
    public function setDatetime($d)
    {
        return $this->datetime = $d;
    }
    
    public function getMessageHtml()
    {
        $res = $this->message;
        //TODO: XSS cleanup here
        //$res = nl2br($res);
        return $res;
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
    
    private $poster;
    
    public function setPoster(Profile $poster)
    {
        $this->poster = $poster;
    }
    
    public function getPoster()
    {
        return $this->poster;
    }
}
