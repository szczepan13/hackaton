<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use \App\Library\XapiProvider as XapiProvider;

/*
$app->get('/getAvailPlaces', function() use ($app) {
    $response = [
        [
            'row' => 12,
            'seat'=> 'B',
            'type'=> 'free'
        ],
        [
            'row' => 14,
            'seat' => 'C',
            'type' => 'swap'
        ]
    ];
    echo json_encode($response, JSON_PRETTY_PRINT);
});
*/
$app->get('/getAvailPlaces', function() use ($app){

    $xapi = new XapiProvider();
    //https://xap.ix-io.net/api/v1/ab16_Fru/seatMap?fields[seatMap]=type,seat,row,x_id&sort=x_id&page[number]=1&page[size]=100

    $params = [
        'fields' => [
            'seatMap' => 'type,seat,row,x_id'
        ]
    ];
    $response = $xapi->buildUrl('seatMap', 'ab16_Fru')->addParams($params)->execute();

    $result = [];
    foreach($response->seatMap as $map){
        $result[] = [
            'row' => $map->row,
            'seat' => $map->seat,
            'type' => $map->type
        ];
    }
    //echo json_encode($result)

    return response(json_encode($result))
        ->header('Access-Control-Allow-Origin', '*');
});

$app->get('/saveNewSeat/{reservationNr}/{seat}', function() use ($app){
    $response = [
        'status' => 'ON_WAIT'
    ];
    return response(json_encode($response))
        ->header('Access-Control-Allow-Origin', '*');
});


/*
$app->get('/getFlightInfo', function() use ($app){
    $response = [
        'speed'     => '256 knots',
        'altitude'  => '25000 feet',
        'flightStatus'=> '15 min delayed'
    ];
    echo json_encode($response);
});
*/



$app->get('/getFlightInfo/{id}', function($id) use ($app){

    // https://xap.ix-io.net/api/v1/ab16_Fru/getFlightInfo?filter[flight_id]=12312&fields[getFlightInfo]=flight_id,status,altitude,speed,x_id&sort=x_id&page[number]=1&page[size]=100
    // override
    $id = '12312';
    $xapi = new XapiProvider();
    $params = [
        'filter' => [
            'flight_id' => $id
        ],
        'fields' => [
            'getFlightInfo' => 'flight_id,status,altitude,speed,x_id'
        ]
    ];
    $response = $xapi->buildUrl('getFlightInfo', 'ab16_Fru')->addParams($params)->execute();

    $result = [
        'speed'     => $response->getFlightInfo[0]->speed . ' km/h',
        'altitude'  => $response->getFlightInfo[0]->altitude . ' feet',
        'flightStatus' => $response->getFlightInfo[0]->status
    ];
    return response(json_encode($result))
        ->header('Access-Control-Allow-Origin', '*');
});

/*
$app->get('/getFlightDetails/{id}', function($id) use ($app) {

    $result = [
        'departureDate' => '2017-02-11',
        'departureTime' => '15:05:00',
        'seat' => '16C',
        'birthday' => '13.07.1982'
    ];
    return json_encode($result);
});
*/

$app->get('/getFlightDetails/{id}', function($id) use ($app){

    //override
    $id = 'P6I4NG';

    $xapi = new XapiProvider();
    $params = [
            'filter' => [
                'booking_id' => $id
            ],
            'fields' => [
                'get_booking' => 'passengers_1_flight_segments,passengers_2_fare_detail,passengers_2_total,passengers_2_currency,passengers_2_amount,passengers_1_fare_family_info,passengers_1_document_no,booking_id,random_id,agency_no,is_option_booking,booking_date,record_locator,customer_addresses_0_phone,customer_addresses_0_email,customer_addresses_0_language,customer_addresses_0_zip,customer_addresses_0_name,customer_addresses_0_country_code,customer_addresses_0_company,customer_addresses_0_city,customer_addresses_0_address1,services_1_text,services_1_confirmation_status,services_1_code,services_1_service_no,services_2_segment_id,services_2_passenger_id,passengers_0_last_name,passengers_0_first_name,passengers_0_salutation,passengers_0_academic_title,passengers_0_passenger_type,passengers_0_external_passenger_id,passengers_0_passenger_id,passengers_1_document_status'
            ],
            'include' => 'flight_segments'
    ];
    $response = $xapi->buildUrl('get_booking')->addParams($params)->execute();

    $result = [
        'departureDate' => $response->flight_segments['0']->{'@flight_date'},
        'departureTime' => $response->flight_segments['0']->{'@departure_time_local'},
        'seat' => "16C",
        'birthday' => '13.07.1982'
    ];
    return response(json_encode($result))
        ->header('Access-Control-Allow-Origin', '*');
});

$app->get('/spotify', function() use ($app){

//      https://xap.ix-io.net/api/v1/spotify/playlists_by_search_term?filter[search_term]=airberlin&fields[playlists_by_search_term]=external_urls_spotify,search_term,uri,type,tracks_total,tracks_href,snapshot_id,public,owner_uri,owner_type,owner_id,owner_href,owner_spotify_url,name,playlist_id,href,collaborative&sort=playlist_id&page[number]=1&page[size]=100

    $xapi = new XapiProvider();
    $params = [
        'filter' => [
            'search_term' => 'airberlin'
        ],
        'fields' => [
            'playlists_by_search_term' => 'external_urls_spotify,search_term,uri,type,tracks_total,tracks_href,snapshot_id,public,owner_uri,owner_type,owner_id,owner_href,owner_spotify_url,name,playlist_id,href,collaborative'
        ]
    ];

    $response = $xapi->buildUrl('playlists_by_search_term', 'spotify')->addParams($params)->execute();

    $result = (array)$response->playlists_by_search_term[0];

    return response(json_encode($result))
        ->header('Access-Control-Allow-Origin', '*');
});


$app->get('/test', function () use ($app){
    $xapi = new XapiProvider();
    $params = [
        'filter' => [
            'departure'     => 'TXL',
            'destination'   => 'PMI',
            'flightdate'    => '2017-02-11'
        ],
        'fields' => [
            'availabilities' => 'destination,departure,random_id,previous_outbound_flight_date,next_outbound_flight_date'
        ],
        'sort' => 'random_id',
        'page' => [
            'number'    =>1,
            'size'      =>100
        ]
    ];

    $result = $xapi->buildUrl('availabilities')->addParams($params)->execute();

    echo '<pre>';
    var_dump($result);
});

