<?php

$userId = $_SESSION['userID'];
include('include/connect.inc.php');

function getCurrencyList() {
    /**
     * GET THE LIST OF CURRENCIES AND COUNTRIES USED ON THE USER ID
     * RETURN ARRAY
     */

     //Declaring variables
     global $userId, $connection;
     

     //Prepared stmt
     $stmt = $connection->prepare("SELECT transactions_currency, transactions_country 
     FROM transactions WHERE user_id = ? GROUP BY transactions_currency ORDER BY transactions_date DESC");

     $stmt->bind_param("s", $userId);
     $stmt->execute();
     $result = $stmt->get_result();
     
     return $result->fetch_all();
     // ENF OF FUNCTION
}

$array = getCurrencyList();
foreach($array as $key => $value) {
    print_r($value[0]);
}

function generateAccounts() {

    global $userId, $connection;

    $currencyList = getCurrencyList();
     //Loop for each getCurrencyList();
     foreach($currencyList as $key => $value) {
        
    }

     //Get the total for the select currency
     /*SELECT SUM(CASE WHEN DATE(transactions_date) <= DATE(CURDATE()) THEN transactions_value ELSE 0 END) AS total FROM transactions
    WHERE transactions_currency = 'BRL' AND user_id = '1';*/

    // Generates the HTML on loop
    // loop over the countries on the currency

    //end of loop

    //end of function

}


?>