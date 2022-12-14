<?php

function getCurrencyRate($from) {
    // Fetching JSON
    $req_url = 'https://v6.exchangerate-api.com/v6/bc29665b8b5123e8d32125a7/latest/' . $from;
    $response_json = file_get_contents($req_url);

    // Continuing if we got a result
    if(false !== $response_json) {

        // Try/catch for json_decode operation
        try {

            // Decoding
            $response = json_decode($response_json);

            // Check for success
            if('success' === $response->result) {
                $currencyRate = $response->conversion_rates;
            }
        
        }
        catch(Exception $e) {
            // Handle JSON parse error...
        }
    }
    return $currencyRate;
}

?>