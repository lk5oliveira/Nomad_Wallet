<?php
    /* GENERATES DE DATA FOR THE BAR CHART (MONTH BY MONTH RESULT) AT THE DASHBOARD.
    This code is capturing the transactions from the data base (income and expenses)
    and calculating the diference.
    Later this is included at the panel.php (dashboard page) and use enconde with JSON
    a printed as an JS array data.
    */

   //Create a variable for the session user ID.
    $sessionUser = $_SESSION['email'];
    $userId = mysqli_fetch_array(mysqli_query($connection, "SELECT * FROM users WHERE usersEmail = '$sessionUser';"));
    $userMainCurrency = $currencyFilter;
    $userIdResult = $userId['usersID']; //USER ID
    $resultArray = [];
    $chartDataArray = [];

    $currentMonth = date('m');
    $currentYear = date('Y');
    $initialValue = floatval($_SESSION['initialValue']);
    
    
    global $userIdResult, $connection, $date, $currentYear, $currentDate;

    // MONTH BY MONTH CHART
    for ($month = 1; $month < 13;$month++) { //create a loop for each month
        if ($month > 9) {
        $date = $currentYear . $month;
        } else {
        $date = $currentYear . 0 . $month; // add a 0 before the number as the search happens as 01, 02...
        }

        // gets the income
        $query_income = "SELECT EXTRACT(YEAR_MONTH FROM transactions_date) AS date, SUM(transactions_value) AS total
        FROM transactions
            WHERE user_id = '$userIdResult' AND transactions_value > 0 AND transactions_currency = '$userMainCurrency'
            GROUP BY date HAVING date = '$date';";
        $mysqlExec_income = mysqli_query($connection, $query_income);
        $mysqlResult_income = mysqli_fetch_array($mysqlExec_income);
        if (isset($mysqlResult_income['total'])) {
            $total_income = $mysqlResult_income['total'];
        } else {
            $total_income = $mysqlResult_income['total'] = 0;
        }

        // get the expense
        $query_expense = "SELECT EXTRACT(YEAR_MONTH FROM transactions_date) AS date, SUM(transactions_value) AS total
        FROM transactions
            WHERE user_id = '$userIdResult' AND transactions_value < 0 AND transactions_currency = '$userMainCurrency'
            GROUP BY date HAVING date = '$date';";
        $mysqlExec_expense = mysqli_query($connection, $query_expense);
        $mysqlResult_expense = mysqli_fetch_array($mysqlExec_expense);
        if (isset($mysqlResult_expense['total'])) {
            $total_expense = $mysqlResult_expense['total'];
        } else {
            $total_expense = $mysqlResult_expense['total'] = 0;
        }

        // calculates the difference to have the month result value. (income - expense)
        $dif = $total_income + $total_expense;

        // create an array following the chart rules
        array_push($resultArray, round($dif));
    }


    // ------------- YEAR OVERVIEW CHART -----------------

    // get the result for each month where is not null.
    $query_get_month_income = "SELECT YEAR(transactions_date) AS year, MONTH(transactions_date) as month, 
	SUM(transactions_value) AS total,
    EXTRACT( YEAR_MONTH FROM transactions_date) AS period
        FROM transactions
            WHERE user_id = '$userIdResult' AND transactions_currency = '$userMainCurrency'
            GROUP BY period;";
    $mysqlExec = mysqli_query($connection, $query_get_month_income);
    
    //print_r(mysqli_fetch_all($mysqlExec));

    // Create an array separating year and month.  
    while($mysqlResultIncome = mysqli_fetch_array($mysqlExec)) { 

        $year = strval($mysqlResultIncome['year']);
        $month = $mysqlResultIncome['month'];
        $total = $mysqlResultIncome['total'];

        $arrayOverView[$year][$month] = $total;
    }

    // Discover the first transaction date and the last transaction date.
    $query_get_first_last_date = "SELECT MIN(transactions_date),MAX(transactions_date) FROM transactions WHERE user_id = '$userIdResult';";
    $mysqlExecDates = mysqli_query($connection, $query_get_first_last_date);
    $mysqlResultDates = mysqli_fetch_row($mysqlExecDates); // Array element. key [0] = first date; key [1] = last date.
    $firstYear = substr($mysqlResultDates[0], 0, 4); // get only the year from the date
    $lastYear = substr($mysqlResultDates[1], 0, 4); // get only the year from the date

    $arrayToEnconde = []; // declaring the final array variable to be json enconded
    $currentTotal = floatval($_SESSION['initialValue']); // declaring the total value to calculate the acumulated total

    // creates an array to display at the chart
   for ($year = $firstYear; $year <= $lastYear; $year++) {
        
        $arrayToEnconde[$year] = [];

        for ($month = 1; $month < 13; $month++) {

            if(empty($arrayOverView[$year][$month])) {
                
                $currentTotal += 0;
                array_push($arrayToEnconde[$year], round($currentTotal, 2));


            } else {

                $currentTotal += $arrayOverView[$year][$month];
                array_push($arrayToEnconde[$year], round($currentTotal, 2));

            }
        }
    }
    
    $previousYear = $currentYear - 1;
    $currentMonth = ltrim($currentMonth, "0") -1;
    $previousMonth = $currentMonth - 1;

    if(empty($arrayToEnconde[$previousYear])) {
        $previousYearResult = 0;
    } else {
        $previousYearResult = $arrayToEnconde[$previousYear][11];
    }

    if(isset($arrayToEnconde[$currentYear][$currentMonth])){
        $currentResult = $arrayToEnconde[$currentYear][$currentMonth];
        $yearDiff = $currentResult + $previousYearResult;
    }

    

   //var_dump($arrayToEnconde);
?>