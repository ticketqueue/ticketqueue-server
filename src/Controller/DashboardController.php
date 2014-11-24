<?php
namespace TicketQueue\Server\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use TicketQueue\Server\Model\Profile;
use TicketQueue\Server\Model\Comment;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Rhumsaa\Uuid\Uuid;
use DateTime;

class DashboardController
{
    public function indexAction(Application $app, Request $request)
    {
        $data = array('hello' => 'world');
        $html =  $app['twig']->render('@Dashboard/index.html.twig', $data);
        return $html;
    }
    
    public function queuesAction(Application $app, Request $request)
    {
        $storage = $app['ticketqueue.storage'];
        $data = array('queues' => $storage->getQueues());
        $html =  $app['twig']->render('@Dashboard/queues.html.twig', $data);
        return $html;
    }
    
    public function queuesViewAction(Application $app, Request $request, $queuekey)
    {
        $storage = $app['ticketqueue.storage'];
        $data = array(
            'queue' => $storage->getQueueByKey($queuekey),
            'tickets' => $storage->getTicketsByQueueKey($queuekey)
        );
        $html =  $app['twig']->render('@Dashboard/queues_view.html.twig', $data);
        return $html;
    }
    
    public function ticketsViewAction(Application $app, Request $request, $ticketkey)
    {
        $storage = $app['ticketqueue.storage'];

        $ticket = $storage->getTicketByKey($ticketkey);
        $queue = $storage->getQueueByKey($ticket->getQueueKey());
        $comments = $storage->getCommentsByTicketKey($ticketkey);
        $userprofile = new Profile();
        $userprofile->setDisplayName('Agent 1');
        $userprofile->setEmail('agent1@example.web');
        $userprofile->setAvatarUrl('agent1@example.web');
        
        $data = array(
            'queue' => $queue,
            'ticket' => $ticket,
            'comments' => $comments,
            'userprofile' => $userprofile
        );
        $html =  $app['twig']->render('@Dashboard/tickets_view.html.twig', $data);
        return $html;
    }

    public function ticketsReplyAction(Application $app, Request $request, $ticketkey)
    {
        $storage = $app['ticketqueue.storage'];

        $ticket = $storage->getTicketByKey($ticketkey);
        $queue = $storage->getQueueByKey($ticket->getQueueKey());
        $comments = $storage->getCommentsByTicketKey($ticketkey);
        
        $userprofile = new Profile();
        $userprofile->setKey('922b6c18-91bf-102b-a0bc-0030482ae110');
        $userprofile->setDisplayName('Agent 1');
        $userprofile->setEmail('agent1@example.web');
        $userprofile->setAvatarUrl('agent1@example.web');
        
        $poster = $userprofile;
        $comment = new Comment();
        $now = new DateTime();
        $comment->setDateTime($now);
        $comment->setMessage($request->request->get('reply_message'));
        $comment->setPoster($poster);
        $commentuuid = Uuid::uuid4();
        $comment->setKey($commentuuid->toString());
        
        $storage->addCommentToTicket($ticket, $comment);
        $storage->setTicketStatus($ticket, 'CLOSED');
        return new RedirectResponse('/queues/' . $ticket->getQueueKey());
    }
}
