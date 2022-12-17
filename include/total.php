<?php
    //Create a variable for the session user ID.
    include("connect.inc.php");
    include_once('world-currency.php');
  
    $sessionUser = $_SESSION['email'];
    $userId = mysqli_fetch_array(mysqli_query($connection, "SELECT usersID FROM users WHERE usersEmail = '$sessionUser';"));
    $userIdResult = $userId['usersID']; //USER ID


    //Get the full period and the monthly income and expenses - used to calculate the montlhy result and the total. - $period = month = current month.
    function getTotal($type, $period, $wallet) {     
        include("connect.inc.php");
        GLOBAL $userIdResult;


        if($period === "month") { // CURRENT MONTH
            $mysqli_query = "SELECT MONTH(transactions_date) AS month, SUM(transactions_value) AS total FROM transactions 
                WHERE user_id = '$userIdResult' AND transactions_type = '$type' AND transactions_currency = '$wallet';
                    GROUP BY month HAVING month = MONTH(CURRENT_DATE());";
        } else { // ALL
            $stmt = $connection->prepare("SELECT SUM(CASE WHEN DATE(transactions_date) <= DATE(CURDATE()) THEN transactions_value ELSE 0 END) AS total FROM transactions 
            WHERE transactions_currency = ? AND user_id = ?;");
        }
        
        $stmt->bind_param("ss", $wallet, $userIdResult);
        $stmt->execute();
        $get_result = $stmt->get_result();
        $rows = $get_result->num_rows;
        $result = $get_result->fetch_array();
        if($rows < 1 || !isset($result['total'])) {
            return 0;
        } else {
            return $result['total'];
        }
    }

    function sumCategories () { 
        $query = "SELECT transactions_category, SUM(transactions_value) FROM transactions
            WHERE transactions_type = 'expense' AND user_id = 1
            GROUP BY transactions_category;";
    }

    //Calculate the avg of expenses in the last 30 days.
    function avg() {
        include("connect.inc.php");
        GLOBAL $userIdResult;
        $sql = "SELECT SUM(transactions_value) AS total FROM transactions
        WHERE
        user_id = '$userIdResult ' AND
        transactions_type = 'expense' AND
        transactions_date BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW();";
        $mysqli_query = mysqli_query($connection, $sql);
        $sqlresult = mysqli_fetch_array($mysqli_query);
        $result = $sqlresult['total'] / 30;
        echo round($result, 2);
    }


    //Generates the table with the transactions datas.
    function showTransactionsTable($amount, $show) {
        
        global $connection, $currency_list;
        
        $sessionUser = $_SESSION['email'];
        $userId = mysqli_fetch_array(mysqli_query($connection, "SELECT usersID FROM users WHERE usersEmail = '$sessionUser';"));
        $userIdResult = $userId['usersID'];



        if($show == 'upcoming') {

            $select = "SELECT * FROM transactions 
                    WHERE user_id = '$userIdResult' AND transactions_date >= CURRENT_DATE()
                    ORDER BY transactions_date ASC";

        } elseif ($show == 'history') {
            
            $select = "SELECT * FROM transactions WHERE user_id = '$userIdResult' ORDER BY transactions_date DESC;";

        } else {
            echo "error";
        }

        $mysqlResult = mysqli_query($connection, $select);
        
        if($amount === "all") {
            while ($result = mysqli_fetch_array($mysqlResult)) { 
                $transactionsID = $result['transactions_id']; // general id for table
                $repeatId = $result['transactions_repeat_id'];
                
                if(!empty($repeatId)) {
                    $query_get_all_repeats = "SELECT transactions_date, transactions_id, transactions_repeat_id FROM transactions WHERE transactions_repeat_id = '$repeatId' AND user_id = $userIdResult ORDER BY transactions_date DESC;";
                    $exec_get_all_repeats = mysqli_query($connection, $query_get_all_repeats);
                    $num_of_repeats = mysqli_num_rows($exec_get_all_repeats); // Get the number of repeats.
                    $rep = 0;
                    
                    while ($array = mysqli_fetch_array($exec_get_all_repeats)) {
                        $transactions_id = $array['transactions_id']; //id from the while loop.
                        
                        $rep += 1;
                        if($transactions_id == $transactionsID) {
                            break;
                        }
                    }
                    $rep = $num_of_repeats - $rep + 1;
                }
                    

                if(!is_null($result['transactions_transfer_id'])) {
                    $queryTransfer = "SELECT transactions_id FROM transactions WHERE user_id = '$userIdResult' AND transactions_transfer_id = '$result[transactions_transfer_id]' AND transactions_value < 0;";
                    $queryTransferExec = mysqli_query($connection, $queryTransfer);
                    if(mysqli_num_rows($queryTransferExec) == 1) {
                        $originalTranferId = mysqli_fetch_array($queryTransferExec);
                        $transactionsID = $originalTranferId[0];
                    }
                }
                
                $transactionValue = $result['transactions_value'];
                $transactionRate = $result['transactions_exchange_rate'];
                $transactionCurrency = $result['transactions_currency'];
                $convertedValue = $transactionValue / $transactionRate;

                echo '<tr id="row" style="display: table-row;">
                <td id="transaction-paid"><form action="include/updateTransactionComplete.php?id='. $transactionsID .'" method="post">  <input type="hidden" id="transferID" name="transferID" value="' . $result['transactions_transfer_id'] . '"> 
                <input type="checkbox" value="1" id="checkbox" name="complete" onChange="this.form.submit()"'; if($result['transactions_complete'] == 1) :  echo 'checked'; endif; echo '></form></td>' .
                '<td id="transactionDate">' . date('d/m/Y', strtotime($result['transactions_date'])) .
                '<td>' . ucfirst(strtolower($result['transactions_country'])) . '</td>' .
                '<td id="transaction-type">' . $result['transactions_type'] . '</td>' .
                '<td id="transaction-category">' . $result['transactions_category'] . '</td>' .
                '<td>'; if(!empty($repeatId)) { echo '<span id="rep-span">' . $rep . '/' . $num_of_repeats . '</span>'; } echo $result['transactions_description'] . '</td>' .
                '<td id="transaction-value" data-converted="' . number_format($convertedValue, 4) . '">' . $result['transactions_value'] . '<small>' . $result['transactions_currency'] . '</small>' . '</td>' .
                '<td id="edit">'; 
                if (strtolower($result['transactions_type'] == 'transfer')) { echo "<a href='edit-transfer.php?edit=". (int)$transactionsID . "'>"; } 
                else { echo "<a href='edit.php?edit=". (int)$transactionsID . "'>"; } echo '<i class="fa-solid fa-pencil table-icon"></i>' . '</td>' .
                '<td id="delete">' . "<a id='" . (int)$transactionsID . "'href='#delete-div' onclick='reply_click(this.id);openDeleteAlert();' '>" . '<i class="fa-solid fa-trash-can table-icon"></i>' . "</a>" . '</td></tr>';
            }
        } else {
            for ($i = $amount; $i > 0;$i--){
                $result = mysqli_fetch_array($mysqlResult);
                
                if(!is_null($result)) {

                    if(!is_null($result['transactions_transfer_id'])) {
                        $queryTransfer = "SELECT transactions_id FROM transactions WHERE user_id = '$userIdResult' AND transactions_transfer_id = '$result[transactions_transfer_id]' AND transactions_value < 0;";
                        $queryTransferExec = mysqli_query($connection, $queryTransfer);
                        if(mysqli_num_rows($queryTransferExec) == 1) {
                            $originalTranferId = mysqli_fetch_array($queryTransferExec);
                            $transactionsID = $originalTranferId[0];
                        }
                    }

                    echo 
                    '<a class="history-transaction" href="edit.php?edit='. (int)$result['transactions_id'] . '">
                        <p class="description">' . $result['transactions_description'] . '</p>' .
                        '<p class="value">' . $currency_list[strtoupper($result['transactions_currency'])]['symbol'] . $result['transactions_value'] . '</p>' .
                        '<p class="date">' . date('F j, Y', strtotime($result['transactions_date'])) . '</p>' .
                        '<p class="currency">' . $result['transactions_currency'] . '</p>' . 
                    '</a>';
                } else {
                    echo "";
                }
            }
        }
        
        return mysqli_num_rows($mysqlResult); // used in the Upcoming table to check how many rows the query gets. If zero then the tables display a message.
    }

    function generateCountryList() {
        global $userIdResult, $connection;

        $countriesQuery = "SELECT transactions_country FROM transactions WHERE user_id = '$userIdResult' GROUP BY transactions_country ORDER BY transactions_date DESC;";
        $execCountriesQuery = mysqli_query($connection, $countriesQuery);
        

        while($countriesList = mysqli_fetch_array($execCountriesQuery)) {
            if(!isset($_GET['country'])) {
                echo '<option value="' . strtolower($countriesList['transactions_country']) . '" id="' . strtolower($countriesList['transactions_country']) . '">'. ucfirst(strtolower($countriesList['transactions_country'])) . '</option>';
            } else {
                if(strtolower($countriesList['transactions_country']) == strtolower($_GET['country'])) {
                    echo
                    '<option value="' . strtolower($countriesList['transactions_country']) . '" id="' . strtolower($countriesList['transactions_country']) . '" selected>'. ucfirst(strtolower($countriesList['transactions_country'])) . '</option>';
                } else {
                    echo
                    '<option value="' . strtolower($countriesList['transactions_country']) . '" id="' . strtolower($countriesList['transactions_country']) . '">'. ucfirst(strtolower($countriesList['transactions_country'])) . '</option>';
                }
            }
        }
    }

?>