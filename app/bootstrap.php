<?php

use TicketQueue\Server\Application;
use Symfony\Component\HttpFoundation\Request;

/** show all errors! */
ini_set('display_errors', 1);
error_reporting(E_ALL);

$app = new Application();

// General
$app->get(
    '/login',
    'TicketQueue\Server\Controller\DashboardController::loginAction'
);

$app->get(
    '/',
    'TicketQueue\Server\Controller\DashboardController::rootAction'
);
// General
$app->get(
    '/dashboard',
    'TicketQueue\Server\Controller\DashboardController::indexAction'
);
$app->get(
    '/dashboard/queues',
    'TicketQueue\Server\Controller\DashboardController::queuesAction'
);
$app->get(
    '/dashboard/queues/{queuekey}',
    'TicketQueue\Server\Controller\DashboardController::queuesViewAction'
);
$app->get(
    '/dashboard/tickets/{ticketkey}',
    'TicketQueue\Server\Controller\DashboardController::ticketsViewAction'
);
$app->post(
    '/dashboard/tickets/{ticketkey}/reply',
    'TicketQueue\Server\Controller\DashboardController::ticketsReplyAction'
);

return $app;
