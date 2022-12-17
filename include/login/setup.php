<?php

include_once ("../connect.inc.php");
include_once ("../prepare_data.php");

session_start();

if($_SESSION['register'] == 'register') {
    if (isset($_POST["submit"])) {
    
        $defaultCountry = prepareData($_POST["defaultCountry"]);
        $defaultCurrency = prepareData($_POST["defaultCurrency"]);
        $initialValue = prepareData($_POST["initial-value"]);
        $userID = $_SESSION['userID'];
        $userId = $_SESSION['user'];
        $userEmail = $_SESSION['email'];
    
        include_once ("../connect.inc.php");
    
        // Checking on database if user exists
        $sql_select = $connection->prepare("SELECT * FROM users WHERE usersEmail = ?");
        $sql_select->bind_param('s', $userEmail);
        $sql_select->execute();
        $sql_select_result = $sql_select->get_result();

        $sql_result = $sql_select_result->num_rows;
        
        if($sql_result === 1) {

            if(is_null($initialValue)) {

                $initialValue = 0;

            }

            $sql_insert = $connection->prepare("UPDATE `users` SET `usersDefaultCountry` = ?, 
            `usersDefaultCurrency` = ?, `usersInitialValue` = ? WHERE `usersEmail` = ?;");
            $sql_insert->bind_param("ssss", $defaultCountry, $defaultCurrency, $initialValue, $userEmail);
            $sql_insert->execute();

            $_SESSION['register'] = 'not registering';
            
            include_once('../location-api.php');
            include_once('../world-currency.php');
    
            $_SESSION['country'] = $country;
            $_SESSION['defaultCountry'] = $defaultCountry;
            $_SESSION['currencyCode'] = $currencyCode;
            $_SESSION['currencySymbol'] = $currencySymbol;
            $_SESSION['user'] = $userId;
            $_SESSION['initialValue'] = $initialValue;

            $_SESSION['defaultSymbol'] = $currency_list[$defaultCurrency]['symbol'];
            $_SESSION['defaultCurrency'] = $defaultCurrency;

            $_SESSION['exchangeRates'] = GetCurrencyRate($_SESSION['defaultCurrency']);

            $connection->close();

            header('location: ../../panel.php');

        } else {

            header('location: ../../index.php');
            echo 'else';

        }
   }
}
?>