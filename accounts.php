<html lang="en">
<head>
    <?php

        session_start();
        include('include/login/verify_login.inc.php');
        include('include/total.php');
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

    <?php include('transaction-buttons.php');?>


        <div class="page-title">
            <h2 class="title-text">Accounts</h2>
        </div>



        <div class="content" id="content">
            
            <div class="card-container main-account-div">
                <h3 class="card-title">Home wallet</h3>
                <div class="main-account content-div" id="main-account">
                    <div class="location-info">
                        <span class="country">
                        <i class="fa-solid fa-location-dot"></i>
                            Main wallet
                        </span>
                        <span class="currency">
                        <i class="fa-solid fa-money-bill-1-wave"></i>
                            <?=  $_SESSION['defaultCurrency']; ?>
                        </span>
                    </div>
                    <div class="balance">
                        <span class="balance-text">BALANCE</span>
                        <span class="travel-value">
                            <?php htmlspecialchars($total = getTotal('all', 'all', $_SESSION['defaultCurrency']) + floatval($_SESSION['initialValue']));
                            echo htmlspecialchars($_SESSION['defaultSymbol']) . number_format($total, 2);?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="card-container travelling-account-div">
                <div class="title-container">
                    <h3 class="card-title">Travel wallet</h3>

                </div>
                <div class="travelling-account content-div" id="travelling-account">
                    <div class="location-info">
                        <span class="country">
                        <i class="fa-solid fa-location-dot"></i>
                            <?= htmlspecialchars($_SESSION['country']); ?>
                        </span>
                        <span class="currency">
                        <i class="fa-solid fa-money-bill-1-wave"></i>
                            <?= htmlspecialchars($_SESSION['currencyCode']); ?>
                        </span>
                    </div>
                    <div class="balance">
                        <span class="balance-text">BALANCE</span>
                        <span class="travel-value"><?php $total = number_format(getTotal('all', 'all', $_SESSION['currencyCode']), 2); echo htmlspecialchars($_SESSION['currencySymbol']) . $total;?></span>
                    </div>
                </div>
            </div>

            <div id="add-transaction">
                <div class="transfer-buttons-container" id="income" onclick="displayForm('income');toggleCountryCurrency();">
                    <i class="fa-solid fa-plus income-icon icon"></i>
                    <span class="income-text">Add income</span>
                </div>
                <div class="transfer-buttons-container" id="Expense" onclick="displayForm('expense');toggleCountryCurrency();">
                    <i class="fa-solid fa-minus expense-icon icon"></i>    
                    <span class="expense-text">Add expense</span>
                </div>
                <div class="transfer-buttons-container" id="transfer" onclick="displayForm('transfer');toggleCountryCurrency();">
                    <i class="fa-solid fa-arrow-right-arrow-left transfer-icon icon"></i>
                    <span class="transfer-text">Transfer</span>
                </div>
            </div>


        </div>
        
    </div>

    <script>
        const cb = document.getElementById("checkbox");
        const travellingAccountDiv = document.getElementById('travelling-account')

        function travelCardCheckBox () {

            if (cb.checked === true) {
                travellingAccountDiv.style.filter = "none";
                travellingAccountDiv.style.opacity = "1";
            } else {
                travellingAccountDiv.style.filter = "grayscale(1)";
                travellingAccountDiv.style.opacity = "0.3";
            }
        };

        travelCardCheckBox ();
        
    </script>

</body>
</html>