<?php

namespace NetTuts;

Class Foursquare
{
    const CLIENTID = "Insert Client ID";
    const CLIENTSECRET = "Insert Client Secret";

    const BASEURI = "https://api.foursquare.com/v2/";
    const APIVERSION = "20130224";

    /*---------API Functions---------*/

    public function getVenuesByCoordinates($lat, $lng, $limit = 10)
    {
        $params = array(
            "ll" => $lat . "," . $lng,
            "limit" => $limit
        );
        return $this->makeGetRequest("venues/search", $params);
    }

    public function getVenueInfo($venueId)
    {
        return $this->makeGetRequest("venues/" . $venueId);
    }

    /*---------Helper Functions---------*/

    private function prepareURL($api, $params)
    {
        $url = self::BASEURI . $api
             . "?v=" . self::APIVERSION
             . "&client_id=" . self::CLIENTID
             . "&client_secret=" . self::CLIENTSECRET;
        if (count($params)) {
            $url .= "&" . http_build_query($params);
        }
        return $url;
    }

    private function makeGetRequest($api, $params = array())
    {
        $url = $this->prepareURL($api, $params);
        $response = json_decode(file_get_contents($url));
        return (object)array(
            'data' => $response,
            'headers' => $this->parseHeaders($http_response_header)
        );
    }

    private function parseHeaders($headersRaw)
    {
        $headers = array();
        foreach ($headersRaw as $i => $header) {
            list($name, $value) = explode(" ", $header, 2);
            $name = str_replace(':', '', $name);
            $headers[$name] = $value;
        }
        return $headers;
    }
}
