<?php
session_start();
include("connect.inc.php");

$id = $_GET['edit'];
print_r($_POST);

function validate() {
    $category = 
    ['Car',
    'Flight',
    'Food',
    'Health',
    'Investments ',
    'Others',
    'Projects',
    'Rent',
    'Restaurant',
    'Salary',
    'Shopping',
    'Transport'];

    include('world-currency.php');
    include_once('connect.inc.php');

    if($_SERVER['REQUEST_METHOD'] === 'POST') { // FORM VALIDATION
        $date = explode('-', $_POST['date']);
        $month = $date[1];
        $day = $date[2];
        $year = $date[0];
        $wallet = $_POST['wallet'];
        $type = $_POST['type'];
        $categoryPost = ucfirst($_POST['category']);
        $country = $_POST['country'];
        $value = $_POST["value"];
        $exchange = $_POST['exchange'];

        
        $validationCategory = false;
        $validationCountry = false;
        $validationCurrency = false;

        foreach($category as $catValue) {
            if($catValue === $categoryPost) {
                $validationCategory = true;
                //echo 'cat ok';
            } else {
                $validation = false;
            }
        }

        for($i = 0;$i <= $countryArraySize;$i++) {

            if (strtolower($countryCurrency[$i]['country']) == strtolower($country)) {
                $validationCountry = true;
                //echo 'currency ok';
            } else {
                $validation = false;
            }
        }

        for($i = 0;$i <= $countryArraySize;$i++) {
            if (strtolower($countryCurrency[$i]['currency_code']) == strtolower($wallet)) {
                $validationCurrency = true;
                //echo 'country ok';
            } else {
                $validation = false;
            }
        }

        if ($validationCategory == true && $validationCountry == true && $validationCurrency == true) {
            $validation = true;
            //echo 'val error';
        } 

        if (!checkdate($month, $day, $year)) {
            $validation = false;
            //echo 'date';
        }

        if($type != 'income' && $type != 'expense' && $type != 'transfer') {
            $validation = false;
            //echo 'type';
        }
        
        $regex= "/[A-Za-z]/";

        if(preg_match_all($regex, $value) > 0 && preg_match_all($regex, $exchange) > 0 && preg_match_all($regex, $converted)) {
            $validation = false;
            //echo 'regex';
        }
    }

    return $validation;
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (validate() == false) {
        $_SESSION['validateEdit'] = false;
        header('location: ../edit.php?edit=' . $id);
        exit();
    } else {

        $date = mysqli_real_escape_string($connection, $_POST["date"]); // transaction date
        $value = mysqli_real_escape_string($connection, str_replace($_SESSION['defaultCurrency'], '',str_replace(',','.',str_replace('.', '', $_POST['value'])))); // transaction value
        $type = mysqli_real_escape_string($connection, $_POST["type"]); // transfer, income, expense
        $description = mysqli_real_escape_string($connection, $_POST["description"]); // transaction description
        $wallet = strtoupper(mysqli_real_escape_string($connection, $_POST["wallet"]));
        //$mode = $_POST['mode'];
        $country = strtoupper(mysqli_real_escape_string($connection, $_POST['country']));
        //$check = $_POST['check'];
        $id = mysqli_real_escape_string($connection, $_GET['edit']);
        $userId = mysqli_real_escape_string($connection, $_SESSION['userID']);

        if (array_key_exists('update-next', $_POST)) { 
            $updateNext = mysqli_real_escape_string($connection, $_POST['update-next']); // update next transactions checkbox.
        } else {
            $updateNext = 'off';
        }

        if (array_key_exists('paid', $_POST)) { 
            $paid = mysqli_real_escape_string($connection, $_POST['paid']); // paid checkbox
        } else {
            $paid = '0';
        }

        $generatedId;
        $_SESSION['validateEdit'] = validate(); 

        // Query the userID related with the transaction ID and to valiate it belongs to the user who is requesting to delete.
        // Avoids to delete by URL parameters transactions that not belongs to the user.
        $queryValidation = "SELECT user_id, transactions_type, transactions_transfer_id, transactions_repeat_id FROM `transactions` WHERE transactions_id = '$id';";
        $execValidation = mysqli_query($connection, $queryValidation);
        $resultValidation = mysqli_fetch_array($execValidation);
        $repeatId = $resultValidation[3];

        function createIdForMultipleTransfers($generateForColumn) {

            // Preparing variables
            global $userId, $generatedId, $connection;

            $generatedId;

            // Getting the last transfer ID
            $queryTransferId = "SELECT max(transactions_transfer_id) FROM transactions WHERE $generateForColumn IS NOT NULL AND user_id = $userId;";
            $execQueryTransferId = mysqli_query($connection, $queryTransferId);
            $lastTransferId = mysqli_fetch_array($execQueryTransferId);

            if (isset($lastTransferId)) {
                $generatedId = $lastTransferId[0] + 1;
            } else {
                $generatedId = 1; // if there's no transfer id registered, the id = 1.
            }

            return $generatedId;

        }

        if ($resultValidation[0] == $userId) { //validates the transaction id with the user id


            if ($resultValidation[1] == 'transfer' && $type == 'transfer') {

            /* TRANSFER EDIT
            * Transfered transactions are always updated from the transactions that originates the transfer -> the logic is a transaction came FROM a wallet TO another wallter.
            * so the transfer transactions is always edited from the FROM wallet transaction. E.g user's transfer from HOME to TRAVEL. The editing this transfer will pull out data from the HOME wallet transfer.
            * The code update both FROM and TO transactions.
            */

                $convertedValue = mysqli_real_escape_string($connection, $_POST['converted']);
                $exchangeRate = mysqli_real_escape_string($connection, str_replace(',','.',str_replace('.', '',$_POST['exchange']))); // formats the number to be send to DB

                // Get the transcation FROM ID
                $queryNegativeTransfer = "SELECT transactions_id FROM transactions WHERE transactions_transfer_id = '$resultValidation[2]' AND transactions_value < 0 AND user_id = '$userId';";
                $execNegative = mysqli_query($connection, $queryNegativeTransfer);
                $resultQueryNegative= mysqli_fetch_array($execNegative);
                $idTransferFrom = $resultQueryNegative[0];

                // Get the transcation TO ID
                $queryPositiveTransfer = "SELECT transactions_id FROM transactions WHERE transactions_transfer_id = '$resultValidation[2]' AND transactions_value > 0 AND user_id = '$userId';";
                $execPositive = mysqli_query($connection, $queryPositiveTransfer);
                $resultQueryPositive= mysqli_fetch_array($execPositive);
                $idTransferTo = $resultQueryPositive[0];

                if($wallet == 'home') {
                    $toWallet = 'travel';
                } elseif ($wallet == 'travel') {
                    $toWallet = 'home';
                }

                // Update the value for the account where the transaction is from.
                $sqlFrom = "UPDATE `transactions` 
                SET `transactions_date` = '$date', 
                `transactions_description` = '$description', `transactions_type` = 'transfer', 
                `transactions_value` = '-$value', `transactions_wallet` = '$wallet', 
                `transactions_category` = 'transfer', `transactions_country` = '$country', 
                `transactions_currency` = '$currency', transactions_exchange_rate = '$exchangeRate', transactions_complete = '$paid'
                WHERE `transactions_id` = '$idTransferFrom' AND user_id = '$userId';";
                
                // Update the value for the account where the transaction is to.
                $sqlTo = "UPDATE `transactions` 
                SET `transactions_date` = '$date', 
                `transactions_description` = '$description', `transactions_type` = 'transfer', 
                `transactions_value` = '$convertedValue', `transactions_wallet` = '$toWallet', 
                `transactions_category` = 'transfer', `transactions_country` = '$country', 
                `transactions_currency` = '$currency', transactions_exchange_rate = '$exchangeRate', transactions_complete = '$paid'
                WHERE `transactions_id` = '$idTransferTo' AND user_id = '$userId';";

                if(mysqli_query($connection, $sqlFrom) && mysqli_query($connection, $sqlTo)) {
                    echo '<script>history.go(-2);</script>';
                } else {
                    echo "there's something wrong here";
                }
                exit();

            } elseif (!empty($repeatId) && $updateNext == 'on') {

                /* UPDATE NEXT RELATED TRANSACTIONS */ 

                $category = mysqli_real_escape_string($connection, $_POST["category"]);

                $query_get_all_repeats = "SELECT transactions_id, transactions_repeat_id FROM transactions WHERE transactions_repeat_id = $repeatId AND user_id = $userId  GROUP BY transactions_date DESC";
                $exec_get_all_repeats = mysqli_query($connection, $query_get_all_repeats);
                $num_of_repeats = mysqli_num_rows(mysqli_query($connection, $query_get_all_repeats)); // Get the number of repeats.
                $array_of_repeats = mysqli_fetch_all(mysqli_query($connection, $query_get_all_repeats));

                $rep = 0;
                
                // Discovers the transactions that should be updated. For this, the query check how many transactions nexts must be updated.

                while ($array = mysqli_fetch_array($exec_get_all_repeats)) {

                    $transactions_id = $array['transactions_id'];
                
                    $rep += 1;
            
                    if($transactions_id == $id) {
                        break;
                    }
                
                }

                $rep -= 1;

                // FINISH - discovering. $rep will be used in a for loop.

                    for($i = $rep; $i >= 0;$i--) {

                        $date = mysqli_real_escape_string($connection, $_POST["date"]);
                        $transaction_id = $array_of_repeats[$i][0];

                        $sql = "UPDATE `transactions` 
                        SET `transactions_date` = transactions_date, 
                        `transactions_description` = '$description', `transactions_type` = '$type', 
                        `transactions_value` = '$value', `transactions_wallet` = '$wallet', 
                        `transactions_category` = '$category', `transactions_country` = '$country', 
                        `transactions_currency` = '$currency', transactions_complete = '$paid'
                        WHERE `transactions`.`transactions_repeat_id` = '$repeatId' AND transactions_id = '$transaction_id' AND user_id = '$userId';";

                        mysqli_query($connection, $sql);
                        
                    }

                    echo '<script>history.go(-2);</script>';

                    exit();

            } elseif ($type == 'transfer' && $resultValidation[1] != 'transfer') {

                /* 
                UPDATE FROM A SINGLE TRANSACTION TO A TRANSFER TRANSACTION
                * Editing a single transaction (income, expense) to a transfer
                * Inclues a transfer to the DB.
                * Deletes the single transactions.
                */

                $exchangeRate = mysqli_real_escape_string($connection, str_replace(',','.',str_replace('.', '',$_POST['exchange']))); // formats the number to be send to DB
                echo "dentro";

                createIdForMultipleTransfers('transactions_transfer_id');

                if($wallet == 'home') {
                    $toWallet = 'travel';
                } elseif ($wallet == 'travel') {
                    $toWallet = 'home';
                }
        
                $convertedValue = mysqli_real_escape_string($connection, $_POST['converted']);
                $exchangeRate = mysqli_real_escape_string($connection, str_replace(',','.',str_replace('.', '',$_POST['exchange']))); // formats the number to be send to DB
                
                // FROM ACCOUNT - query
                $sql_insert_sql_from = "INSERT INTO transactions (transactions_date, transactions_description, transactions_type, transactions_value, transactions_wallet, transactions_category, transactions_country, transactions_currency, transactions_exchange_rate, transactions_transfer_id, user_id, transactions_complete)
                VALUES ('$date','$description', '$type', '-$value', '$wallet', 'transfer', '$country', '$currency', '$exchangeRate', '$generatedId', '$userId', $paid);";
        
                // TO ACCOUNT - query
                $sql_insert_sql_to = "INSERT INTO transactions (transactions_date, transactions_description, transactions_type, transactions_value, transactions_wallet, transactions_category, transactions_country, transactions_currency, transactions_exchange_rate, transactions_transfer_id, user_id, transactions_complete)
                VALUES ('$date','$description', '$type', '$convertedValue', '$toWallet', 'transfer', '$country', '$currency', '$exchangeRate', '$generatedId', '$userId', $paid);";
        
                mysqli_query($connection, $sql_insert_sql_from); // EXEC FROM
                mysqli_query($connection, $sql_insert_sql_to); // EXEC TO

                // Deletes the single transaction
                $sql_delete_query = "DELETE FROM transactions WHERE transactions_id = '$id' AND user_id = '$userId';";
                $exec_delete_query = mysqli_query($connection, $sql_delete_query);

                echo '<script>history.go(-2);</script>';

                exit();

            } else {

            /* SINGLE TRANSACTIONS
            * This code update single transactions (that happened just once) without repetetion or connected transactions like transfer.
            */
                $category = mysqli_real_escape_string($connection, $_POST["category"]);
                if(!isset($_POST['category'])) {
                    $category = 'transfer';
                }

                if($type == "expense") {
                    $value = -1 * abs($value);
                }

                $sql = "UPDATE `transactions` 
                SET `transactions_date` = '$date', 
                `transactions_description` = '$description', `transactions_type` = '$type', 
                `transactions_value` = '$value',
                `transactions_category` = '$category', `transactions_country` = '$country', 
                `transactions_currency` = '$wallet', transactions_complete = '$paid'
                WHERE `transactions`.`transactions_id` = $id AND user_id = '$userId';";

                if(mysqli_query($connection, $sql)) {
                    echo '<script>history.go(-2);</script>';
                } else {
                   echo "there's something wrong here";
                }
                exit();

            }

        } else {
            echo 'error';
        }
    }
}
?> 