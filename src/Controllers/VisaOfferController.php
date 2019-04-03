<?php

/**
 * This controller handle Visa APIs responses
 * 
 * @author Gayan
 */

namespace App\Controllers;

use \Datetime;
use \DateTimeZone;

class VisaOfferController extends Controller
{

    public function __construct($c)
    {
        parent::__construct($c);
    }

    public function getAllOffers($request, $response, $args)
    {
        $res = $this->httpRequest("vmorc/offers/v1/all", 'GET');
        
        if ($res['error']) {
            return $response->withJson($res);
        }

        $offers = $this->filterOffers($res['response']->Offers);

        return $response->withJson([
            'error' => false,
            'offers' => $offers,
        ]);
    }

    public function getOfferById($request, $response, $args)
    {
        $res = $this->httpRequest(
            "vmorc/offers/v1/byofferid",
            'GET',
            [
                'offerid' => $args['id'],
            ]
        );

        if ($res['error']) {
            return $response->withJson($res);
        }

        $offers = $this->filterOffers($res['response']->Offers);

        return $response->withJson([
            'error' => false,
            'offers' => $offers,
        ]);
    }

    public function getCountyPromotedOffers($request, $response, $args)
    {
        $res = $this->httpRequest(
            "vmorc/offers/v1/byfilter",
            'GET',
            [
                'promoting_country' => $args['code'],
            ]
        );
        
        if ($res['error']) {
            return $response->withJson($res);
        }

        $offers = $this->filterOffers($res['response']->Offers);

        return $response->withJson([
            'error' => false,
            'offers' => $offers,
        ]);
    }

    private function httpRequest($uri, $method = 'GET', $args = null, $body = null)
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
        if ($args) {
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

    // filter offer list to get what required
    private function filterOffers($offers)
    {
        $customizedOffers = [];
        foreach ($offers as $key => $offer) {

            $dummyOffer = [
                "offerId" => $offer->offerId,
                "offerStatus" => $offer->offerStatus,
                "offerTitle" => $offer->offerTitle,
                "validityFromDate" => $this->dateToLocal($offer->validityFromDate),
                "validityToDate" => $this->dateToLocal($offer->validityToDate),
                "offerShortDescription" => $offer->offerCopy->text,
                "merchantTerms" => $offer->merchantTerms->text,
                "visaTerms" => $offer->visaTerms->text,
                "imageList" => [],
                "merchantList" => [],
            ];

            // add image list
            foreach ($offer->imageList as $key => $image) {
                array_push($dummyOffer['imageList'], $image->fileLocation);
            }

            // add image list
            foreach ($offer->merchantList as $key => $merchant) {
                array_push($dummyOffer['merchantList'], [
                    'merchant' => $merchant->merchant,
                    'merchantAddress' => sizeof($merchant->merchantAddress) > 0 ?
                    [
                        'address1' => $merchant->merchantAddress[0]->address1,
                        'address2' => $merchant->merchantAddress[0]->address2,
                        'city' => $merchant->merchantAddress[0]->city,
                        'state' => $merchant->merchantAddress[0]->state,
                        'countryName' => $merchant->merchantAddress[0]->countryName,
                        'phoneNumbers' => $merchant->merchantAddress[0]->phoneNumbers,
                    ] : null,
                    'merchantImage' => sizeof($merchant->merchantImages) > 0 ? $merchant->merchantImages[0]->fileLocation : null,
                ]);
            }

            array_push($customizedOffers, $dummyOffer);
            unset($dummyOffer);
        }

        return $customizedOffers;
    }

    private function dateToLocal($dateTime)
    {
        $date = new DateTime($dateTime, new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone('Asia/Colombo'));
        return $date->format('Y-m-d');
    }
}
