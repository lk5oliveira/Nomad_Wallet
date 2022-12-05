<?php

include_once ("../connect.inc.php");

session_start();

if($_SESSION['register'] == 'register') {
    if (isset($_POST["submit"])) {
    
        $defaultCountry = mysqli_real_escape_string($connection, $_POST["defaultCountry"]);
        $defaultCurrency = mysqli_real_escape_string($connection, $_POST["defaultCurrency"]);
        $initialValue = mysqli_real_escape_string($connection, str_replace(',','.',str_replace('.', '', $_POST["initial-value"])));
        $userID = $_SESSION['userID'];
        $userId = $_SESSION['user'];
        $userEmail = $_SESSION['email'];
    
        include_once ("../connect.inc.php");
    
        // Checking for register under the email or usernameId
        $sql_select = "SELECT * FROM users WHERE usersEmail = '$userEmail'";
        $sql_check = mysqli_query($connection, $sql_select);
        $sql_result = mysqli_num_rows($sql_check);
        
        if($sql_result === 1) {
            if(is_null($initialValue)) {
                $initialValue = 0;
            }
            $sql_insert = "UPDATE `users` SET `usersDefaultCountry` = '$defaultCountry', 
            `usersDefaultCurrency` = '$defaultCurrency', `usersInitialValue` = '$initialValue' WHERE `usersEmail` = '$userEmail';";
            if(mysqli_query($connection, $sql_insert)) {
                $_SESSION['register'] = 'not registering';


                include_once('../location-api.php');
                include_once('../world-currency.php');
        
                $_SESSION['country'] = $country;
                $_SESSION['currencyCode'] = $currencyCode;
                $_SESSION['currencySymbol'] = $currencySymbol;
                $_SESSION['user'] = $userId;
                $_SESSION['initialValue'] = $initialValue;

                $_SESSION['defaultSymbol'] = $currency_list[$defaultCurrency]['symbol'];
                $_SESSION['defaultCurrency'] = $defaultCurrency;

                $_SESSION['exchangeRates'] = GetCurrencyRate($_SESSION['defaultCurrency']);

                header('location: ../../panel.php');

            } else {
            $_SESSION['errorRegistered'] = TRUE;
            }

        } else {
            header('location: ../../index.php');
            echo 'else';
        }
   }
}
?>