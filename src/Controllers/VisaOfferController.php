<?php

/**
 * This controller handle Visa APIs responses
 * 
 * @author Gayan
 */

namespace App\Controllers;

class VisaOfferController extends Controller
{

    public function __construct($c)
    {
        parent::__construct($c);
    }

    public function getAllOffers($request, $response, $args)
    {
        $res = $this->httpRequest("vmorc/offers/v1/all", 'GET');
        return $response->withJson($res);
    }

    public function getOfferById($request, $response, $args)
    {
        $res = $this->httpRequest("vmorc/offers/v1/byofferid", 'GET', [
            'offerid' => $args['id'],
        ]);
        return $response->withJson($res);
    }

    public function getCountyPromotedOffers($request, $response, $args)
    {
        $res = $this->httpRequest("vmorc/offers/v1/byfilter", 'GET', [
            'promoting_country' => $args['code'],
        ]
    );
        return $response->withJson($res);
    }

    private function httpRequest($uri, $method='GET', $args=null, $body=null)
    {
        // user credentials
        $username = $this->container->get('visa_config')['username'];
        $password = $this->container->get('visa_config')['password'];

        // key file locations
        chdir('../keys/visa');
        $crtFilePath = realpath(getcwd() . $this->container->get('visa_config')['file_name']['certificate']);
        $keyFilePath = realpath(getcwd() . $this->container->get('visa_config')['file_name']['private_key']);

        // if args exists
        // build query
        $queryParams = '';
        if($args){
            $queryParams .= '?';
            $queryParams .= http_build_query($args);
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->container->get('visa_config')['url'] . $uri . $queryParams,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
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
            return [
                'error' => true,
                'msg' => $err,
            ];
        }

        return [
            'error' => false,
            'response' => json_decode($res),
        ];
    }
}
