<?php

use TicketQueue\Server\Application;
use Symfony\Component\HttpFoundation\Request;

/** show all errors! */
ini_set('display_errors', 1);
error_reporting(E_ALL);

$app = new Application();

// General
$app->get(
    '/',
    'TicketQueue\Server\Controller\DashboardController::indexAction'
);
$app->get(
    '/queues',
    'TicketQueue\Server\Controller\DashboardController::queuesAction'
);
$app->get(
    '/queues/{queuekey}',
    'TicketQueue\Server\Controller\DashboardController::queuesViewAction'
);
$app->get(
    '/tickets/{ticketkey}',
    'TicketQueue\Server\Controller\DashboardController::ticketsViewAction'
);

return $app;
