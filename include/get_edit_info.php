<?php

$id = mysqli_real_escape_string($connection, (INT)$_GET['edit']);

$_SESSION["transaction_id"] = $id;

include_once('connect.inc.php');
if (isset($_GET['edit'])) {
    $sql = "SELECT * FROM transactions WHERE transactions_id = $id;";
    $query = mysqli_query($connection, $sql);
    $result = mysqli_fetch_array($query);
    $date = strtotime($result['transactions_date']);
    $newDate = date("Y-m-d", $date);
} else {
   //header("location: ../panel.php");
}   

?>