<?php

function transactionBelongsToUser($transactionId, $userId) {
    /**
     * CHECK IF TRANSACTION ID BELONGS TO THE LOGGED USER
     * Prevents user to access data from different users by passing the transaction id by URL parameters
     * RETURNS BOOLEAN
     */

     include ("connect.inc.php");

     $stmt = $connection->prepare("SELECT user_id FROM transactions WHERE transactions_id = ?;");   
     $stmt->bind_param("s", $transactionId);

     $stmt->execute();

     $stmt_result = $stmt->get_result();
     $result_array = $stmt_result->fetch_array();

    if (is_null($result_array)) { // if the given transaction ID doesn't exist, then return false.
        //echo "null";
        return false;
    }

    $transaction_user_id = $result_array['user_id'];

     if ($transaction_user_id != $userId) {
        //echo 'false';
        return false;
     }

    // echo 'true';
     return true;

}

transactionBelongsToUser(293, 1);

?>