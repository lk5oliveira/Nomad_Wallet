<?php

/**
 * Prepare a string to send to a database
 * Cleaning the left and right spaces on the string
 * Removing forbidden char
 */

include('connect.inc.php');

function prepareData($data) : string {
    //declaring variables
    global $connection;
    $dataType = gettype($data);

    //Eliminates the right and left spaces on a string
    $trimmedData = trim($data);

    //Keep strings always upper case.
    $upperCaseData = strtoupper($trimmedData);

    //scape string to prevent SQL injection
    $finalData = mysqli_real_escape_string($connection, $upperCaseData);

    //The input fields are money masked, therefore it's necessary to replace the thousand and decimal separator
    if($dataType == 'integer' || $dataType == 'double') {
        //floatval and str_replace
        $upperCaseData = floatval(str_replace(',','.',str_replace('.', '', $upperCaseData)));
    }

    //return STRING
    return $finalData;
}


?>