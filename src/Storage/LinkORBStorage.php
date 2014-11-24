<?php

namespace TicketQueue\Server\Storage;

use TicketQueue\Server\Model\Queue;
use TicketQueue\Server\Model\Ticket;
use TicketQueue\Server\Model\Comment;
use TicketQueue\Server\Model\Profile;
use LinkORB\Component\DatabaseManager\DatabaseManager;
use PDO;

class LinkORBStorage implements StorageInterface
{
    private $pdo;
    private $gravatar;
    
    public function __construct(DatabaseManager $manager, $dbname, $gravatar)
    {
        $this->manager = $manager;
        $this->gravatar = $gravatar;
        $this->pdo = $manager->getPdo($dbname, 'default');
    }
    
    public function getQueues()
    {
        $queues = array();
        $sql = "SELECT id, r_uuid, name FROM ticket_queue ORDER BY name";
        $res = $this->pdo->query($sql);
        foreach ($res as $row) {
            $queue = new Queue();
            $queue->setName($row['name']);
            $queue->setKey($row['r_uuid']);

            $csql = "SELECT count(id) as c FROM ticket_entry WHERE tq_id=" . $row['id'] . " AND status='OPEN' AND isnull(r_d_s)";
            $cres = $this->pdo->query($csql);
            foreach ($cres as $crow) {
                $queue->setOpenCount($crow['c']);
            }

            $queues[] = $queue;
        }
        return $queues;
    }
    
    public function getQueueByKey($queuekey)
    {
        $sql = "SELECT r_uuid, name FROM ticket_queue WHERE r_uuid=" . $this->pdo->quote($queuekey) . "";
        $res = $this->pdo->query($sql);
        foreach ($res as $row) {
            $queue = new Queue();
            $queue->setName($row['name']);
            $queue->setKey($row['r_uuid']);
            return $queue;
        }
        return null;
    }
    
    public function getTicketsByQueueKey($queuekey)
    {
        $tickets = array();
        $sql = "SELECT t.id as ref, t.r_uuid, t.status, t.subject, t.senderorganization, tq.r_uuid as queuekey FROM ticket_entry AS t JOIN ticket_queue AS tq ON tq.id=t.tq_id WHERE tq.r_uuid=" . $this->pdo->quote($queuekey) . " AND status='OPEN' AND isnull(t.r_d_s)";
        $res = $this->pdo->query($sql);
        foreach ($res as $row) {
            $ticket = new Ticket();
            $ticket->setKey($row['r_uuid']);
            $ticket->setReference($row['ref']);
            $ticket->setSubject($row['subject']);
            $ticket->setQueueKey($row['queuekey']);
            
            $sender = new Profile();
            $sender->setDisplayName($row['senderorganization']);
            $ticket->setSender($sender);
            
            $csql = "SELECT count(id) as c FROM comment WHERE recorduuid='" . $row['r_uuid'] . "' AND isnull(r_d_s)";
            $cres = $this->pdo->query($csql);
            foreach ($cres as $crow) {
                $ticket->setCommentCount($crow['c']);
            }
            
            $tickets[] = $ticket;

        }
        return $tickets;
    }
    
    public function getTicketByKey($ticketkey)
    {
        $sql = "SELECT t.id as ref, t.r_uuid, t.subject, t.senderorganization, t.status, tq.r_uuid as queuekey 
            FROM ticket_entry AS t
            JOIN ticket_queue AS tq ON tq.id=t.tq_id
            WHERE t.r_uuid=" . $this->pdo->quote($ticketkey) . "";
        $res = $this->pdo->query($sql);
        
        foreach ($res as $row) {
            $ticket = new Ticket();
            $ticket->setKey($row['r_uuid']);
            $ticket->setReference($row['ref']);
            $ticket->setSubject($row['subject']);
            $ticket->setQueueKey($row['queuekey']);
            
            $sender = new Profile();
            $sender->setDisplayName($row['senderorganization']);
            $ticket->setSender($sender);
            return $ticket;
        }
        return null;
    }
    
    private function getProfile($dbname, $useruuid)
    {
        $profile = new Profile();

        // Try 'local' db first
        $csql = "SELECT id, fullname, email FROM user_entry WHERE r_uuid = " . $this->pdo->quote($useruuid) . "";
        $res = $this->pdo->query($csql);
        foreach ($res as $row) {
            $profile->setDisplayName($row['fullname']);
            $profile->setEmail($row['email']);
            $profile->setAvatarUrl($this->gravatar->buildGravatarURL($row['email']));
            return $profile;
        }

        // try remote db
        $pdo = $this->manager->getPdo($dbname, 'default');
        $csql = "SELECT id, fullname, email FROM user_entry WHERE r_uuid = " . $pdo->quote($useruuid) . "";
        $res = $pdo->query($csql);
        foreach ($res as $row) {
            $profile->setDisplayName($row['fullname']);
            $profile->setEmail($row['email']);
            $profile->setAvatarUrl($this->gravatar->buildGravatarURL($row['email']));
            return $profile;
        }
        // Fallback to 'anonymous';
        
        $profile->setDisplayName('Anonymous');
        $profile->setAvatarUrl($this->gravatar->buildGravatarURL(null));

        return $profile;
    }
    
    public function getCommentsByTicketKey($ticketkey)
    {
        $comments = array();
        $sql = "SELECT t.dbname, c.r_c_u, c.id as ref, c.r_uuid, c.message, c.postername, c.posteremail
            FROM comment AS c
            JOIN ticket_entry AS t ON t.r_uuid = c.recorduuid
            WHERE c.recorduuid=" . $this->pdo->quote($ticketkey) . " AND isnull(c.r_d_s)";
            
        $res = $this->pdo->query($sql);
        $rpdo = null; // remote pdo connection to customer database;
        foreach ($res as $row) {
            $comment = new Comment();
            $comment->setKey($row['r_uuid']);
            //$comment->setReference($row['ref']);
            $comment->setMessage(nl2br($row['message']));
            //$comment->setSenderName($row['senderorganization']);

            $poster = $this->getProfile($row['dbname'], $row['r_c_u']);
            $comment->setPoster($poster);

            $comments[] = $comment;
        }
        return $comments;

    }
}
