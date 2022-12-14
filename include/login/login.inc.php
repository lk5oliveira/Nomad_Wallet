<?php

//To connect to the DB, check and login to the account.

    session_start();
    
    include_once('../connect.inc.php');
    include('../prepare_data.php');
    include('../gravatar.php');

    if((time() - $_SESSION['last_attempt']) > 300) { // unlock the login system after 5 minutes for users that tried to login more than 3 times without success.
        $_SESSION['attempts'] = 0; // attempts are used to verify how many login attempts the user had. If more than 5 minutes since the last failed attempt; attempts are reset to zero to let the user try again;
    }
    
    if($_SESSION['attempts'] < 3) { // if failed attempts are less than 3, then try to login.

        if(empty($_POST['user']) || empty($_POST['password'])) { // if any of the fields are empty, send back to the login page.

            header('location: index.php');
            exit();
        }

        $usersEmail = prepareData($_POST['user']);
        $userPassword = mysqli_real_escape_string($connection, $_POST['password']);

        $_SESSION['return_user'] = $usersEmail; // return the user value to the input value when login failed.

        $stmt = $connection->prepare("SELECT * FROM users WHERE usersEmail = ? AND usersPwd = ?;");
        $stmt->bind_param("ss", $usersEmail, $userPassword);
        $stmt->execute();
        $stmtResult = $stmt->get_result();

        $array = $stmtResult->fetch_array(); // Get the data from the userID

        $row = $stmtResult->num_rows; // Check if there's existing users with this userID

        if($row === 1) { // login success

            session_regenerate_id(true);
            unset($_SESSION['return_user']); // unset the session to return the user to input field when the login fail.

            include_once('../world-currency.php');

            $_SESSION['user'] = $array['usersName'];
            $_SESSION['userID'] = $array['usersID'];
            $_SESSION['email'] = $array['usersEmail'];
            $_SESSION['gravatar'] = get_gravatar($_SESSION['email']);
            $_SESSION['defaultCountry'] = $array['usersDefaultCountry'];
            $_SESSION['defaultCurrency'] = $array['usersDefaultCurrency'];
            $_SESSION['defaultSymbol'] = $currency_list[$_SESSION['defaultCurrency']]['symbol']; // get the currency symbol from a variable available in the world-currency.php
            $_SESSION['initialValue'] = $array['usersInitialValue'];
            $_SESSION['register'] = 'not registering';

            include_once('../location-api.php');
            
            $_SESSION['country'] = $country;
            $_SESSION['currencyCode'] = $currencyCode;
            $_SESSION['currencySymbol'] = $currencySymbol;
            $_SESSION['exchangeRates'] = GetCurrencyRate($_SESSION['defaultCurrency']);


                $queryAlterCountry = "UPDATE `users` SET `usersCurrentCountry` = '$country', `usersCurrentCurrency` = '$currencyCode' WHERE `users`.`usersID` = $_SESSION[userID];";
                $execAlterCountry = mysqli_query($connection, $queryAlterCountry);

                $connection->close();
                header('location: ../../panel.php');
                exit();


        } else { // login failed

            $_SESSION['no-auth'] = TRUE; // display an error message
            $_SESSION['attempts'] = $_SESSION['attempts'] + 1; // show how many attempts
            $_SESSION['last_attempt'] = time(); // renovates the time of attempts
            header("location: ../../index.php");
            exit();

        }

    } else { // if user has too many attempts

        $_SESSION['attempts'] = $_SESSION['attempts'] + 1; // keep summing the attempts
        $_SESSION['last_attempt'] = time(); // renovates the attempt time
        header("location: ../../index.php");
        exit();

    }

?>