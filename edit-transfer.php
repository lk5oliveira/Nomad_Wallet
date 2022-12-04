
<?php 

session_start();
include('include/login/verify_login.inc.php');
backToIndex();



include("include/connect.inc.php");
include("include/world-currency.php");
include("include/check_transaction_is_transfer.php");
include("include/check_transaction_user.php");

/* Exchange rate variables */ 
$exchangeRates = $_SESSION['exchangeRates'];
$decoded_rates = json_encode($_SESSION['exchangeRates']); // JSON enconded for JS
$localCurrency = $_SESSION['currencyCode'];
$localRate = $exchangeRates->$localCurrency;


/* SECURITY CHECK */

if(is_null($_GET['edit'])) { // check if the id is given by URL parameters.
    header('Location: ../history.php');
    exit();
}

$transactionId = (INT)$_GET['edit'];
$userId = $_SESSION['userID'];

if(transactionBelongsToUser($transactionId, $userId) == false) { //check if the transaction belongs to the session user
    header('Location: ../history.php');
    exit();
}

if(isTransfer($transactionId) != 'transfer') { // check if the user is editing the transaction on the right edit page.
    header('Location: ../history.php');
    exit();
}

/* END OF SECURITY CHECK */

/* GET THE TRANSFER DATA */

$stmt = $connection->prepare("SELECT * FROM transactions WHERE transactions_id = ?"); // Query to get the transfer id.
$stmt->bind_param("s", $transactionId);
$stmt->execute();

$stmt_result = $stmt->get_result();
$getTransactionData = $stmt_result->fetch_array();

$transferId = $getTransactionData['transactions_transfer_id'];

$stmt = $connection->prepare("SELECT * FROM transactions WHERE transactions_transfer_id = ?");
$stmt->bind_param("s", $transferId);
$stmt->execute();

$stmt_result = $stmt->get_result();
$transferDataArray= $stmt_result->fetch_all();

$transfer_from_id = $transferDataArray[0][0];
$transfer_from_value = number_format(abs($transferDataArray[0][4]), 2, ',', '.');
$transfer_from_currency = $transferDataArray[0][8];

$transfer_to_id = $transferDataArray[1][0];
$transfer_to_value = number_format($transferDataArray[1][4], 2, ',', '.');
$transfer_to_currency = $transferDataArray[1][8]; //currency

$transfer_rate = number_format($transferDataArray[0][9], 2, ',', '.');
$transfer_date = $transferDataArray[0][1]; 
$transfer_country = $transferDataArray[0][7];


$validationMessage = '';

if (isset($_POST["submit"])) {
    $from = strtoupper($_POST['from']);
    $to = strtoupper($_POST['to']);
    $valueFrom = floatval(str_replace(',','.',str_replace('.', '', $_POST['value-from'])));
    $valueTo = floatval(str_replace(',','.',str_replace('.', '', $_POST['value-to'])));
    $exchangeRate = floatval(str_replace(',','.',str_replace('.', '', $_POST['exchange'])));
    $date = $_POST['date'];
    $currentCountry = $_POST['country'];
    $description = "transfer from: " . $from . " to " . $to;
    $userId = $_SESSION['userID'];
    $type = 'transfer';

    /* Form validation */

    if(empty($valueFrom) || empty($valueTo) || empty($exchangeRate)) {
        $validationMessage = "Type a value.";
        $validation_values = "validation-error";
    }
    elseif(!is_numeric($valueFrom) || !is_numeric($valueTo) || !is_numeric($exchangeRate)) {
        $validationMessage = "Only numbers are allowed.";
        $validation_values = 'validation-error';
    }
    elseif ($valueFrom <= 0 || $valueTo <= 0 || $exchangeRate <= 0) {
        $validationMessage = "Values can't be 0,00 or less.";
        $validation_values = 'validation-error';
    } 

    if($validationMessage == "") {
        if($connection->connect_error) {
            die("Connection failed: " . $connection->connect_error);
        }

        $valueFrom = 0 - floatval(str_replace(',','.',str_replace('.', '', $_POST['value-from'])));
        
        // Prepare statment

        $stmt = $connection->prepare("UPDATE `transactions` SET `transactions_date` = ?, `transactions_description` = ?, 
        `transactions_value` = ?, `transactions_country` = ?, 
        `transactions_currency` = ?, transactions_exchange_rate = ?
        WHERE `transactions_id` = ? AND user_id = '$userId';");

        // EDIT VALUES FROM THE ACCOUNT WHERE THE MONEY IS TRANSFERED FROM.
        $stmt->bind_param("sssssss", $date, $description, $valueFrom, $currentCountry, $from, $exchangeRate, $transfer_from_id);
        $stmt->execute();

        // EDIT VALUES FROM THE ACCOUNT THAT RECEIVES THE MONEY
        $stmt->bind_param("sssssss", $date, $description, $valueTo, $currentCountry, $to, $exchangeRate, $transfer_to_id);
        $stmt->execute();

        $stmt->close();
        $connection->close();
    }

    header('Location: history.php');
    exit();
}


?>

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
        justify-content: center;
        gap: 20px;
        max-width: 700px;
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
        margin-bottom: 0px !important;
        width: 260px !important;
    }

    #value-div {
        position: relative;
    }

    .value {
        padding-left: 40px !important;
        width: 50% !important;
    }

    .currency-symbol {
        font-size: 12px;
        bottom: 8px !important;
    }

    #exchange, #country, #date {
        flex-basis: 30%;
    }

    .fa-right-left {
        position: absolute;
        right: 8%;
        bottom: 10%;
        font-size: 1.5rem;
        transform: rotateZ(90deg);
    }

    .transfer-from, .transfer-to {
        width: 100%;
        padding: 20px;
        border-radius: 20px;
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        color: white;
        background: rgb(24,61,85);
        background: linear-gradient(156deg, rgb(150 150 150) 32%, rgb(55 55 55) 100%);
        position: relative;
    }

    .transfer-from h3, .transfer-to h3 {
        flex-basis: 100%;
    }


    @media screen and (max-width: 480px) {
        #content {
            margin: 0px;
            margin-top: 100px;
        }

        .top {
            display: none;
        }

        #exchange, #date {
            flex-basis: 40%;
        }

        #country {
        flex-basis: 90%;
        }

        .fa-right-left:active {
            color: #f66b0e;
            cursor: pointer;
        }


    }

    @media screen and (min-width: 1024px) {
        .fa-right-left:hover {
        color: #f66b0e;;
        cursor: pointer;
    } 
    }

</style>


<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <title>Edit transfer</title>
</head>
<body>
    
    <div id="main-container">

    <?php include('menu.php'); ?>

    <?php include('areusure.php'); ?>

    <div class="page-title">
            <h2 class="title-text">Edit Transfer</h2>
    </div>

        <div class="content" id="content">

            <div class="top">
                <i onclick="history.back()" class="fa-solid fa-arrow-left"></i>
                <h2>TRANSFER</h2>
            </div>

            <?php if($validationMessage != "") : ?>
                <div class="alert">
                    <span> <?= $validationMessage ?> </span>
                </div>
            <?php endif ?>
                <form action="" method="POST">
                    
                    <div class="transfer-from">
                        <h3>From</h3>
                        <div class="input" id="currency">
                            <label for="currency">Currency</label>
                            <select name="from" type="text" id="currency-field-from" onchange="updateCurrency('currency-field-from', 'currency-code-from');" required>
                                <?php 
                                    $arr_keys = array_keys($currency_list);
                                    $arr_keys_size = sizeof($arr_keys) - 1;
                                    for($i = 0;$i <= $arr_keys_size;$i++){
                                        if(strtolower($arr_keys[$i]) == strtolower($transfer_from_currency)) {
                                            echo '<option value="' . strtolower($arr_keys[$i]) . '" selected>' . ucwords(strtolower($currency_list[$arr_keys[$i]]['name'])) . '</option>';
                                        } else {
                                        echo '<option value="' . strtolower($arr_keys[$i]) . '">' . ucwords(strtolower($currency_list[$arr_keys[$i]]['name'])) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="input" id="value-div">
                            <label for="value">Value</label>
                            <input name="value-from" type="tel" class="value" id="value-from" data-js="money" value='<?= $transfer_from_value; ?>' onchange="calculateRate('value-from', 'value-to')" required />
                            <span class="currency-symbol" id="currency-code-from"></span>
                        </div>
                        <i class="fa-solid fa-right-left" onclick="invertCurrencyButton();"></i>
                    </div>
                    <div class="input symbol-div" id="exchange">
                        <label for="exchange">Exchange rate</label>
                        <input name="exchange" type="tel" data-js="money" class="symbol-input" id="exchange-field" data-rate='<?= $transfer_rate ?>' value='<?= $transfer_rate; ?>' required>
                        <span class="currency-symbol" id="exchange-currency-code"></span>
                    </div>

                    <div class="input" id="date">
                        <label for="date">Date</label>
                        <input type="date" name="date" id="date" value="<?= date('Y-m-d', strtotime($transfer_date)); ?>">
                    </div>

                    <div class="input" id="country">
                        <label for="country">Current Country</label>
                        <select name="country" type="text" id="country-field" onchange="getCountryCurrency();" required>
                            <?php for($i = 0;$i <= $countryArraySize;$i++) {
                                if(strtolower($countryCurrency[$i]['country']) == strtolower($transfer_country)) {
                                    echo '<option value="' . strtolower($countryCurrency[$i]['country']) . '" selected>' . ucwords(strtolower($countryCurrency[$i]['country'])) . '</option>';
                                } else {
                                    echo '<option value="' . strtolower($countryCurrency[$i]['country']) . '">' . ucwords(strtolower($countryCurrency[$i]['country'])) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="transfer-to">
                        <h3>To</h3>
                        <div class="input" id="currency">
                            <label for="currency">Currency</label>
                            <select name="to" type="text" id="currency-field-to" onchange="updateCurrency('currency-field-to', 'currency-code-to');" required>
                                <?php 
                                        for($i = 0;$i <= $arr_keys_size;$i++){
                                            if(strtolower($arr_keys[$i]) == strtolower($transfer_to_currency)) {
                                                echo '<option value="' . strtolower($arr_keys[$i]) . '" selected>' . ucwords(strtolower($currency_list[$arr_keys[$i]]['name'])) . '</option>';
                                            }
                                            echo '<option value="' . strtolower($arr_keys[$i]) . '">' . ucwords(strtolower($currency_list[$arr_keys[$i]]['name'])) . '</option>';
                                        }
                                    ?>
                            </select>
                        </div>

                        <div class="input" id="value-div">
                            <label for="value">Value</label>
                            <input name="value-to" type="tel" class="value" id="value-to" data-js="money" value='<?= $transfer_to_value ?>' required />
                            <span class="currency-symbol" id="currency-code-to"></span>
                        </div>
                    </div>

                <div class="button-container">
                    <a href="#delete-div" class="delete" onclick="openDeleteAlert();disableInput();">delete</a>
                    <button class="cancel" onclick="history.back()">cancel</button>
                    <button class="submit" type="submit" name="submit" onClick="this.form.submit(); this.disabled=true; this.value='Sending…'; this.style.background='gray'; ">Transfer</button>
                </div>
            </form>

        </div>

    </div>
</body>

<script>
    let array_rates = <?=  $decoded_rates  ?>;
    let defaultCurrency = '<?= $_SESSION['defaultCurrency'] ?>';
</script>
<script src="include/JS/transferPage.js"></script>
<script src="include/JS/transactionsForm.js"></script>


</html>