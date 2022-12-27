<?php
        session_start();
        include('include/currency_api.php');
        include('include/login/verify_login.inc.php');
        backToIndex();
        include('include/connect.inc.php');
        include('include/world-currency.php');
        include('include/prepare_data.php');
        


        $usersEmail = $_SESSION['email'];
        $queryGetUsers = "SELECT * FROM users WHERE usersEmail = ?;";
        $stmt = $connection->prepare($queryGetUsers);
        $stmt->bind_param('s', $usersEmail);
        $stmt->execute();
        $userInfo = $stmt->get_result()->fetch_array();

        $validationError = '';

        function profileValidation() {

            global $connection, $countryArraySize, $countryCurrency, $currency_list, $userInfo;
            $validation = ''; // to check if there any validation error. If any validation error apears, the variable will return the field with the error.

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                $usersID = $_SESSION['userID'];
                $defaultCountry = prepareData($_POST['default-country']);
                $defaultCurrency = prepareData($_POST['default-currency']);
                $currency = prepareData($userInfo['usersCurrentCurrency']);
                $country = prepareData($userInfo['usersCurrentCountry']);
                $initialValue = prepareData($_POST['initial-value']);
                $email = prepareData($_POST['email']);
                $name = prepareData($_POST['name']);

                if(isset($_POST['currency']) && isset($_POST['country'])) {
                    $currency = prepareData($_POST['currency']);
                    $country = prepareData($_POST['country']);
                }
            
                if(empty($name)){
            
                    $validation = 'name can not be empty';
            
            
                } elseif (!preg_match("/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ ]+$/", $name)) {
            
                        $validation = 'name can only contains letters and space';
            
                }
                
                if(empty($email)) {
            
                    $validation = 'email can not be empty';
            
                }elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    
                    $validation = 'please enter a valid email'; 
                        
                }
            
                if(!empty($defaultCurrency) || !empty($currency)) {
            
                    $validationDefaultCurrency = false;
                    $validationTravelCurrency = false;
            
                    foreach(array_keys($currency_list) as $key) {
            
                        if($key == strtoupper($defaultCurrency)) {
            
                            $validationDefaultCurrency = true;
            
                        } elseif ($key == strtoupper($currency)) {
            
                            $validationTravelCurrency = true;
            
                        }
                    }
            
                    if($validationDefaultCurrency != true || $validationTravelCurrency != true) {
            
                        $validation = 'Error, please try again';
            
                    }
                }
            
                if(!empty($defaultCountry) || !empty($country)) {
            
                    $validationDefaultCountry = false;
                    $validationTravelCountry = false;
                    //echo 'entrou';
                    for($i = 0;$i <= $countryArraySize;$i++) {
            
                        if(strtoupper($countryCurrency[$i]['country']) == strtoupper($defaultCountry)) {
            
                            $validationDefaultCountry = true;
                            //echo 'true def';
            
                        }
            
                        if (strtoupper($countryCurrency[$i]['country']) == strtoupper($country)) {
            
                            $validationTravelCountry = true;
                            //echo 'true';
            
                        }
                    }
            
                    if($validationDefaultCountry != true || $validationTravelCountry != true) {
            
                        $validation = 'Error, please try again';
            
                    }
                }
            
                $regex= "/[A-Za-z]/";
            
                if(preg_match_all($regex, $initialValue) > 0) {
            
                    $validation = 'Error, please try again';
            
                }
            
                if($validation == '') { // if $validation is empty then it means there's no errors.


                    $query = "UPDATE users SET `usersName` = ?, `usersEmail` = ?, `usersDefaultCountry` = ?, `usersDefaultCurrency` = ?,
                    `usersInitialValue` = ?, `usersCurrentCountry` = ?, `usersCurrentCurrency` = ? WHERE `users`.`usersID` = ?";

                    $stmt = $connection->prepare($query);
                    $stmt->bind_param("ssssssss", $name, $email, $defaultCountry, $defaultCurrency, $initialValue, $country, $currency, $usersID);

                    
                    if($stmt->execute()) {

                        $_SESSION['user'] = $name;
                        $_SESSION['email'] = $email;
                        $_SESSION['defaultCurrency'] = $defaultCurrency;
                        $_SESSION['defaultSymbol'] = $currency_list[$_SESSION['defaultCurrency']]['symbol'];
                        $_SESSION['currencyCode'] = $currency;
                        $_SESSION['currencySymbol'] = $currency_list[$_SESSION['currencyCode']]['symbol'];
                        $_SESSION['country'] = $country;
                        $_SESSION['initialValue'] = $initialValue;
                        $_SESSION['exchangeRates'] = GetCurrencyRate($_SESSION['defaultCurrency']);

                        $connection->close();

                        header('location:' . $_SERVER['HTTP_REFERER']);

                    } else {

                        echo 'something is wrong here.';
            
                    }
            
            
                } else {
                    
                    return $validation;
            
                }
            }

            
        }

        $validationError = profileValidation();

    ?>


<style>

    .content {
        display: flex;
        flex-direction: column;
        justify-content: space-around;
        align-items: center;
        gap: 30px;
    }

    .profile-title {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: space-around;
        align-items: center;
        gap: 30px;
    }

    form.update {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        gap: 1.2rem;
        align-content: center;
        justify-content: space-between;
    }

    hr {
        width: 100%;
    }

    .left-div, .middle-div, .right-div {
        width: 25%;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
    }

    .input {
        width: 100% !important;
    }

    .button-container {
        display: flex;
        width: 100%;
        justify-content: space-evenly !important;
        margin-top: 10px;
    }

    #travel-off > * {
        opacity: 0.6;
    }

    .button-container button {
        width: 30%;
        border-radius: 30px;
        font-weight: 700;
        color: #06113C;
        border: 2px solid #06113C;
        transition: 0.2s;
        height: 3em;
        border: none;
        color: white;
    }

    .button-container button:hover, form button:focus {
        opacity: 1.2;
        color: white;
        border: none;
        box-shadow: 5px 5px 15px 5px rgba(0,0,0,0.1);
        cursor: pointer;
    }

    .submit {
        background-color: #98abcd;
    }

    .cancel {
        background-color: #f07167;
    }

    @media screen and (max-width: 480px) {
        #main-container {
            height: auto !important;
        }

        .content {
            margin: 0 !important;
            margin-top: 100px !important;
        }

        form.update {
            justify-content: center;
        }

        .left-div, .middle-div, .right-div {
            flex-basis: 45%;
        }

        .profile-title {
            display: none;
        }
    }

    @media screen and (max-width: 1020px) and (min-width: 481px) {
        .content {
            margin-left: 100px !important;
        }
    }



</style>
<!DOCTYPE html>
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
    <script src="https://cdn.jsdelivr.net/npm/apexcharts" defer></script>
    <script src="https://kit.fontawesome.com/a440aae6fe.js" defer crossorigin="anonymous"></script>
    <script src="include/js/menu.js" defer></script>
    <script src="https://plentz.github.io/jquery-maskmoney/javascripts/jquery.maskMoney.min.js" type="text/javascript"></script>
    <title>Wallets</title>
</head>
<body onresize="resize();">
    <div id="main-container">

    <?php include('menu.php'); ?>

        <div class="page-title">
            <h2 class="title-text">Profile</h2>
        </div>

        <div class="content" id="content content-div">
            <div class="profile-title">
                <h2 class="profile-tile-text">Edit profile</h2>
            </div>
            
            <?php if($validationError != '') : ?>
                <div class="alert">
                    <span><?= $validationError ?></span>
                </div>
            <?php endif ?>
                
            <form action="" class="update" id="update" method="post">
                <div class="left-div">
                    <h5>Account</h5>
                    <hr/>
                        <div class="input <?php if(str_contains($validationError, 'name')) : ?>validation-error<?php endif ?>">
                            <label for="name">Name</label>
                            <input name='name' type="text" value="<?=htmlspecialchars($userInfo['usersName'])?>">
                        </div>

                        <div class="input <?php if(str_contains($validationError, 'email')) : ?>validation-error<?php endif ?>">
                            <label for="email">email</label>
                            <input name='email' type="email" value="<?=$userInfo['usersEmail']?>">
                        </div>

                        <div class="input">
                            <label for="password">password</label>
                            <input name='password' type="password" placeholder="********">
                        </div>
                </div>

                <div class="middle-div">
                        <h5>Home</h5>
                    <hr/>
                    <div class="input">
                        <label for="currency">Country</label>
                            <select name="default-country" type="text" id="currency-field" required>
                                <option name='default-country' value="" disabled>select</option>
                                <?php for($i = 0;$i <= $countryArraySize;$i++) {
                                    if (strtolower($countryCurrency[$i]['country']) == strtolower($userInfo['usersDefaultCountry'])) {
                                        echo '<option value="' . ucfirst(strtolower($countryCurrency[$i]['country'])) . '" selected>' . ucfirst(strtolower($countryCurrency[$i]['country'])) . '</option>';
                                    } else {
                                        echo '<option value="' . ucfirst(strtolower($countryCurrency[$i]['country'])) . '">' . ucfirst(strtolower($countryCurrency[$i]['country'])) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="input">
                        <label for="default-currency">Currency</label>
                            <select name="default-currency" type="text" id="currency-field" required>
                                <option value="" disabled>select</option>
                                <?php foreach(array_keys($currency_list) as $key) {
                                    if (strtolower($key) == strtolower($userInfo['usersDefaultCurrency'])) {
                                        echo '<option value="' . strtoupper($key) . '" selected>' . strtoupper($key) . '</option>';
                                    } else {
                                        echo '<option value="' . strtoupper($key) . '">' . strtoupper($key) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="input">
                            <label for="initial-value">Initial value</label>
                            <input type="text" data-js="money" value="<?= number_format($userInfo['usersInitialValue'], 2, ',', '.');?>" name="initial-value" id="initial-value" class="login-input">
                        </div>
                </div>

                <div class="right-div">
                <h5>Travel</h5>
                    <hr/>
                        <div class="input">
                            <label for="country">Country</label>
                            <select name="country" type="text" id="country-field" required>
                                <option value="" disabled>select</option>
                                <?php for($i = 0;$i <= $countryArraySize;$i++) {
                                    if (strtolower($countryCurrency[$i]['country']) == strtolower($userInfo['usersCurrentCountry'])) {
                                        echo '<option value="' . ucfirst(strtolower($countryCurrency[$i]['country'])) . '" selected>' . ucfirst(strtolower($countryCurrency[$i]['country'])) . '</option>';
                                    } else {
                                        echo '<option value="' . ucfirst(strtolower($countryCurrency[$i]['country'])) . '">' . ucfirst(strtolower($countryCurrency[$i]['country'])) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="input">
                        <label for="default-currency">Currency</label>
                            <select name="currency" type="text" id="currency-field" required>
                                <option value="" disabled>select</option>
                                <?php  foreach(array_keys($currency_list) as $key) {
                                    if (strtolower($key) == strtolower($userInfo['usersCurrentCurrency'])) {
                                        echo '<option value="' . strtoupper($key) . '" selected>' . strtoupper($key) . '</option>';
                                    } else {
                                        echo '<option value="' . strtoupper($key) . '">' . strtoupper($key) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        </form>
                </div>
                <div class="button-container">
                    <button class="cancel" onclick="history.go(-1); return false;">cancel</button>
                    <button class="submit" type="submit" name="update" form="update">save</button>
                </div>
            </form>
            
        </div>
    </div>
    <script>
        jQuery(function() {
            
            jQuery("#initial-value").maskMoney({ 
            thousands:'.', 
            decimal:','
            })

        });

    </script>
    <script src="include/JS/transactionsForm.js"></script>
</body>
</html>