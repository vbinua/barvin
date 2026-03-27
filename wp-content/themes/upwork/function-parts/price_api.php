<?php
// wp_clear_scheduled_hook('five_min');
add_filter( 'cron_schedules', 'cron_add_five_min' );
function cron_add_five_min( $schedules ) {
	$schedules['five_min'] = array(
		'interval' => 300,
		'display' => 'Every five minutes'
	);
	return $schedules;
}

add_action( 'wp', 'my_activation' );
function my_activation() {
	if ( ! wp_next_scheduled( 'my_five_min_event' ) ) {
		wp_schedule_event( time(), 'five_min', 'my_five_min_event');
	} 
}

add_action( 'my_five_min_event', 'do_every_five_min' );
function do_every_five_min() {
    return
    $url = "#";

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $headers = array(
    "Authorization: Basic #",
    "Content-Type: application/x-www-form-urlencoded",
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $data = "grant_type=client_credentials";

    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $resp = curl_exec($curl);
    curl_close($curl);
    $accessObj = json_decode($resp);
    $aceessToken = $accessObj->access_token;


    $url = "#";

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $headers = array(
    "Authorization: Bearer " . $aceessToken,
    "Content-Type: application/json",
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $data = <<<DATA
    {
        "line_items": [
            {
                "page_count": 22, 
                "pod_package_id": "0850X0850FCPRESS080CW444MXX",
                "quantity": 1
            },
            {
                "page_count": 16,
                "pod_package_id": "0850X0850FCPRESS080CW444MXX",
                "quantity": 1
            },
            {
                "page_count": 20,
                "pod_package_id": "0850X0850FCPRESS080CW444MXX",
                "quantity": 1
            }
        ], 
        "shipping_address": {
            "city": "Washington",
            "country_code": "US",
            "postcode": "20540",
            "state_code": "DC",
            "street1": "101 Independence Ave SE"
        },
        "shipping_level": "EXPRESS"
    }
    DATA;

    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $resp = curl_exec($curl);
    curl_close($curl);
    $response = json_decode($resp);
    $prices = $response->line_item_costs;  
    // update_term_meta(3, 'price', $prices[0]->cost_excl_discounts);
    // update_term_meta(4, 'price', $prices[1]->cost_excl_discounts);
    // update_term_meta(7, 'price', $prices[2]->cost_excl_discounts);
}