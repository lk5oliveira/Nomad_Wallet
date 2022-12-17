<?php

$userId = $_SESSION['userID'];
include('connect.inc.php');

function getCurrencyList() {
    /**
     * GET THE LIST OF CURRENCIES AND COUNTRIES USED ON THE USER ID
     * RETURN ARRAY
     */

     //Declaring variables
     global $userId, $connection;
     
     //Prepared stmt
     $stmt = $connection->prepare("SELECT transactions_currency, transactions_country 
     FROM transactions WHERE user_id = ? GROUP BY transactions_currency ORDER BY transactions_date ASC");

     $stmt->bind_param("s", $userId);
     $stmt->execute();
     $result = $stmt->get_result();
     
     return $result->fetch_all();
}

function getDefaultCountry($currencyList) {
    /**
     * CHECK IF THE DEFAULT CURRENCY EXISTS ON getCurrencyList FUNCTION.
     * The default currency should always be printed on the accounts page
     * This checks if the default currency exists.
     * If it doesn't exist, it will print the default currency before the other currencies.
     * @param array used specially with the getCurrencyList.
     * @return bool
     */

    $currencyExist = false;

    //Loop for each getCurrencyList();
    foreach($currencyList as $key => $currency) {

        if(strtolower($currency[0]) == strtolower($_SESSION['defaultCurrency'])) {

            $currencyExist = true;
            return $currencyExist;

        }
        
        $currencyExist = false;

    }

    return $currencyExist;

}



function getCountryByCurrency($currency) {
    /**
     * GET THE LIST OF COUNTRIES BY CURRENCY AND USER ID
     * RETURN ARRAY
     */

     //Declaring variables
     global $userId, $connection;
     

     //Prepared stmt
     $stmt = $connection->prepare("SELECT transactions_currency, transactions_country 
     FROM transactions WHERE user_id = ? AND transactions_currency = ? GROUP BY transactions_country ORDER BY transactions_date DESC");

     $stmt->bind_param("ss", $userId, $currency);
     $stmt->execute();
     $result = $stmt->get_result();
     
     return $result->fetch_all();
}

function generateAccounts() {
    /**
     * GENERATE THE HTML LIST OF COUNTRIES ON THE ACCOUNTS PAGE
     * @return void
     */

    // Declaring variables
    global $userId, $connection;
    $currencyList = getCurrencyList();
    $initialValue = $_SESSION['initialValue'];
    $defaultCountry = $_SESSION['defaultCountry'];
    $defaultCurrency = $_SESSION['defaultCurrency'];
    $defaultCountryExists = getDefaultCountry($currencyList);

    if($defaultCountryExists === false || $defaultCountryExists == null) {

        $num = rand(1,13);

        echo "
        <div class='account-div' style='order: 0'>
            <a href='history.php?currency=$defaultCurrency' tarfet='_self' class='account-name'><i class='fa-solid fa-circle'></i> $defaultCurrency 🏡</a>
            <div class='account-details img-$num' style='border: 2px solid #f66b0e'>
                <div class='account-balance row-details'>
                        <span><i class='fa-solid fa-money-bill-1-wave'></i>  $initialValue <small> $defaultCurrency</small></span>
                    </div>
                    <span class='country-titles row-details'><i class='fa-solid fa-location-dot'></i> Used in</span>
                    <div class='account-countries row-details'>
                        <a href='history.php?currency=$defaultCurrency&country=$defaultCountry' tarfet='_self' class='country-list used-country'><span>$defaultCountry</span></a>
                </div>
            </div>
        </div>        
        ";
    }
    
     //Loop for each getCurrencyList();
    foreach($currencyList as $key => $currency) {
        $stmt = $connection->prepare("SELECT SUM(CASE WHEN DATE(transactions_date) <= DATE(CURDATE()) THEN transactions_value ELSE 0 END) AS total FROM transactions
        WHERE transactions_currency = ? AND user_id = ?;");

        $stmt->bind_param("ss", $currency[0], $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $resultArray = $result->fetch_all();
        $accountTotal = $resultArray[0][0];

        $countryList = getCountryByCurrency($currency[0]);
        $num = rand(1,13);

        if ($currency[0] == $_SESSION['defaultCurrency']) {  // Currency is equal to the user default currency
            echo 
            "<div class='account-div' style='order: 0'>
                <a href='history.php?currency=$currency[0]' tarfet='_self' class='account-name'><i class='fa-solid fa-circle'></i> $currency[0] 🏡</a>
                <div class='account-details img-$num' style='border: 2px solid #f66b0e'>";
        } elseif ($currency[0] == $_SESSION['currencyCode']) { // Currency is equal to user current country currency
            echo 
            "<div class='account-div' style='order: 1'>
                <a href='history.php?currency=$currency[0]' tarfet='_self' class='account-name'><i class='fa-solid fa-circle'></i> $currency[0] 🧳</a>
                <div class='account-details img-$num' style='border: 2px solid #f66b0e'>";
        } else {
            echo 
            "<div class='account-div' style='order: 2'>
                <a href='history.php?currency=$currency[0]' tarfet='_self' class='account-name'>$currency[0]</a>
                <div class='account-details img-$num'>";
        }
            echo "
                    <div class='account-balance row-details'>
                        <span><i class='fa-solid fa-money-bill-1-wave'></i>  $accountTotal<small> $currency[0]</small></span>
                    </div>
                    <span class='country-titles row-details'><i class='fa-solid fa-location-dot'></i> Used in</span>
                    <div class='account-countries row-details'>";
        foreach($countryList as $key => $country) {
            echo        
                        "<a href='history.php?currency=$currency[0]&country=$country[1]' tarfet='_self' class='country-list used-country'><span>" . ucfirst(strtolower($country[1])) . "</span></a>";
        }
            echo "
                     </div>
                </div>
            </div>";
    }

    return;
}


?>