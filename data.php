<?php

require "foursquare.php";

use NetTuts\Foursquare;

if (isset($_GET['lng']) && isset($_GET['lat'])) {
    $foursquare = new Foursquare;

    $lng = $_GET['lng'];
    $lat = $_GET['lat'];

    $venues = $foursquare->getVenuesByCoordinates($lat, $lng);

    if ($venues->headers['X-RateLimit-Remaining'] > 10) {
        $venues = $venues->data->response->venues;
        $places = array();
        foreach ($venues as $key => $place) {
            $place = $foursquare->getVenueInfo($place->id);
            if ($place->data->meta->errorType !== "rate_limit_exceeded") {
                $place = $place->data->response->venue;
                if (isset($place->mayor)) {
                    $data = array(
                        "name" => $place->name,
                        "lat" => $place->location->lat,
                        "lng" => $place->location->lng,
                        "address" => $place->location->address,
                        "total_checkins" => $place->stats->checkinsCount,
                        "total_people" => $place->stats->usersCount,
                        "mayor" => array(
                            "name" => $place->mayor->user->firstName . " " . $place->mayor->user->lastName,
                            "photo" => $place->mayor->user->photo,
                            "checkins" => $place->mayor->count
                        )
                    );
                    array_push($places, $data);
                }
            } else {
                rateLimitReached();
            }
        }
        echo json_encode($places);
    } else {
        rateLimitReached();
    }
}

function rateLimitReached()
{
    echo '{"error":"Rate Limit Hit"}';
    exit;
}
