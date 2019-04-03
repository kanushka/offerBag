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

    $this->get('/lk', function (Request $request, Response $response, array $args) {
        // Sample log message
        $this->logger->info("Slim-Skeleton '/offers' route");

        // user credentials
        $username = 'MXQ4H8NR802VVQIXNR5621A05pcmYEEd0d2OaP082vfB15hxA';
        $password = 'yz1kXO4outKqNtCinm6C0C5qw1KeZHrD77';

        // key file locations
        chdir('../keys/visa');
        $crtFilePath = realpath(getcwd() . '\cert.pem');
        $keyFilePath = realpath(getcwd() . '\key_2d724912-2c02-45ef-b2c8-accb2983ad08.pem');

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://sandbox.api.visa.com/vmorc/offers/v1/byfilter?promoting_country=144",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "cache-control: no-cache"
            ),
            CURLOPT_USERPWD => ($username . ":" . $password),
            CURLOPT_SSLCERT => $crtFilePath,
            CURLOPT_SSLKEY => $keyFilePath,
        ));

        $res = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return $response->withJson([
                'error' => true,
                'msg' => $err,
            ]);
        }

        return $response->withJson([
            'error' => false,
            'response' => json_decode($res),
        ]);
    });
});

