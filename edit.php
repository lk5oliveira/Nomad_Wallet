<style>
    .content {
        display: flex;
        flex-direction: column;
        gap: 20px;
        align-items: center;
    }

    form {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        width: 80%;
    }

    .top {
        display: flex;
        flex-direction: row;
        position: relative;
        width: 100%;
    }
    
    .fa-arrow-left {
        position: absolute;
        top: 0;
        left: 0;
        font-size: 2rem;
        cursor: pointer;
    }

    .top > h2 {
        width: 100%;
        text-align: center;
    }

    .form-edit {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 10px;
        justify-content: center;
        align-items: center;
    }

    .input-container {
        display: flex;
        flex-direction: column;
        width: 60%;
    }

    input, select {
        padding: 5px;
        border-radius: 10px;
        border: none;
    }

    .button-container {
        display: flex;
        width: 100%;
        justify-content: space-between;
        margin-top: 10px;
        gap: 10px;
    }

    .button-container button {
        width: 30%;
        border-radius: 30px;
        font-weight: 700;
        color: #06113C;
        border: 2px solid #06113C;
        transition: 0.5s;
        height: 3em;
        border: none;
        color: white;
    }

    .button-container a {
        text-align: center;
        padding: 10px;
        transition: 0.2s;
        border-radius: 30px;
        width: 30%;
        font-weight: 700;
        color: white;
        text-decoration: none;
    }

    a:link, a:visited, a:hover, a:active {
        color: white;
        text-decoration: none;
    } 

    .button-container button:hover, .button-container button:focus, .button-container a:hover, .button-container a:focus {
        opacity: 1.2;
        color: white;
        border: none;
        box-shadow: 5px 5px 15px 5px rgba(0,0,0,0.05);
        cursor: pointer;
        text-decoration: none;
    }

    .submit {
        background-color: #98abcd;
    }

    .button-container .delete {
        background-color: #f07167;
    }

    .button-container .cancel {
        background-color: #a4a4a4;
    }

    #amount-repeat {
        width: fit-content !important;
        flex-direction: column;
    }

    #amount-repeat > input {
        width: 40%;
    }

    .switch-paid {
        flex-basis: 100%;
        flex-direction: row;
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
        justify-content: flex-end;
    }
    
    .switch-paid p {
        margin: 0px;
        place-self: center;
        font-weight: 800;
    }

    .switch-paid .switch {
        top: 10% !important;
    }

    .input {
        margin-bottom: 1.5em !important;
    }

    #value-div {
        position: relative;
    }

    #value {
        padding-left: 33px;
    }

    @media screen and (max-width: 480px) {
        #content {
            margin: 0px;
            margin-top: 100px;
        }

        .top {
            display: none;
        }
    }





</style>


<?php session_start(); 

include("include/connect.inc.php");
include("include/check_transaction_is_transfer.php");
include("include/check_transaction_user.php");

if(is_null($_GET['edit'])) { // check if the id is given by URL parameters.
    echo '<script>history.go(-1);</script>';
    exit();
}

$id = (INT)$_GET['edit'];
$userId = $_SESSION['userID'];

if(transactionBelongsToUser($id, $userId) == false) { //check if the transaction belongs to the session user
    echo '<script>history.go(-1);</script>';
    exit();
}

if(isTransfer($id) == 'transfer') { // check if the user is not trying to edit a transfer transaction from these page.
    echo '<script>history.go(-1);</script>';
    exit();
}



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
'Transport',];

include('include/world-currency.php');
include('include/connect.inc.php');



$editId = $_GET['edit'];
$query = "SELECT * FROM transactions WHERE transactions_id = '$editId';";
$queryExec = mysqli_query($connection, $query);
$editResult = mysqli_fetch_array($queryExec);
$transactionCategory = $editResult['transactions_category'];
$repeatId = $editResult['transactions_repeat_id'];
$paid = $editResult['transactions_complete'];

if($repeatId > 0){
    $query_get_all_repeats = "SELECT transactions_date, transactions_id, transactions_repeat_id FROM transactions WHERE transactions_repeat_id = $repeatId AND user_id = $userId ORDER BY transactions_date DESC;";
    $exec_get_all_repeats = mysqli_query($connection, $query_get_all_repeats);
    $num_of_repeats = mysqli_num_rows($exec_get_all_repeats); // Get the number of repeats.
    $array_of_repeats = mysqli_fetch_all(mysqli_query($connection, $query_get_all_repeats));
    //$arrayA = mysqli_fetch_array($exec_get_all_repeats);
    $rep = 0;
    
    while ($array = mysqli_fetch_array($exec_get_all_repeats)) {
        $transactions_id = $array['transactions_id'];
        
        $rep += 1;
        if($transactions_id == $editId) {
            break;
        }
    }

    $rep = $num_of_repeats - $rep + 1;
    
}


?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="img/fav_icons/fav_icon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.sandbox.google.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <script src="include/JS/menu.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://kit.fontawesome.com/a440aae6fe.js" crossorigin="anonymous"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js" type="text/javascript"></script>
    <title>Document</title>
</head>
<body>
    
    <div id="main-container">

    <?php include('menu.php'); ?>

    <?php include('areusure.php'); ?>

    <div class="page-title">
            <h2 class="title-text">Edit</h2>
    </div>

        <div class="content" id="content">

            <div class="top">
                <i onclick="history.back()" class="fa-solid fa-arrow-left"></i>
                <h2>EDIT</h2>
            </div>

            <?php if(isset($_SESSION['validateEdit']) && $_SESSION['validateEdit'] == false) : ?>
            <div class="alert">
                    <span>Error, please try again.</span>
            </div>
            <?php endif ?>

            <?php include('include/get_edit_info.php'); ?>
                <form action="include/update.php?edit=<?= $editId ?>" method="post">

                    <?php if(!empty($repeatId) && $rep != $num_of_repeats) : ?>

                        <div id="repeat-info">
                            <p class="rep-num">Transaction <?= $rep . '/' . $num_of_repeats; ?> </p>
                            <div class="switch-update-all">
                                <p>Update next transactions</p>
                                <label class="switch">
                                    <input id="checkbox" type="checkbox" name="update-next" value="on">
                                <span class="slider round"></span>
                            </div>
                        </label>
                        </div>

                    <?php endif ?>

                        <div class="switch-paid">
                            <p>Paid</p>
                            <label class="switch">
                                <input id="checkbox" type="checkbox" name="paid" value="1" <?php if ($paid == 1) : ?> checked <?php endif ?> >
                            <span class="slider round"></span>
                        </div>

                    

                    <div class="input">
                        <label for="type">Wallet</label>
                        <select type="text" name="wallet" id="wallet" onchange="updateCurrency('wallet', 'value-symbol')">
                        <?php 
                            $arr_keys = array_keys($currency_list);
                            $arr_keys_size = sizeof($arr_keys) - 1;
                            for($i = 0;$i <= $arr_keys_size;$i++){
                                if(strtolower($arr_keys[$i]) == strtolower($editResult[8])) {
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
                        <input type="date" name="date" id="date" value="<?= date('Y-m-d', strtotime($editResult[1]));?>">
                    </div>

                    <div class="input">
                        <label for="description">Description</label>
                        <input type="text" name="description" id="description" value="<?= htmlspecialchars($editResult[2]); ?>" required>
                    </div>

                    <div class="input">
                        <label for="type">Type</label>
                        <select type="text" name="type" id="type" required>
                            <?php if ($editResult[3] == 'income'): ?>
                                <option value="income" selected>Income</option>
                                <option value="expense">Expense</option> 
                            <?php else : ?>
                                <option value="income">Income</option>
                                <option value="expense" selected>Expense</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="input">
                        <label for="type">Category</label>
                        <select type="text" name="category" id="category" required>
                            <?php
                            foreach($category as $item) {
                                if(strtoupper($item) == strtoupper($transactionCategory)){
                                    echo '<option value="' . strtolower($item) . '" selected>' . $item . '</option>';
                                } else {
                                    echo '<option value="' . strtolower($item) . '">' . $item . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="input" id="value-div">
                        <label for="value">Value</label>
                        <input name="value" type="tel" id="value" data-js="money" value='<?= number_format(abs($editResult[4]), 2, ",", ".") ?>' required />
                        <span class="currency-symbol" id="value-symbol"><?= $editResult[8]; ?></span>
                    </div>

                    <div class="input" id="country">
                        <label for="country">Country</label>
                        <select name="country" type="text" id="country-field" required>
                            <?php for($i = 0;$i <= $countryArraySize;$i++) {
                                if(strtolower($countryCurrency[$i]['country']) == strtolower($editResult[7])) {
                                    echo '<option value="' . strtolower($countryCurrency[$i]['country']) . '" selected>' . ucwords(strtolower($countryCurrency[$i]['country'])) . '</option>';
                                } 

                                else {
                                    echo '<option value="' . strtolower($countryCurrency[$i]['country']) . '">' . ucwords(strtolower($countryCurrency[$i]['country'])) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="input symbol-div" id="exchange">
                        <label for="exchange">Exchange rate</label>
                        <input name="exchange" type="text" class="" id="exchange-field" data-js="money" value='<?= number_format(abs($editResult[9]), 2, ",", ".")?>' required>
                    </div>

                <div class="button-container">
                    <button class="cancel" onclick="history.back()">cancel</button>
                    
                    <a href="#delete-div" class="delete" onclick="openDeleteAlert();disableInput();">delete</a>

                    <?php if(!empty($repeatId) && $rep != $num_of_repeats) : ?>

                        <a href="#delete-div" class="delete" onclick="openDeleteAlert();enableInput();">delete all</a>

                    <?php endif ?>
                    <button class="submit" type="submit" name="update">update</button>
                </div>
            </form>

        </div>
        
    </div>


</body>

</html>

<script src="include/JS/transactionsForm.js"></script>

<script>

getTransactionId(); // Get the transaction ID for delete action




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

//Toggle country and currency fields - only show when travel wallet is selected.


let wallet = document.getElementById('wallet');
let defaultCurrency = '<?= $_SESSION['defaultCurrency'] ?>';
let currencyCode = '<?= $_SESSION['currencyCode'] ?>';
let exchangeInput = document.getElementById('exchange-field');
let defaultCurrencySymbol = '<?= $_SESSION['defaultSymbol'] ?>';
let travelCurrencySymbol = '<?= $_SESSION['currencySymbol'] ?>';
let convertedDiv = document.getElementById('converted-div');
let currency = document.getElementById('currency');
let currencyInput = document.getElementById('currency-field');
let transactionType = document.getElementById('type');
let valueSymbol = document.getElementById('value-symbol');




</script>