<?php

function isTransfer($transactionId) {
    /**
    ** RETURN THE TRANSACTION ID TYPE
    ** to be used on edit pages to check if the user is acessing the right edit page for the related transfer Id
    ** Prevents user to pass a transfer Id by URL parameters and access transfer transaction in the regular edit page.
    ** RETURNS STRING - return the transfer type.
    */
    
    include ("connect.inc.php");

    $stmt = $connection->prepare("SELECT transactions_type FROM transactions WHERE transactions_id = ?;");
    $stmt->bind_param("s", $transactionId);

    $stmt->execute();

    $stmt_result = $stmt->get_result();
    $result_array = $stmt_result->fetch_array();

    $transaction_type = strtolower($result_array['transactions_type']);

    return $transaction_type;

}   

?>