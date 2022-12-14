<?php
session_start();
include("connect.inc.php");
include('prepare_data.php');
$id = $_GET['edit'];

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

        $date = prepareData($_POST["date"]); // transaction date
        $value = prepareData($_POST['value']); // transaction value
        $type = prepareData($_POST["type"]); // transfer, income, expense
        $description = prepareData($_POST["description"]); // transaction description
        $wallet = prepareData($_POST["wallet"]);
        //$mode = $_POST['mode'];
        $country = prepareData($_POST['country']);
        //$check = $_POST['check'];
        $id = prepareData($_GET['edit']);
        $userId = prepareData($_SESSION['userID']);

        if (array_key_exists('update-next', $_POST)) { 
            $updateNext = prepareData($_POST['update-next']); // update next transactions checkbox.
            echo $updateNext;
        } else {
            $updateNext = 'off';
        }

        if (array_key_exists('paid', $_POST)) { 
            $paid = prepareData($_POST['paid']); // paid checkbox
        } else {
            $paid = '0';
        }

        $generatedId;
        $_SESSION['validateEdit'] = validate(); 

        // Query the userID related with the transaction ID and to valiate it belongs to the user who is requesting to delete.
        // Avoids to delete by URL parameters transactions that not belongs to the user.
        $queryValidation = $connection->prepare("SELECT user_id, transactions_type, transactions_transfer_id, transactions_repeat_id FROM `transactions` WHERE transactions_id = ?");
        $queryValidation->bind_param('i', $id);
        $queryValidation->execute();
        $resultValidation = $queryValidation->get_result()->fetch_array();
        $repeatId = $resultValidation[3];


        if ($resultValidation[0] == $userId) { //validates the transaction id with the user id

            if (!empty($repeatId) && strtolower($updateNext) == 'on') {


                /* UPDATE NEXT RELATED TRANSACTIONS */

                $category = prepareData($_POST["category"]);

                $query_get_all_repeats = "SELECT transactions_id, transactions_repeat_id FROM transactions WHERE transactions_repeat_id = ? AND user_id = ? GROUP BY transactions_date DESC";

                $stmt = $connection->prepare($query_get_all_repeats);
                $stmt->bind_param('ss', $repeatId, $userId);
                $stmt->execute();

                $get_result = $stmt->get_result();
                $num_of_repeats = $get_result->num_rows;

                $rep = 0;
                $array_of_repeats = [];
                // Discovers the transactions that should be updated. For this, the query check how many transactions nexts must be updated.

                while($row = $get_result->fetch_assoc()) {

                    $transactions_id = $row['transactions_id'];
                    array_push($array_of_repeats, $transactions_id);
    
                    $rep += 1;
            
                    if($transactions_id == $id) {
                        break;
                    }
                
                }

                print_r($array_of_repeats);
                $rep -= 1;

                // FINISH - discovering. $rep will be used in a for loop.

                    for($i = $rep; $i >= 0;$i--) {

                        echo 'oi';
                        $date = prepareData($_POST["date"]);
                        $transaction_id = $array_of_repeats[$i];

                        $query = "UPDATE transactions
                        SET transactions_date = transactions_date,
                            transactions_description = ?,
                            transactions_type = ?,
                            transactions_value = ?,
                            transactions_category = ?,
                            transactions_country = ?,
                            transactions_currency = ?,
                            transactions_complete = ?
                        WHERE transactions.transactions_repeat_id = ?
                          AND transactions_id = ?
                          AND user_id = ?";
              
                        $stmt = mysqli_prepare($connection, $query);
                        mysqli_stmt_bind_param($stmt, "ssssssssss", $description, $type, $value, $category, $country, $wallet, $paid, $repeatId, $transaction_id, $userId);
                        mysqli_stmt_execute($stmt);
                    }

            } else {

            /* SINGLE TRANSACTIONS
            * This code update single transactions (that happened just once) without repetetion or connected transactions like transfer.
            */
                
                $category = mysqli_real_escape_string($connection, $_POST["category"]);

                if($type == "expense") {
                    $value = -1 * abs($value);
                }
               
                $query = "UPDATE transactions
                SET transactions_date = ?,
                    transactions_description = ?,
                    transactions_type = ?,
                    transactions_value = ?,
                    transactions_category = ?,
                    transactions_country = ?,
                    transactions_currency = ?,
                    transactions_complete = ?
                WHERE transactions.transactions_id = ?
                AND user_id = ?";

                $stmt = $connection->prepare($query);
                $stmt->bind_param(
                "ssssssssss", $date, $description, $type, $value, $category, $country, $wallet, $paid, $id, $userId);
                $stmt->execute();

            }

        } else {
            echo 'error';
        }

        $connection->close();
        echo '<script>history.go(-2);</script>';
        exit();

    }
}
?> 