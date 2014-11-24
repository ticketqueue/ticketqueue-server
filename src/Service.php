<?php

namespace TicketQueue\Server;

use TicketQueue\Server\Model\Queue;

class Service
{
    public function getQueues()
    {
        $queues = array();
        
        $queue = new Queue();
        $queue->setName('Primary');
        $queues[] = $queue;
        
        $queue = new Queue();
        $queue->setName('Secondary');
        $queues[] = $queue;
        
        return $queues;
    }
}
