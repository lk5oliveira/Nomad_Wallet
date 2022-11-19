<?php

/*$hostname = "sql108.epizy.com";
$database = "epiz_32170393_getIp";
$username = "epiz_32170393";
$password = "vG2tf8nDiH5q";

$connection = new mysqli($hostname, $username, $password, $database);
    if($connection->connect_errno) {
        echo "Connection error: (" . $connection->connect_errno . ") " . $connection->connect_error;
    } else {
    }
*/
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
    curl_setopt($ch, CURLOPT_URL, 'https://ipgeolocation.abstractapi.com/v1/?api_key=f82260fc2609440f89135fd8dd8f020b&ip_address='. $ip);

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
    $currencySymbol = '';

    $ipAddress = $decodedData->ip_address;
    $city = $decodedData->city;
    $region = $decodedData->region;
    $postal_code = $decodedData->postal_code;
    $longitude = $decodedData->longitude;
    $latitude = $decodedData->latitude;




    /*$query = "INSERT INTO `ip`(`ip_address`, `city`, `region`, `postal_code`, `longitude`, `latitude`) 
    VALUES ('$ipAddress','$city','$region','$postal_code','$longitude','$latitude');";
    $exec = mysqli_query($connection, $query);
*/
    echo 'Obrigado por compartilhar a tua localização e seu endereço de IP {$ip}';


?>