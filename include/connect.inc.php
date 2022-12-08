<?php

$hostname = "localhost";
$database = "finance";
$username = "root";
$password = "Bnqlytxqcs3#";


$connection = new mysqli($hostname, $username, $password, $database);
    if($connection->connect_errno) {
        echo "Connection error: (" . $connection->connect_errno . ") " . $connection->connect_error;
    } else {
    }

?>