<?php
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

include('include/world-currency.php');
include_once('include/connect.inc.php');

$validation = true;
$value = '';
$description = '';


if($_SERVER['REQUEST_METHOD'] === 'POST') {

    $date = explode('-', $_POST['date']);
    $description = $_POST['description'];
    $month = $date[1];
    $day = $date[2];
    $year = $date[0];
    $type = $_POST['type'];
    $categoryPost = ucfirst($_POST['category']);
    $currency = $_POST['currency'];
    $country = $_POST['country'];
    $value = $_POST["value"];
    
    $validationCategory = false;
    $validationCountry = false;
    $validationCurrency = false;

    foreach($category as $catValue) {
        if($catValue === $categoryPost) {
            $validationCategory = true;
            echo 'cat ok';
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
        if (strtolower($countryCurrency[$i]['currency_code']) == strtolower($currency)) {
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
    

    if($validation = true) {
        $date = $_POST["date"]; // transaction date
        $value = str_replace(',','.',str_replace('.', '', $_POST["value"])); // transaction value
        $type = $_POST["type"]; // transfer, income, expense
        $category = '';
        
        $country = strtoupper($_POST['country']);
        $currency = strtoupper($_POST['currency']);

        $repeat = null;
        $period = null;
        $repeatTime = null;

        if(isset($_POST['repeat'])) {
            $repeat = $_POST['repeat'];
            $period = $_POST['repeat-period'];
            $repeatTime = $_POST['amount-repeat'];        
        }
        $i = 1;
        $generatedId;
    
    
       
    
        /* Check if Repeat is on. If on, then checked the number of times to repeat and repeat peiord.
        If repeat is off, then repeat time is 1.
        A loop will be created in the query, and sum 1 week or 1 month in the current transaction date.
        This will add multiple transactions with the same values for the next weeks or months.*/
        if($repeat != 'on') {
            $repeatTime = 1;
        }
    
        // generates the transfer ID or repeat ID - this id is usable to access all the transactions related to the transfer or the repeat.
        // $type = transactions_transfer_id or transactions_repeat_id;
        function createIdForMultipleTransfers($generateForColumn) {
            include_once('include/connect.inc.php');
    
            global $userIdResult, $generatedId, $connection, $type;
    
            $queryTransferId = "SELECT max($generateForColumn) FROM transactions WHERE $generateForColumn IS NOT NULL AND user_id = $userIdResult;";
            $execQueryTransferId = mysqli_query($connection, $queryTransferId);
            $lastTransferId = mysqli_fetch_array($execQueryTransferId);
    
            if (!isset($_POST['repeat']) && $type != 'transfer') {
                $generatedId = NULL;
                echo "not set";
            } elseif (isset($lastTransferId)) {
                $generatedId = $lastTransferId[0] + 1;
                echo "is set";
            } else {
                $generatedId = 1;
                "first time";
            }
            
        }
       
    
        // Get the userID on the current section
        
        $sessionUser = $_SESSION['email'];
        $userId = mysqli_fetch_array(mysqli_query($connection, "SELECT usersID FROM users WHERE usersEmail = '$sessionUser';"));
        $userIdResult = $userId['usersID'];
    
        // Insert the transaction
    
        if($type == 'expense') { // EXPENSE TRANSACTION (the value is passed with PHP as negative)
            
            createIdForMultipleTransfers('transactions_repeat_id');
            $category = $_POST["category"]; // If type is transfer then this input field is disabled, thus the key will not exist in a global scope - it's not in gloabal scope to avoid errors.
    
            for($i = 0; $i < $repeatTime;$i++) {
                $date = $_POST["date"];
                if($period == 'monthly') {
                    $date = date('Y-m-d', strtotime("+$i months", strtotime($date)));
    
                    $sql_insert_sql = "INSERT INTO transactions (transactions_date, transactions_description, transactions_type, transactions_value, transactions_category, transactions_country, transactions_currency, transactions_repeat_id, user_id)
                    VALUES ('$date','$description', '$type', '-$value', '$category', '$country', '$currency', '$generatedId', '$userIdResult');";
            
                    mysqli_query($connection, $sql_insert_sql);
                } else {
                    $date = date('Y-m-d', strtotime("+$i weeks", strtotime($date)));
    
                    $sql_insert_sql = "INSERT INTO transactions (transactions_date, transactions_description, transactions_type, transactions_value, transactions_category, transactions_country, transactions_currency, transactions_repeat_id, user_id)
                    VALUES ('$date','$description', '$type', '-$value', '$category', '$country', '$currency', '$generatedId', '$userIdResult');";
            
                    mysqli_query($connection, $sql_insert_sql);
                }
            }
    
        } else { // INCOME TRANSACTION
            createIdForMultipleTransfers('transactions_repeat_id');
            $category = $_POST["category"]; // If type is transfer then this input field is disabled, thus the key will not exist in a global scope - it's not in gloabal scope to avoid errors.
            echo 'oi';
            for($i = 0; $i < $repeatTime;$i++) {
                $date = $_POST["date"];
                if($period == 'monthly') {
                    $date = date('Y-m-d', strtotime("+$i months", strtotime($date)));
                    $sql_insert_sql = "INSERT INTO transactions (transactions_date, transactions_description, transactions_type, transactions_value, transactions_category, transactions_country, transactions_currency, transactions_repeat_id, user_id)
                    VALUES ('$date','$description', '$type', '$value', '$category', '$country', '$currency', '$generatedId', '$userIdResult');";
            
                    mysqli_query($connection, $sql_insert_sql);
                } else {
                    $date = date('Y-m-d', strtotime("+$i weeks", strtotime($date)));
                    echo 'week';
                    $sql_insert_sql = "INSERT INTO transactions (transactions_date, transactions_description, transactions_type, transactions_value, transactions_category, transactions_country, transactions_currency, transactions_repeat_id, user_id)
                    VALUES ('$date','$description', '$type', '$value', '$category', '$country', '$currency', '$generatedId', '$userIdResult');";
            
                    mysqli_query($connection, $sql_insert_sql);
                }
            }
    
        }
        
        //header('Location:' . $_SERVER['HTTP_REFERER']);
        print_R($_POST);
    }
    
}

?>

    <div class="form-container" id="form-container" style="display: none;">
            
            
            <div class="transaction-form" id="transaction-form">
                <h2 class='title' id='title'></h2>
                <i class="fa-solid fa-xmark" id="close-icon" onclick="closeForm()"></i>
                <?php if ($validation == false) : ?>
                <div class="alert">
                    <span>Error, please try again.</span>
                </div>
                <?php endif ?>
                <form action="" method="POST">
                    <div class="input">
                    <label for="currency">Wallet</label>
                        <select name="currency" type="text" id="currency-field-to" required>
                            <?php 
                                $arr_keys = array_keys($currency_list);
                                $arr_keys_size = sizeof($arr_keys) - 1;
                                for($i = 0;$i <= $arr_keys_size;$i++){
                                    if(strtolower($arr_keys[$i]) == strtolower($_SESSION['defaultCurrency'])) {
                                        echo '<option value="' . strtolower($arr_keys[$i]) . '" selected>' . ucwords(strtolower($currency_list[$arr_keys[$i]]['name'])) . '</option>';
                                    } else {
                                    echo '<option value="' . strtolower($arr_keys[$i]) . '">' . ucwords(strtolower($currency_list[$arr_keys[$i]]['name'])) . '</option>';
                                    }
                                }
                                ?>
                        </select>
                    </div>

                    <div class="input">
                        <label for="date">Date</label>
                        <input type="date" name="date" id="date" value="<?= date('Y-m-d'); ?>">
                    </div>

                    <div class="input">
                        <label for="description">Description</label>
                        <input type="text" name="description" id="description" value="<?=$description?>" required>
                    </div>

                    <div class="input">
                        <label for="type">Type</label>
                        <select type="text" name="type" id="type" required>
                            <option value="income">Income</option>
                            <option value="expense">Expense</option>
                        </select>
                    </div>

                    <div class="input">
                        <label for="type">Category</label>
                        <select type="text" name="category" id="category" required>
                            <option value="" disabled>select</option>
                            <?php
                            foreach($category as $item) {
                                echo '<option value="' . strtolower($item) . '">' . $item . '</option>';
                            }
                            ?>
                            <option value="transfer" id="transferCat" style="display: none;">Transfer</option>
                        </select>
                    </div>
                    
                    <div class="input symbol-div">
                        <label for="value">Value</label>
                        <input name="value" type="text" class="symbol-input" data-js="money" id="value" value='<?= $value; ?>' required />
                        <!--<span class="currency-symbol" id="value-symbol"></span>-->
                    </div>

                    <div class="input" id="country">
                        <label for="country">Country</label>
                        <select name="country" type="text" id="country-field" required>
                            <option value="select" disabled>select</option>
                            <?php for($i = 0;$i <= $countryArraySize;$i++) {
                                if($countryCurrency[$i]['country'] == strtoupper($_SESSION['country'])) {
                                    echo '<option value="' . ucfirst(strtolower($countryCurrency[$i]['country'])) . '" selected>' . ucfirst(strtolower($countryCurrency[$i]['country'])) . '</option>';
                                } else {
                                    echo '<option value="' . ucfirst(strtolower($countryCurrency[$i]['country'])) . '">' . ucfirst(strtolower($countryCurrency[$i]['country'])) . '</option>';
                                }
                            }

                            ?>
                        </select>
                    </div>
                                        
                    <div class="checkbox" id="repeat-div">
                        <div class="repeat-box repeat">
                            <label for="repeat">Repeat</label>
                            <input name="repeat" type="checkbox" id="repeat" onclick="toggleRepeat();" />
                        </div>

                        <div class="repeat-radio repeat" id="radio" style="display: none;">
                            <label for="value">weekly</label>
                            <input name="repeat-period" type="radio" id="weekly" value="weekly" />
                            <label for="value">Monthly</label>
                            <input name="repeat-period" type="radio" id="montlhy" value="monthly" />
                        </div>

                        <div class="repeat-period repeat input" id="amount-repeat" style="display: none;">
                            <label for="value">Amount of repeats</label>
                            <input name="amount-repeat" type="number" id="amount-times" min="2" step="1" max='24'/>
                        </div>
                        

                    </div>
                    
                    <div class="button-container">
                        <button class="submit-transaction" type="submit" name="submit">Add</button>
                    </div>
                </form>
            </div>
            
        </div>


<script>
let transactionType = document.getElementById('type');


const $money = document.querySelector('[data-js="money"]');

$money.value = "0,00";

$money.addEventListener(
  "input",
  (e) => {
    e.target.value = maskMoney(e.target.value);
  },
  false
);

function maskMoney(value) {
  const valueAsNumber = value.replace(/\D+/g, "");
  return new Intl.NumberFormat("pt-BR", {
    style: 'decimal', 
    minimumFractionDigits: 2, 
    maximumFractionDigits: 2
  }).format(valueAsNumber / 100);
}

</script>


<script>

//Toggle repeat fields
    /*declaring variables*/
    let repeat = document.getElementById('repeat');
    let radio = document.getElementById('radio');
    let amount = document.getElementById('amount-repeat');

function toggleRepeat() {

    if (repeat.checked == true) {     //show the fields
        radio.style.display = 'block';
        amount.style.display = 'flex';

    } else {     //hide the fields
        radio.style.display = 'none';
        amount.style.display = 'none'
    }
}

let lightBox = document.getElementById('form-container');
let transferType = document.getElementById('type');
let title = document.getElementById('title');
let main = document.getElementById('main-container');
let body = document.getElementsByTagName('BODY')[0];

function displayForm(type) {
    lightBox.style.display = 'flex';
    transferType.value = type;
    title.innerHTML = type;
    body.style.overflow = 'hidden';
}

function closeForm() {
    lightBox.style.display = 'none';
    body.style.overflow = 'auto';
}

<?php if ($validation == false) : ?>
    displayForm('expense');
<?php endif ?>


</script>