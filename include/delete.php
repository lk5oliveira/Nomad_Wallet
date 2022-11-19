<?php

session_start();

include('connect.inc.php');
if(isset($_POST['id'])) {
    
    $id = mysqli_real_escape_string($connection, (INT)$_POST['id']);
    $userId = $_SESSION['userID'];
    if(empty($_POST['all'])) {
        $deleteAll = false;
    } else {
        $deleteAll = mysqli_real_escape_string($connection, $_POST['all']);
    }

    // Query the userID related with the transaction ID and to valiate it belongs to the user who is requesting to delete.
    // Avoids to delete by URL parameters
    $queryValidation = "SELECT user_id, transactions_type, transactions_transfer_id, transactions_repeat_id FROM `transactions` WHERE transactions_id = '$id';";
    $execValidation = mysqli_query($connection, $queryValidation);
    $resultValidation = mysqli_fetch_array($execValidation);

    if ($resultValidation[0] == $userId) { //validates the transaction id with the user id
        $transferId = $resultValidation[2];
        $repeatId = $resultValidation[3];

        if($resultValidation[1] == 'transfer') {

            $sql = "DELETE FROM transactions WHERE transactions_transfer_id = '$transferId' AND user_id = '$userId';";
            //echo "transfer";

        } elseif ($deleteAll == 'all') {

            $sql = "DELETE FROM transactions WHERE transactions_repeat_id = '$repeatId' AND user_id = '$userId';";
            //echo "all";

        } else {

            $sql = "DELETE FROM transactions WHERE transactions_id = '$id' AND user_id = '$userId';";
            //echo "else";

        }
        if(mysqli_query($connection, $sql)) {

        } else {
            //echo "error";
        }

    } else {
        //echo "error";
    }
}

header('Location: ../history.php');

?>