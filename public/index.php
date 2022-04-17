<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use App\Controller\SitesController;

require '../vendor/autoload.php';

$app = new Slim\App([

    'settings' => [
        'displayErrorDetails' => true,
        'debug'               => true,
        'whoops.editor'       => 'sublime',
    ]

]);
$app->get('/', SitesController::class . ':selectSites');
$app->run();

?>