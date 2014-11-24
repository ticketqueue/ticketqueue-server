<?php
namespace TicketQueue\Server\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

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

        $data = array(
            'queue' => $queue,
            'ticket' => $ticket,
            'comments' => $comments
        );
        $html =  $app['twig']->render('@Dashboard/tickets_view.html.twig', $data);
        return $html;
    }
}
