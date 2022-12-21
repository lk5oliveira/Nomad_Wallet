<?php
    session_start();
    include('world-currency.php');
    $country = '';
    $currencyCode = '';
    $currencySymbol = '';
    $currencyRate = 0;
     

    // LOCATION API

    function getIPAddress() { 
        //whether ip is from the share internet  
         if(!empty($_SERVER['HTTP_CLIENT_IP'])) {  
                    $ip = $_SERVER['HTTP_CLIENT_IP'];  
            }  
        //whether ip is from the proxy  
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];  
         }  
        //whether ip is from the remote address  
        else{  
                 $ip = $_SERVER['REMOTE_ADDR'];  
         }  
         return $ip;  
    }  
    $ip = getIPAddress();  

    // Initialize cURL.
    $ch = curl_init();

    // Set the URL that you want to GET by using the CURLOPT_URL option.
    curl_setopt($ch, CURLOPT_URL, 'https://ipgeolocation.abstractapi.com/v1/?api_key=f82260fc2609440f89135fd8dd8f020b&ip_address='. $ip); // replace the url for this one when deployed: 'https://ipgeolocation.abstractapi.com/v1/?api_key=f82260fc2609440f89135fd8dd8f020b&ip_address='. $ip

    // Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    // Execute the request.
    $data = curl_exec($ch);

    // Close the cURL handle.
    curl_close($ch);

    // Print the data out onto the page.
    $decodedData = json_decode($data);
    $country = $decodedData->country;
    $currencyCode = $decodedData->currency->currency_code;
    $currencyCodeHome = $_SESSION['defaultCurrency'];
    include_once('world-currency.php');
    $currencySymbol = $currency_list[$currencyCode]['symbol'];

  
    // CURRENCY API
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