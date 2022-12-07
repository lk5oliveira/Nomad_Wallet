<?php

session_start();
include('include/login/verify_login.inc.php');
include('include/total.php');
include('include/generate_account_list.php');
backToIndex();

?>
<html lang="en">
    <style>
        .content-account {
            margin-top: 100px;
            padding: 20px;
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: flex-start;
            width: 100%;
            gap: 1rem;
        }

        .account-div {
            display: flex;
            flex-direction: column;
            flex-basis: 100%;
        }

        .account-name {
            font-weight: 700;
            font-size: 1.75rem;
            padding: 5px;
            z-index: 3;
            width: fit-content;
        }

        .account-details {
            padding: 1rem;
            border-radius: 20px;
            width: 100%;
            background: linear-gradient(145deg, #d7d7d7, #ffffff);
            box-shadow: 20px 20px 60px #cbcbcb, -20px -20px 60px #ffffff;
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            gap: 10px;
            position: relative;
            z-index: 2;
        }

        .fa-circle {
            color: #85bb65;
            font-size: 1rem;
        }

        .account-details:before {
            content: ' ';
            display: block;
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0.2;
            border-radius: 20px;
            background-repeat: no-repeat;
            background-size: cover;
            background-position: 0% 50% ;
        }

        a.account-name, a.country-list {
            text-decoration: none !important;
            color: inherit !important;
            transition-duration: 0.4s;
            border-radius: 10px;
        }

        a.account-name:hover , a.country-list:hover, a.used-country:hover {
            text-decoration: none !important;
            cursor: pointer;
            background-color: whitesmoke;
        }

        .img-1:before {
            background-image: url('img/account/digital_nomad_1.webp');
        }

        .img-2:before {
            background-image: url('img/account/digital_nomad_2.webp');
        }

        .img-3:before {
            background-image: url('img/account/digital_nomad_3.webp');
        }

        .img-4:before {
            background-image: url('img/account/digital_nomad_4.webp');
        }

        .img-5:before {
            background-image: url('img/account/digital_nomad_5.webp');
        }

        .img-7:before {
            background-image: url('img/account/digital_nomad_7.webp');
        }

        .img-8:before {
            background-image: url('img/account/digital_nomad_8.webp');
        }

        .img-9:before {
            background-image: url('img/account/digital_nomad_9.webp');
        }
        .img-10:before {
            background-image: url('img/account/digital_nomad_10.webp');
        }

        .img-11:before {
            background-image: url('img/account/digital_nomad_11.webp');
        }

        .img-12:before {
            background-image: url('img/account/digital_nomad_12.webp');
        }

        .img-13:before {
            background-image: url('img/account/digital_nomad_13.webp');
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

        small {
            font-size: 50% !important;
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
                margin: 0px 0px 0px 0px;
                padding-left: 80px;
                overflow: scroll;
            }

            .account-div {
                flex-basis: 30%;
            }
        }

        /* RESPONSIVE PC AND BIG SCREENS */
        @media screen and (min-width: 1301px) {
            .content-account {
                margin: 0px;
                overflow: scroll;
            }

            .account-div {
                flex-basis: 30%;
            }
        }

    </style>
<head>
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
            <?php generateAccounts(); ?>
        </div>
        
    </div>

</body>
</html>