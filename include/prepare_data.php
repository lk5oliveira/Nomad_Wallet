<?php

/**
 * Prepare a string to send to a database
 * Cleaning the left and right spaces on the string
 * Removing forbidden char
 * @return mixed float or string
 */

include('connect.inc.php');

function prepareData($data) : string {
    //declaring variables
    global $connection;
    $dataType = gettype($data);
    $regex = '/^[0-9,\.]+$/';

    //Eliminates the right and left spaces on a string
    $trimmedData = trim($data);

    //scape string to prevent SQL injection
    $finalData = mysqli_real_escape_string($connection, $trimmedData);

    //The input fields are money masked, therefore it's necessary to replace the thousand and decimal separator
    if($dataType == 'integer' || $dataType == 'double' || preg_match($regex, $finalData)) {
        //floatval and str_replace
        $finalNumber = floatval(str_replace(',','.', str_replace('.', '', $finalData)));
        return $finalNumber;
    }

    //Keep strings always upper case.
    $finalData = strtoupper($finalData);

    //return STRING
    return $finalData;
}

?>