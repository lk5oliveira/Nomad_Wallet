<?php

$include('../location-api.php');

function getCurrencyRate() {
    // Fetching JSON
    $req_url = 'https://v6.exchangerate-api.com/v6/bc29665b8b5123e8d32125a7/latest/USD';
    $response_json = file_get_contents($req_url);

    // Continuing if we got a result
    if(false !== $response_json) {

        // Try/catch for json_decode operation
        try {

            // Decoding
            $response = json_decode($response_json);

            // Check for success
            if('success' === $response->result) {

                // YOUR APPLICATION CODE HERE, e.g.
                $base_price = 12; // Your price in USD
                $convertion = number_format(($base_price * $response->conversion_rates->BRL), 2);

            }

        }
        catch(Exception $e) {
            // Handle JSON parse error...
        }
    echo $response->conversion_rates->BRL;
    }
    return $convertion;
}
getCurrencyRate();
?>