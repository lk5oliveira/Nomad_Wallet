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

if(isTransfer($id) == 'transfer') { // check if the user is editing the transaction on the right edit page.
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
$editResult = mysqli_fetch_row($queryExec);
$transactionCategory = $editResult[7];
$repeatId = $editResult[13];
$paid = $editResult[11];



if(!empty($repeatId)){
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
                        <select type="text" name="wallet" id="wallet" onchange="toggleCountryCurrency();">
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
                        <input type="date" name="date" id="date" value="<?= date('Y-m-d', strtotime($editResult[1]));?>">
                    </div>

                    <div class="input">
                        <label for="description">Description</label>
                        <input type="text" name="description" id="description" value="<?= $editResult[2]; ?>" required>
                    </div>

                    <div class="input">
                        <label for="type">Type</label>
                        <select type="text" name="type" id="type" onchange="transferCategory();" required>
                            <?php if($editResult[3] == 'transfer') : ?>
                                <option value="transfer" selected>Transfer</option> 
                            <?php elseif ($editResult[3] == 'income'): ?>
                                <option value="income" selected>Income</option>
                                <option value="expense">Expense</option> 
                            <?php elseif ($editResult[3] == 'expense') : ?>
                                <option value="income">Income</option>
                                <option value="expense" selected>Expense</option>
                            <?php else : ?>
                                <option value="income" selected>Income</option>
                                <option value="expense">Expense</option>   
                            <?php endif ?>
                        </select>
                    </div>

                    <div class="input">
                        <label for="type">Category</label>
                        <select type="text" name="category" id="category" onchange="transferOnChange();" required>
                            <?php
                            foreach($category as $item) {
                                if(strtoupper($item) == strtoupper($transactionCategory)){
                                    echo '<option value="' . strtolower($item) . '" selected>' . $item . '</option>';
                                } else {
                                    echo '<option value="' . strtolower($item) . '">' . $item . '</option>';
                                }
                            }
                            ?>
                            <option value="transfer" id="transferCat" style="display: none;">Transfer</option>
                        </select>
                    </div>

                    <div class="input" id="value-div">
                        <label for="value">Value</label>
                        <input name="value" type="tel" id="value" data-js="money" value='<?= number_format(abs($editResult[4]), 2, ",", ".") ?>' required />
                        <span class="currency-symbol" id="value-symbol"></span>
                    </div>

                    <div class="input" id="country">
                        <label for="country">Country</label>
                        <select name="country" type="text" id="country-field" required>
                            <?php for($i = 0;$i <= $countryArraySize;$i++) {
                                if(ucfirst(strtolower($countryCurrency[$i]['country'])) == $editResult[8]) {
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
                        <input name="exchange" type="text" class="symbol-input" id="exchange-field" value='<?= number_format($_SESSION['currencyRateHome'], 2, ",", ".")?>' required>
                        <span class="currency-symbol" id="exchange-symbol"></span>
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

<script>
const $money = document.querySelector('[data-js="money"]');

$money.value = "<?= number_format(abs($editResult[4]), 2, ",", ".") ?>";

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


function updateExchangeLabel() {
    let convertedLabel = document.getElementById('converted-label');

    if (wallet.value == 'home' && transactionType.value == 'transfer') {
        convertedLabel.innerHTML = defaultCurrency + ' to ' + currencyInput.value;
    } else if (wallet.value == 'travel' && transactionType.value == 'transfer') {
        convertedLabel.innerHTML = currencyInput.value + ' to ' +  defaultCurrency;
    }
}

function calculateExchangeRate() {
    let transactionValue = document.getElementById('value');
    let convertedInput = document.getElementById('converted-input');
    let currencyRateHome = '<?= number_format($_SESSION['currencyRateHome'], 2, ',', '.');?>';
    let currencyRateTravel = '<?= number_format($_SESSION['currencyRateTravel'], 2, ',', '.');?>';
    let exchangeSymbol = document.getElementById('exchange-symbol');
    let convertedSymbol = document.getElementById('converted-symbol');
    console.log(currencyRateHome);
    updateExchangeLabel()

    if (wallet.value == 'home' && transactionType.value == 'transfer') {

        exchangeInput.value = currencyRateHome;
        valueSymbol.innerHTML = defaultCurrencySymbol;
        exchangeSymbol.innerHTML = travelCurrencySymbol;
        convertedSymbol.innerHTML = travelCurrencySymbol;
        currencyInput.value = currencyCode;
    
    } else if (wallet.value == 'travel' && transactionType.value == 'transfer') {

        exchangeInput.value = currencyRateTravel;
        valueSymbol.innerHTML = travelCurrencySymbol;
        exchangeSymbol.innerHTML = defaultCurrencySymbol;
        convertedSymbol.innerHTML = defaultCurrencySymbol;
        currencyInput.value = currencyCode;

    } else if (wallet.value == 'home') {
        valueSymbol.innerHTML = defaultCurrencySymbol;
        currencyInput.value = defaultCurrency;
               
    } else if (wallet.value == 'travel') {
        valueSymbol.innerHTML = travelCurrencySymbol;
        currencyInput.value = currencyCode; 
    }
    let getValueTranaction = transactionValue.value;
    let transactionValueRegex = getValueTranaction.replace(/[.]+/g, '');
    let value = transactionValueRegex.replace(/[,]+/g, '.');

    let getValueExchange = exchangeInput.value;
    let exchangeValueRegex = getValueExchange.replace(/[.]+/g, '');
    let exchangeValue = exchangeValueRegex.replace(/[,]+/g, '.');


    let conversion = parseFloat(value) * parseFloat(exchangeValue);
    conversion = conversion || 0;
    convertedInput.value = conversion.toFixed(2);

}

let countryList = [
    {
        "country": "Afghanistan",
        "currency_code": "AFN"
    },
    {
        "country": "Albania",
        "currency_code": "ALL"
    },
    {
        "country": "Algeria",
        "currency_code": "DZD"
    },
    {
        "country": "American Samoa",
        "currency_code": "USD"
    },
    {
        "country": "Andorra",
        "currency_code": "EUR"
    },
    {
        "country": "Angola",
        "currency_code": "AOA"
    },
    {
        "country": "Anguilla",
        "currency_code": "XCD"
    },
    {
        "country": "Antarctica",
        "currency_code": "XCD"
    },
    {
        "country": "Antigua and Barbuda",
        "currency_code": "XCD"
    },
    {
        "country": "Argentina",
        "currency_code": "ARS"
    },
    {
        "country": "Armenia",
        "currency_code": "AMD"
    },
    {
        "country": "Aruba",
        "currency_code": "AWG"
    },
    {
        "country": "Australia",
        "currency_code": "AUD"
    },
    {
        "country": "Austria",
        "currency_code": "EUR"
    },
    {
        "country": "Azerbaijan",
        "currency_code": "AZN"
    },
    {
        "country": "Bahamas",
        "currency_code": "BSD"
    },
    {
        "country": "Bahrain",
        "currency_code": "BHD"
    },
    {
        "country": "Bangladesh",
        "currency_code": "BDT"
    },
    {
        "country": "Barbados",
        "currency_code": "BBD"
    },
    {
        "country": "Belarus",
        "currency_code": "BYR"
    },
    {
        "country": "Belgium",
        "currency_code": "EUR"
    },
    {
        "country": "Belize",
        "currency_code": "BZD"
    },
    {
        "country": "Benin",
        "currency_code": "XOF"
    },
    {
        "country": "Bermuda",
        "currency_code": "BMD"
    },
    {
        "country": "Bhutan",
        "currency_code": "BTN"
    },
    {
        "country": "Bolivia",
        "currency_code": "BOB"
    },
    {
        "country": "Bosnia and Herzegovina",
        "currency_code": "BAM"
    },
    {
        "country": "Botswana",
        "currency_code": "BWP"
    },
    {
        "country": "Bouvet Island",
        "currency_code": "NOK"
    },
    {
        "country": "Brazil",
        "currency_code": "BRL"
    },
    {
        "country": "British Indian Ocean Territory",
        "currency_code": "USD"
    },
    {
        "country": "Brunei",
        "currency_code": "BND"
    },
    {
        "country": "Bulgaria",
        "currency_code": "BGN"
    },
    {
        "country": "Burkina Faso",
        "currency_code": "XOF"
    },
    {
        "country": "Burundi",
        "currency_code": "BIF"
    },
    {
        "country": "Cambodia",
        "currency_code": "KHR"
    },
    {
        "country": "Cameroon",
        "currency_code": "XAF"
    },
    {
        "country": "Canada",
        "currency_code": "CAD"
    },
    {
        "country": "Cape Verde",
        "currency_code": "CVE"
    },
    {
        "country": "Cayman Islands",
        "currency_code": "KYD"
    },
    {
        "country": "Central African Republic",
        "currency_code": "XAF"
    },
    {
        "country": "Chad",
        "currency_code": "XAF"
    },
    {
        "country": "Chile",
        "currency_code": "CLP"
    },
    {
        "country": "China",
        "currency_code": "CNY"
    },
    {
        "country": "Christmas Island",
        "currency_code": "AUD"
    },
    {
        "country": "Cocos (Keeling) Islands",
        "currency_code": "AUD"
    },
    {
        "country": "Colombia",
        "currency_code": "COP"
    },
    {
        "country": "Comoros",
        "currency_code": "KMF"
    },
    {
        "country": "Congo",
        "currency_code": "XAF"
    },
    {
        "country": "Cook Islands",
        "currency_code": "NZD"
    },
    {
        "country": "Costa Rica",
        "currency_code": "CRC"
    },
    {
        "country": "Croatia",
        "currency_code": "HRK"
    },
    {
        "country": "Cuba",
        "currency_code": "CUP"
    },
    {
        "country": "Cyprus",
        "currency_code": "EUR"
    },
    {
        "country": "Czech Republic",
        "currency_code": "CZK"
    },
    {
        "country": "Denmark",
        "currency_code": "DKK"
    },
    {
        "country": "Djibouti",
        "currency_code": "DJF"
    },
    {
        "country": "Dominica",
        "currency_code": "XCD"
    },
    {
        "country": "Dominican Republic",
        "currency_code": "DOP"
    },
    {
        "country": "East Timor",
        "currency_code": "USD"
    },
    {
        "country": "Ecuador",
        "currency_code": "ECS"
    },
    {
        "country": "Egypt",
        "currency_code": "EGP"
    },
    {
        "country": "El Salvador",
        "currency_code": "SVC"
    },
    {
        "country": "England",
        "currency_code": "GBP"
    },
    {
        "country": "Equatorial Guinea",
        "currency_code": "XAF"
    },
    {
        "country": "Eritrea",
        "currency_code": "ERN"
    },
    {
        "country": "Estonia",
        "currency_code": "EUR"
    },
    {
        "country": "Ethiopia",
        "currency_code": "ETB"
    },
    {
        "country": "Falkland Islands",
        "currency_code": "FKP"
    },
    {
        "country": "Faroe Islands",
        "currency_code": "DKK"
    },
    {
        "country": "Fiji Islands",
        "currency_code": "FJD"
    },
    {
        "country": "Finland",
        "currency_code": "EUR"
    },
    {
        "country": "France",
        "currency_code": "EUR"
    },
    {
        "country": "French Guiana",
        "currency_code": "EUR"
    },
    {
        "country": "French Polynesia",
        "currency_code": "XPF"
    },
    {
        "country": "French Southern territories",
        "currency_code": "EUR"
    },
    {
        "country": "Gabon",
        "currency_code": "XAF"
    },
    {
        "country": "Gambia",
        "currency_code": "GMD"
    },
    {
        "country": "Georgia",
        "currency_code": "GEL"
    },
    {
        "country": "Germany",
        "currency_code": "EUR"
    },
    {
        "country": "Ghana",
        "currency_code": "GHS"
    },
    {
        "country": "Gibraltar",
        "currency_code": "GIP"
    },
    {
        "country": "Greece",
        "currency_code": "EUR"
    },
    {
        "country": "Greenland",
        "currency_code": "DKK"
    },
    {
        "country": "Grenada",
        "currency_code": "XCD"
    },
    {
        "country": "Guadeloupe",
        "currency_code": "EUR"
    },
    {
        "country": "Guam",
        "currency_code": "USD"
    },
    {
        "country": "Guatemala",
        "currency_code": "QTQ"
    },
    {
        "country": "Guinea",
        "currency_code": "GNF"
    },
    {
        "country": "Guinea-Bissau",
        "currency_code": "CFA"
    },
    {
        "country": "Guyana",
        "currency_code": "GYD"
    },
    {
        "country": "Haiti",
        "currency_code": "HTG"
    },
    {
        "country": "Heard Island and McDonald Islands",
        "currency_code": "AUD"
    },
    {
        "country": "Holy See (Vatican City State)",
        "currency_code": "EUR"
    },
    {
        "country": "Honduras",
        "currency_code": "HNL"
    },
    {
        "country": "Hong Kong",
        "currency_code": "HKD"
    },
    {
        "country": "Hungary",
        "currency_code": "HUF"
    },
    {
        "country": "Iceland",
        "currency_code": "ISK"
    },
    {
        "country": "India",
        "currency_code": "INR"
    },
    {
        "country": "Indonesia",
        "currency_code": "IDR"
    },
    {
        "country": "Iran",
        "currency_code": "IRR"
    },
    {
        "country": "Iraq",
        "currency_code": "IQD"
    },
    {
        "country": "Ireland",
        "currency_code": "EUR"
    },
    {
        "country": "Israel",
        "currency_code": "ILS"
    },
    {
        "country": "Italy",
        "currency_code": "EUR"
    },
    {
        "country": "Ivory Coast",
        "currency_code": "XOF"
    },
    {
        "country": "Jamaica",
        "currency_code": "JMD"
    },
    {
        "country": "Japan",
        "currency_code": "JPY"
    },
    {
        "country": "Jordan",
        "currency_code": "JOD"
    },
    {
        "country": "Kazakhstan",
        "currency_code": "KZT"
    },
    {
        "country": "Kenya",
        "currency_code": "KES"
    },
    {
        "country": "Kiribati",
        "currency_code": "AUD"
    },
    {
        "country": "Kuwait",
        "currency_code": "KWD"
    },
    {
        "country": "Kyrgyzstan",
        "currency_code": "KGS"
    },
    {
        "country": "Laos",
        "currency_code": "LAK"
    },
    {
        "country": "Latvia",
        "currency_code": "LVL"
    },
    {
        "country": "Lebanon",
        "currency_code": "LBP"
    },
    {
        "country": "Lesotho",
        "currency_code": "LSL"
    },
    {
        "country": "Liberia",
        "currency_code": "LRD"
    },
    {
        "country": "Libyan Arab Jamahiriya",
        "currency_code": "LYD"
    },
    {
        "country": "Liechtenstein",
        "currency_code": "CHF"
    },
    {
        "country": "Lithuania",
        "currency_code": "LTL"
    },
    {
        "country": "Luxembourg",
        "currency_code": "EUR"
    },
    {
        "country": "Macao",
        "currency_code": "MOP"
    },
    {
        "country": "North Macedonia",
        "currency_code": "MKD"
    },
    {
        "country": "Madagascar",
        "currency_code": "MGF"
    },
    {
        "country": "Malawi",
        "currency_code": "MWK"
    },
    {
        "country": "Malaysia",
        "currency_code": "MYR"
    },
    {
        "country": "Maldives",
        "currency_code": "MVR"
    },
    {
        "country": "Mali",
        "currency_code": "XOF"
    },
    {
        "country": "Malta",
        "currency_code": "EUR"
    },
    {
        "country": "Marshall Islands",
        "currency_code": "USD"
    },
    {
        "country": "Martinique",
        "currency_code": "EUR"
    },
    {
        "country": "Mauritania",
        "currency_code": "MRO"
    },
    {
        "country": "Mauritius",
        "currency_code": "MUR"
    },
    {
        "country": "Mayotte",
        "currency_code": "EUR"
    },
    {
        "country": "Mexico",
        "currency_code": "MXN"
    },
    {
        "country": "Micronesia, Federated States of",
        "currency_code": "USD"
    },
    {
        "country": "Moldova",
        "currency_code": "MDL"
    },
    {
        "country": "Monaco",
        "currency_code": "EUR"
    },
    {
        "country": "Mongolia",
        "currency_code": "MNT"
    },
    {
        "country": "Montenegro",
        "currency_code": "EUR"
    },
    {
        "country": "Montserrat",
        "currency_code": "XCD"
    },
    {
        "country": "Morocco",
        "currency_code": "MAD"
    },
    {
        "country": "Mozambique",
        "currency_code": "MZN"
    },
    {
        "country": "Myanmar",
        "currency_code": "MMR"
    },
    {
        "country": "Namibia",
        "currency_code": "NAD"
    },
    {
        "country": "Nauru",
        "currency_code": "AUD"
    },
    {
        "country": "Nepal",
        "currency_code": "NPR"
    },
    {
        "country": "Netherlands",
        "currency_code": "EUR"
    },
    {
        "country": "Netherlands Antilles",
        "currency_code": "ANG"
    },
    {
        "country": "New Caledonia",
        "currency_code": "XPF"
    },
    {
        "country": "New Zealand",
        "currency_code": "NZD"
    },
    {
        "country": "Nicaragua",
        "currency_code": "NIO"
    },
    {
        "country": "Niger",
        "currency_code": "XOF"
    },
    {
        "country": "Nigeria",
        "currency_code": "NGN"
    },
    {
        "country": "Niue",
        "currency_code": "NZD"
    },
    {
        "country": "Norfolk Island",
        "currency_code": "AUD"
    },
    {
        "country": "North Korea",
        "currency_code": "KPW"
    },
    {
        "country": "Northern Ireland",
        "currency_code": "GBP"
    },
    {
        "country": "Northern Mariana Islands",
        "currency_code": "USD"
    },
    {
        "country": "Norway",
        "currency_code": "NOK"
    },
    {
        "country": "Oman",
        "currency_code": "OMR"
    },
    {
        "country": "Pakistan",
        "currency_code": "PKR"
    },
    {
        "country": "Palau",
        "currency_code": "USD"
    },
    {
        "country": "Palestine",
        "currency_code": null
    },
    {
        "country": "Panama",
        "currency_code": "PAB"
    },
    {
        "country": "Papua New Guinea",
        "currency_code": "PGK"
    },
    {
        "country": "Paraguay",
        "currency_code": "PYG"
    },
    {
        "country": "Peru",
        "currency_code": "PEN"
    },
    {
        "country": "Philippines",
        "currency_code": "PHP"
    },
    {
        "country": "Pitcairn",
        "currency_code": "NZD"
    },
    {
        "country": "Poland",
        "currency_code": "PLN"
    },
    {
        "country": "Portugal",
        "currency_code": "EUR"
    },
    {
        "country": "Puerto Rico",
        "currency_code": "USD"
    },
    {
        "country": "Qatar",
        "currency_code": "QAR"
    },
    {
        "country": "Reunion",
        "currency_code": "EUR"
    },
    {
        "country": "Romania",
        "currency_code": "RON"
    },
    {
        "country": "Russian Federation",
        "currency_code": "RUB"
    },
    {
        "country": "Rwanda",
        "currency_code": "RWF"
    },
    {
        "country": "Saint Helena",
        "currency_code": "SHP"
    },
    {
        "country": "Saint Kitts and Nevis",
        "currency_code": "XCD"
    },
    {
        "country": "Saint Lucia",
        "currency_code": "XCD"
    },
    {
        "country": "Saint Pierre and Miquelon",
        "currency_code": "EUR"
    },
    {
        "country": "Saint Vincent and the Grenadines",
        "currency_code": "XCD"
    },
    {
        "country": "Samoa",
        "currency_code": "WST"
    },
    {
        "country": "San Marino",
        "currency_code": "EUR"
    },
    {
        "country": "Sao Tome and Principe",
        "currency_code": "STD"
    },
    {
        "country": "Saudi Arabia",
        "currency_code": "SAR"
    },
    {
        "country": "Scotland",
        "currency_code": "GBP"
    },
    {
        "country": "Senegal",
        "currency_code": "XOF"
    },
    {
        "country": "Serbia",
        "currency_code": "RSD"
    },
    {
        "country": "Seychelles",
        "currency_code": "SCR"
    },
    {
        "country": "Sierra Leone",
        "currency_code": "SLL"
    },
    {
        "country": "Singapore",
        "currency_code": "SGD"
    },
    {
        "country": "Slovakia",
        "currency_code": "EUR"
    },
    {
        "country": "Slovenia",
        "currency_code": "EUR"
    },
    {
        "country": "Solomon Islands",
        "currency_code": "SBD"
    },
    {
        "country": "Somalia",
        "currency_code": "SOS"
    },
    {
        "country": "South Africa",
        "currency_code": "ZAR"
    },
    {
        "country": "South Georgia and the South Sandwich Islands",
        "currency_code": "GBP"
    },
    {
        "country": "South Korea",
        "currency_code": "KRW"
    },
    {
        "country": "South Sudan",
        "currency_code": "SSP"
    },
    {
        "country": "Spain",
        "currency_code": "EUR"
    },
    {
        "country": "Sri Lanka",
        "currency_code": "LKR"
    },
    {
        "country": "Sudan",
        "currency_code": "SDG"
    },
    {
        "country": "Suriname",
        "currency_code": "SRD"
    },
    {
        "country": "Svalbard and Jan Mayen",
        "currency_code": "NOK"
    },
    {
        "country": "Swaziland",
        "currency_code": "SZL"
    },
    {
        "country": "Sweden",
        "currency_code": "SEK"
    },
    {
        "country": "Switzerland",
        "currency_code": "CHF"
    },
    {
        "country": "Syria",
        "currency_code": "SYP"
    },
    {
        "country": "Tajikistan",
        "currency_code": "TJS"
    },
    {
        "country": "Tanzania",
        "currency_code": "TZS"
    },
    {
        "country": "Thailand",
        "currency_code": "THB"
    },
    {
        "country": "The Democratic Republic of Congo",
        "currency_code": "CDF"
    },
    {
        "country": "Togo",
        "currency_code": "XOF"
    },
    {
        "country": "Tokelau",
        "currency_code": "NZD"
    },
    {
        "country": "Tonga",
        "currency_code": "TOP"
    },
    {
        "country": "Trinidad and Tobago",
        "currency_code": "TTD"
    },
    {
        "country": "Tunisia",
        "currency_code": "TND"
    },
    {
        "country": "Turkey",
        "currency_code": "TRY"
    },
    {
        "country": "Turkmenistan",
        "currency_code": "TMT"
    },
    {
        "country": "Turks and Caicos Islands",
        "currency_code": "USD"
    },
    {
        "country": "Tuvalu",
        "currency_code": "AUD"
    },
    {
        "country": "Uganda",
        "currency_code": "UGX"
    },
    {
        "country": "Ukraine",
        "currency_code": "UAH"
    },
    {
        "country": "United Arab Emirates",
        "currency_code": "AED"
    },
    {
        "country": "United Kingdom",
        "currency_code": "GBP"
    },
    {
        "country": "United States",
        "currency_code": "USD"
    },
    {
        "country": "United States Minor Outlying Islands",
        "currency_code": "USD"
    },
    {
        "country": "Uruguay",
        "currency_code": "UYU"
    },
    {
        "country": "Uzbekistan",
        "currency_code": "UZS"
    },
    {
        "country": "Vanuatu",
        "currency_code": "VUV"
    },
    {
        "country": "Venezuela",
        "currency_code": "VEF"
    },
    {
        "country": "Vietnam",
        "currency_code": "VND"
    },
    {
        "country": "Virgin Islands, British",
        "currency_code": "USD"
    },
    {
        "country": "Virgin Islands, U.S.",
        "currency_code": "USD"
    },
    {
        "country": "Wales",
        "currency_code": "GBP"
    },
    {
        "country": "Wallis and Futuna",
        "currency_code": "XPF"
    },
    {
        "country": "Western Sahara",
        "currency_code": "MAD"
    },
    {
        "country": "Yemen",
        "currency_code": "YER"
    },
    {
        "country": "Zambia",
        "currency_code": "ZMW"
    },
    {
        "country": "Zimbabwe",
        "currency_code": "ZWD"
    }
]



function toggleCountryCurrency() {
    let country = document.getElementById("country");
    let exchangeHomeToTravel = <?= json_encode(number_format($_SESSION['currencyRateHome'], 2), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK); ?>;
    let exchangeTravelToHome = <?= json_encode(number_format($_SESSION['currencyRateTravel'], 2), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK); ?>;
    let transactionType = document.getElementById('type');
    let transactionValue = document.getElementById('value');
    calculateExchangeRate();

    if (wallet.value == 'travel') {     //show the fields
        country.style.display = 'block';
        currency.style.display = 'block';

    } else if (wallet.value == 'home' && transactionType.value == 'transfer') {
        country.style.display = 'block';
        currency.style.display = 'block'; 

    } else {     //hide the fields
        currency.style.display = 'none'
        
    }

}
     

// Toggle exchange rate when transfer is select

function transferCategory() {
    let transactionType = document.getElementById('type').value;
    let transactionCategory = document.getElementById('category');
    let transferCategory = document.getElementById('transferCat');
    let exchangeInput = document.getElementById('exchange');
    let selectedWallet = '';
    let currency = document.getElementById('currency');
    let repeatDiv = document.getElementById('repeat-div'); 


        if(transactionType == 'transfer') {

            transactionCategory.disabled = true;
            transferCategory.style.display = 'block';
            transactionCategory.value = 'transfer';
            exchangeInput.style.display = 'block';
            currency.style.display = 'block';
            convertedDiv.style.display = 'block';
            repeatDiv.style.display = 'none';

        } else if (wallet.value == 'home' && transactionType.value == 'transfer') {

            transactionCategory.disabled = true;
            transferCategory.style.display = 'block';
            transactionCategory.value = 'transfer';
            exchangeInput.style.display = 'block';
            wallet.value = 'home';
            currency.style.display = 'block';
            convertedDiv.style.display = 'block';
            repeatDiv.style.display = 'none';

        } else {

            transactionCategory.disabled = false;
            transferCategory.style.display = 'none';
            transactionCategory.value = '<?= strtolower($transactionCategory) ?>';
            exchangeInput.style.display = 'none';
            convertedDiv.style.display = 'none';
            repeatDiv.style.display = 'block';
        }
        
        toggleCountryCurrency()
}

function transferCategoryOnLoad() {
    let transactionType = document.getElementById('type').value;
    let transactionCategory = document.getElementById('category');
    let transferCategory = document.getElementById('transferCat');
    let exchangeInput = document.getElementById('exchange');
    let selectedWallet = '';
    let currency = document.getElementById('currency');



        if(transactionType == 'transfer') {
            transactionCategory.disabled = true;
            transferCategory.style.display = 'block';
            transactionCategory.value = 'transfer';
            exchangeInput.style.display = 'block';

            currency.style.display = 'block';
            convertedDiv.style.display = 'block';
        } else if (wallet.value == 'home' && transactionType.value == 'transfer') {
            transactionCategory.disabled = true;
            transferCategory.style.display = 'block';
            transactionCategory.value = 'transfer';
            exchangeInput.style.display = 'block';
            currency.style.display = 'block';
            convertedDiv.style.display = 'block';
        } else {
            transactionCategory.disabled = false;
            transferCategory.style.display = 'none';
            transactionCategory.value = '<?= strtolower($transactionCategory) ?>';
            exchangeInput.style.display = 'none';
            convertedDiv.style.display = 'none';
        }
}

function getCountryCurrency() {
    console.log(wallet.value);
    
    if(wallet.value == 'travel') {
        let CountryInput = document.getElementById("country-field");

        let currencyBySelectedCountry = countryList.filter(country => country.country.toLowerCase() == CountryInput.value)[0]['currency_code'];
        let selectCurrency = document.getElementById(currencyBySelectedCountry);
        selectCurrency.selected = true;
        return;
    }

    let selectCurrency = document.getElementById(defaultCurrency);
    selectCurrency.selected = true;

}

calculateExchangeRate();
updateExchangeLabel();
transferCategoryOnLoad();
toggleCountryCurrency();
getCountryCurrency();



</script>