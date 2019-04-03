<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});


$app->group('/offers', function () {
    // app user login
    $this->get('', \VisaOfferController::class . ':getAllOffers');
    $this->get('/{id:[0-9]+}', \VisaOfferController::class . ':getOfferById');
    $this->get('/country/{code:[0-9]+}', \VisaOfferController::class . ':getCountyPromotedOffers');
});

