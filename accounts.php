<html lang="en">
    <style>
        .content-account {
            margin-top: 100px;
            padding: 20px;
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            width: 100%;
        }

        .account-div {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .account-name {
            font-weight: 700;
            font-size: 1.5rem;
            padding: 5px;
            z-index: 999;
        }

        .account-details {
            padding: 1rem;
            border-radius: 20px;
            background: linear-gradient(145deg, #d7d7d7, #ffffff);
            box-shadow: 20px 20px 60px #cbcbcb, -20px -20px 60px #ffffff;
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            gap: 10px;
        }

        .row-details {
            flex-basis: 100%;
        }

        .account-balance {
            text-align: end;
            padding: 5px;
            border-radius: 10px;
            background-color: #183d55;
            color: white;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .country-titles {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .account-countries {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            gap: 10px;
            border-radius: 5px;
        }

        .used-country {
            background-color: #cbcbcb;
            color: #343434;
            padding: 10px;
            border-radius: 5px;
        }

        /* RESPONSIVE TABLET AND SMALL LAPTOP SCREENS */

        @media screen and (min-width: 481px) and (max-width: 1300px) {
            .content-account {
                margin: 0px 0px 0px 60px;
            }

            .account-div {
                flex-basis: 30%;
            }
        }

        /* RESPONSIVE PC AND BIG SCREENS */
        @media screen and (min-width: 1301px) {
            .content-account {
                margin: 0px;
            }
        }

    </style>
<head>
    <?php

        session_start();
        include('include/login/verify_login.inc.php');
        include('include/total.php');
        include('include/generate_account_list.php');
        backToIndex();

    ?>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.sandbox.google.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="css/accounts.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts" defer></script>
    <script src="https://kit.fontawesome.com/a440aae6fe.js" defer crossorigin="anonymous"></script>
    <script src="include/JS/menu.js" defer></script>
    <script src="include/JS/transactions-button.js" defer></script>
    <title>Wallets</title>
</head>
<body onresize="resize();">


    <div id="main-container">

    <?php include('menu.php'); ?>


        <div class="page-title">
            <h2 class="title-text">Accounts</h2>
        </div>



        <div class="content-account" id="content-account">
            <div class="account-div">
                <span class="account-name">{currencyCode}</span>
                <div class="account-details">
                    <div class="account-balance row-details">
                        <span><i class="fa-solid fa-money-bill-1-wave"></i> {balance}</span>
                    </div>
                    <span class="country-titles row-details"><i class="fa-solid fa-location-dot"></i> Used in</span>
                    <div class="account-countries row-details">
                        <span class="used-country">{country}</span>
                        <span class="used-country">{country}</span>
                    </div>
                </div>
            </div>






          <!--
            <div class="card-container main-account-div">
                <h3 class="card-title"><?=  $_SESSION['defaultCurrency']; ?></h3>
                <div class="main-account content-div" id="main-account">
                    <div class="location-info">
                        <span class="country">
                        <span class="currency">
                        <i class="fa-solid fa-money-bill-1-wave"></i>
                            <?php htmlspecialchars($total = getTotal('all', 'all', $_SESSION['defaultCurrency']) + floatval($_SESSION['initialValue']));
                            echo htmlspecialchars($_SESSION['defaultSymbol']) . number_format($total, 2); ?>
                        </span>
                    </div>
                    <div class="balance">

                    </div>
                </div>
            </div>
        --->



        </div>
        
    </div>

</body>
</html>