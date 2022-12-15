<?php

/**
 * UPDATE THE TRANSACTION COMPLETE WHEN THE PAID CHEKCBOX IS CHECKED ON THE HISTORY TABLE
 * If the transaction is a transfer, the query will update the values where the transfer_id is the same
 * If the one transfer is completed, the other one should also be completed.
 * RETURN VOID
 */

session_start();
include('connect.inc.php');
include('prepare_data.php');

if(isset($_POST)) {

    if(isset($_POST['complete'])) {

        $complete = prepareData($_POST['complete']);

    } else {

        $complete = '0';

    }


    /* DECLARING VARIABLES */

    $transactionId = prepareData($_GET['id']);
    $userID = prepareData($_SESSION['userID']);
    $previousPage = $_SERVER['HTTP_REFERER'];
    $transferID = prepareData($_POST['transferID']);

    if(empty($_POST['transferID'])) {

    /* IF TRANSFER ID IS EMPTY, COMPLETE A SINGLE TRANSACTION BY THE TRANSACTION_ID */

        $query = "UPDATE transactions 
            SET transactions_date = transactions_date, `transactions_complete` = ? 
            WHERE `user_id` = ? AND `transactions_id` = ?;";
        $stmt = $connection->prepare($query);
        $stmt->bind_param('sss', $complete, $userID, $transactionId);
        

    } else {

        /* ELSE UPDATE ALL THE TRANSACTIONS WITH THE TRANSFER_ID */

        $query = "UPDATE transactions 
        SET transactions_date = transactions_date, `transactions_complete` = ? 
        WHERE `user_id` = ? AND `transactions_transfer_id` = ?;";
        $stmt = $connection->prepare($query);
        $stmt->bind_param('sss', $complete, $userID, $transferID);

    }
    
    $stmt->execute();

    echo '<script>history.go(-1);</script>';

} else {

    echo 'error';
    echo '<script>history.go(-1);</script>';

}

?>