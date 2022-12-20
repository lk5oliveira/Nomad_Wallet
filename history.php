<?php
    session_start();
    include('include/login/verify_login.inc.php');
    include('include/total.php');
    include('include/generate_account_list.php');
    include('include/world-currency.php');

    $currenciesArray = json_encode($currency_list);

    $currencyList = getCurrencyList();

    backToIndex();

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
    <link rel="stylesheet" href="css/history.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://kit.fontawesome.com/a440aae6fe.js" crossorigin="anonymous"></script>
    <script src="include/JS/menu.js" defer></script>
    <script src="include/JS/transactions-button.js" defer></script>
    <title>Transactions</title>
</head>
<body>
    


    <div id="main-container">

        <?php include('menu.php'); ?>

        <?php include('include/transaction-form.php'); ?>

        <?php include('areusure.php'); ?>

        <div class="page-title">
            <h2 class="title-text">History</h2>
        </div>

        <div class="content" id="content">

            <div class="period-result content-div" id="period-result">
                <h2 class='title' id="result-text-static">RESULT</h2>
                <p class='title-period' id="title-period"></p>
                <span>Selected Period</span>
                <h2 class='result' id="result"></h2>
                <span>Current balance</span>
                <h2 class='current-result' id="current-result"></h2>
            </div>

            <div class="chart-income-expense content-div" id="chart-income-expense">
                
            </div>

            <div class="chart-categories content-div" id="chart-categories">
                
            </div>

            <?php include ('include/button-transactions.php'); ?>


            <div class="filter-div" id="filter-div">
                <div class="filters">

                    <select type="text" id="typeFilter" onChange="applyFilter(); updateChart();">
                        <option value="all" disabled selected>Type</option>
                        <option value="all">All</option>
                        <option value="transfer">transfer</option>
                        <option value="income">income</option>
                        <option value="expense">expense</option>
                    </select>
                    
                    <select name="categoriesFilter" id="categoriesFilter" class="categoriesFilter" onChange="applyFilter(); updateChart();">
                            <option value="all" disabled="" disabled selected>Category</option>
                            <option value="all">All</option>
                            <option value="Car">Car</option>
                            <option value="Flight">Flight</option>
                            <option value="Food">Food</option>
                            <option value="Health">Health</option>
                            <option value="Investments ">Investments</option>
                            <option value="Others">Others</option>
                            <option value="Projects">Projects</option>
                            <option value="Rent">Rent</option>
                            <option value="Restaurant">Restaurant</option>
                            <option value="Salary">Salary</option>
                            <option value="Shopping">Shopping</option>
                            <option value="Transport">Transport</option> 
                    </select>

                    <select name="currencyFilter" id="currencyFilter" class="filter-currency" onChange="applyFilter(); updateChart();">
                    <option value="all" disabled selected>Currency</option>
                    <option value="all">All</option>
                    <?php
                        foreach($currencyList as $key => $currency) {
                            if(!isset($_GET['currency'])) {
                                if(strtolower($currency[0]) == strtolower($_SESSION['currencyCode'])) {
                                    echo
                                    '<option value="' . strtolower($currency[0]) . '" selected>' . strtoupper($currency[0]) . '</option>';
                                } else {
                                echo
                                '<option value="' . strtolower($currency[0]) . '">' . strtoupper($currency[0]) . '</option>';
                                }
                            } else {
                                if(strtolower($currency[0]) == strtolower($_GET['currency'])) {
                                    echo
                                    '<option value="' . strtolower($currency[0]) . '" selected>' . strtoupper($currency[0]) . '</option>';
                                } else {
                                    echo
                                    '<option value="' . strtolower($currency[0]) . '">' . strtoupper($currency[0]) . '</option>';
                                }
                            }
                        }
                    ?>
                    </select>
                    
                    <select type="text" id="monthFilter" onChange="applyFilter(); updateChart();">
                        <option value="00">All</option>
                        <?php for ($i = 02; $i < 14;$i++) {
                            $m = date("m", mktime(0, 0, 0, $i, 0, 0));
                            $month = date("F", mktime(0, 0, 0, $i, 0, 0));
                            $thisMonth = date('m')+1;
                            if ($i == $thisMonth) {?>
                                <option value="<?= $m ?>" selected><?= $month ?></option>;<?php
                            } else { ?>
                                <option value="<?= $m ?>"><?= $month ?></option>;<?php
                            }
                        }
                        ?>
                    </select>
                <select name="year" id="yearFilter" onchange="applyFilter(); updateChart()">
                    <option value="" disabled selected>Year</option>
                    <option value="00">All</option>
                    <?php
                    for ($i=2000; $i<=2050; $i++) { 
                        $year = date('Y');
                        if ($i == $year) : ?>
                            <option value="<?= $i;?>" selected><?= $i;?></option><?php
                        else : ?>
                            <option value="<?= $i;?>"><?= $i;?></option><?php
                        endif;
                    }?>
                </select>
                

                <select type="text" id="countryFilter" onChange="applyFilter(); updateChart();">
                    <option value="all" disabled selected>Country</option>
                    <option value="all" id="allCountries">All</option>
                    <?php generateCountryList();?>
                </select>

            </div>
            <input type="text" class="search-input" id="search" placeholder="Search for description names...">
            </div>

            
            
            <div class="table-responsive" id="table-responsive">
                <form action="include/updateTransactionComplete.php" id="transactionComplete" method="post" style='display: none;'></form>
                <table class="table" id="table">
                    <tr class="header">
                        <th class="check">Paid</th>
                        <th class="date">Date</th>
                        <th class="country">Country</th>
                        <th class="type">Type</th>
                        <th class="category">Category</th>
                        <th class="description">Description</th>
                        <th class="value">Value</th>
                        <th class="edit"></th>
                        <th class="delete"></th>
                    </tr>
                    <form action="test" method="post">
                        <?php showTransactionsTable('all', 'history'); ?>
                    </form>
                </table>

            </div>
        </div>
    </div>
    
<script>
    // declaring global variables
    var totalIncome = 0;
    var totalExpense = 0;
    var totalResult = 0;
    var currentTotal = 0;
    var uniqueCategories = [];
    var categoriesTotal = [];
    let month = document.getElementById("monthFilter");
    let year = document.getElementById("yearFilter");
    let currency = document.getElementById("currencyFilter");
    let currenciesArray = <?= $currenciesArray; ?>;
    let defaultCurrency = '<?= $_SESSION['defaultCurrency']; ?>';

    function updateCurrencySymbol() {
        let balanceCurrency;

        if (currencyFilter == 'all') {
            balanceCurrency = '<?= $_SESSION['defaultCurrency']; ?>';
            return currenciesArray[balanceCurrency]['symbol']
        } 
        
        balanceCurrency = currencyFilter.toUpperCase();
        return currenciesArray[balanceCurrency]['symbol']

    }


    function showAllDates() {
        /**
         * Function activated when the page loads and the currency filter is not equal to ALL
         * This function works when the user selects to view the transaction history from a currency from the accounts page
         * @return void
         */
        if(window.location.pathname == '/Nomad_Wallet/history.php') { // End of function if the user is acessing the page without any parameters
            return;
        }

        if(currency.value == 'all') {
            return; // end of function.
        }

        year.value = '00';
        month.value = '00';
        return;
    }

    // Get the total income and expense and calculates the result to display at the result div and updates the period text with the choosen month and year
    function periodTotal() { 
        // Declare variables
        var value;
        var valueTxt;
        let transactionValue = document.getElementById('transaction-value');
        let transactionType = document.getElementById('transaction-type');
        let table = document.getElementById('table');
        let tr = table.getElementsByTagName('tr');
        let checkbox = document.getElementById("checkbox").checked;
        var allCategories = [];
        let initialValue = <?= floatval($_SESSION['initialValue']); ?>;
        const todaysDate = new Date();
        currentTotal = 0;
        totalIncome = 0;
        totalExpense = 0;
        totalResult = 0;
        categoriesTotal = [];


        // year filter variables
        let year = document.getElementById("yearFilter");
        let yearFilter = year.value;

        // month filter variables
        let month = document.getElementById("monthFilter");
        let monthFilter = month.value;

        // currency filter variables
        currency = document.getElementById("currencyFilter");
        currencyFilter = currency.value;


        if(currencyFilter.toUpperCase() == defaultCurrency.toUpperCase() || currencyFilter.toUpperCase() == 'ALL') { // If the currency filter is equal to the user default currency, then sum with the inital value

            currentTotal += initialValue;

        }


        // Looping over the table rows.
        for (i =1; i < tr.length; i++) {

            let date = tr[i].getElementsByTagName('td')[1]; // Get the date of each row
            let type = tr[i].getElementsByTagName('td')[3]; // Get the type of each row
            let currency = tr[i].getElementsByTagName("td")[6].getElementsByTagName("small")[0]; // Get the currency of each row
            let categoryColumn = tr[i].getElementsByTagName('td')[4]; // Get the value of each row
            let checkbox = tr[i].getElementsByTagName('td')[0]; // checkbox
            let description = tr[i].getElementsByTagName("td")[6];

            if(currencyFilter.toUpperCase() == 'ALL') {

                value = parseFloat(tr[i].getElementsByTagName('td')[6].dataset['converted'].replaceAll(',','')); // Get the converted value to defatul currency of each row
                valueTxt = parseFloat(value.toFixed(2));

            } else {

                value = tr[i].getElementsByTagName('td')[6];
                valueTxt = parseFloat(value.innerHTML);

            }
            

            if(date) {

                let dateValue = date.innerHTML || date.innerText;
                let [day, month, year] = dateValue.split("/");
                let prepareDateFormat = month + '-' + day + '-' + year;
                let dateObject = new Date(prepareDateFormat);
                let currencyValue = currency.textContent || currency.innerText;

                if(dateObject <= todaysDate && currencyValue.toUpperCase() == currencyFilter.toUpperCase() || currencyFilter.toUpperCase() == 'ALL') {

                    currentTotal += valueTxt;

                }
                
            }

            if(type && getComputedStyle(tr[i]).display == 'table-row') { //Get the HTML content
                typeTxt = type.textContent || type.innerText;
                totalResult += valueTxt;
                
                if(typeTxt.toUpperCase() == 'INCOME') { // Sum the total for income rows

                    totalIncome += valueTxt;
                    
                } else if (typeTxt.toUpperCase() == 'EXPENSE') { // Sum the total for expense rows

                    totalExpense += valueTxt;
                    allCategories.push(categoryColumn.innerHTML);
                    
                }
            }
        }

        uniqueCategories = [...new Set(allCategories)];

        function sumCategoriesValues(category) {
            let categoryTotal = 0
            let tr = table.getElementsByTagName('tr');

            for (i = 1; i < tr.length; i++) {

                let type = tr[i].getElementsByTagName('td')[3]; // Get the type of each row
                let value = tr[i].getElementsByTagName("td")[6]; // Get the currency of each row
                let categoryColumn = tr[i].getElementsByTagName('td')[4]; // Get the value of each row
                
                if(getComputedStyle(tr[i]).display == 'table-row') { //Get the HTML content
                    
                    let typeTxt = type.innerHTML;
                    console.log(valueTxt);

                    if(currencyFilter.toUpperCase() == 'ALL') {

                        value = parseFloat(tr[i].getElementsByTagName('td')[6].dataset['converted'].replaceAll(',','')); // Get the converted value to defatul currency of each row
                        valueTxt = parseFloat(value.toFixed(2));

                    } else {

                        value = tr[i].getElementsByTagName('td')[6];
                        valueTxt = parseFloat(value.innerHTML);

                    }

                    if(typeTxt.toUpperCase() == 'EXPENSE' && categoryColumn.innerHTML.toUpperCase() == category.toUpperCase()) { // Sum the total for income rows
                        categoryTotal += Math.abs(valueTxt); // Generate the total value for each category
                        console.log('total : ' + categoryColumn.innerHTML + categoryTotal);

                    }
                }  
            }
            
            categoriesTotal.push(categoryTotal);
        }

        console.log(categoriesTotal);
        uniqueCategories.forEach(element => sumCategoriesValues(element));

        // display the choosen month and year text
        // declaing variables

        let monthTxt = month.options[month.selectedIndex].text;

        let yearTxt = year.options[year.selectedIndex].text;
        let title = yearTxt + " " + monthTxt; // Month and year title variable
        let result = totalResult; // result value variable

        document.getElementById('result').innerHTML = updateCurrencySymbol() + result.toFixed(2); //prints the result
        document.getElementById('current-result').innerHTML = updateCurrencySymbol() + currentTotal.toFixed(2);
        
        if (yearTxt == 'All' && monthTxt == 'All') {
            let title = 'All periods';
            document.getElementById('title-period').innerHTML = title; //prints the title
        } else {
            document.getElementById('title-period').innerHTML = title; //prints the title
        }

        return totalExpense, totalIncome, result, categoriesTotal;

    }
    
    let currentCountry = '<?= ucfirst($_SESSION['country']) ?>';
   

    function applyFilter() {  // FUNCTION TO FILTER THE DATES ON THE TRANSACTION TABLE HISTORY.
        // Declare variables
        var table, tr, i, year, yearFilter, month, monthFilter, type, typeFilter, categories, categoriesFilter, tdDate, tdType, tdCategories, dateValue, typeValue, categoriesValue;
        // year filter variables
        year = document.getElementById("yearFilter");
        yearFilter = year.value;

        // month filter variables
        month = document.getElementById("monthFilter");
        monthFilter = month.value;

        // type filter variables
        type = document.getElementById("typeFilter");
        typeFilter = type.value;

        // category filter variables
        categories = document.getElementById("categoriesFilter");
        categoriesFilter = categories.value;

        // currency filter variables
        currency = document.getElementById("currencyFilter");
        currencyFilter = currency.value;
        
        // country filter variables
        country = document.getElementById("countryFilter");
        countryFilter = country.value;        

        // table variables
        table = document.getElementById("table");
        tr = table.getElementsByTagName("tr");

        // Loop through all table rows, and hide those who don't match the search query
        for (i = 1; i < tr.length; i++) {
            tdDate = tr[i].getElementsByTagName("td")[1]; // date column
            tdType = tr[i].getElementsByTagName("td")[3]; // type column
            tdCategories = tr[i].getElementsByTagName("td")[4]; // categories column
            tdcurrency = tr[i].getElementsByTagName("td")[6].getElementsByTagName("small")[0]; // currency column
            tdCountry = tr[i].getElementsByTagName("td")[2]; // Country column
        

            if (tdDate && tdType && tdCategories && tdcurrency && tdCountry) {
                dateValue = tdDate.textContent || tdDate.innerText; // date cell values
                typeValue = tdType.textContent || tdType.innerText; // type cell values
                categoriesValue = tdCategories.textContent || tdCategories.innerText; // category cell values
                currencyValue = tdcurrency.textContent || tdcurrency.innerText; // currency cell values
                countryValue = tdCountry.textContent || tdCountry.innerText; // category cell values
                
                let [day, monthValue, yearValue] = dateValue.split("/"); // split the cell date into variables.

                let displayRow = true; 
                
                /*Checks if the filters for each column is true for a non match condition and then tell that the row should be hide by the variable displayRow.
                if any condition below is false then the row going to be hidden. */
                if (yearFilter != 00 && yearValue != yearFilter) {
                    displayRow = false;
                    
                }
                if (monthFilter!= 00 && monthValue != monthFilter) {
                    displayRow = false;
                    
                }
                if (typeFilter != 'all' && typeValue.toUpperCase() != typeFilter.toUpperCase()) {
                    displayRow = false;
                    
                }
                if (categoriesFilter != 'all' && categoriesValue.toUpperCase() != categoriesFilter.toUpperCase()) {
                    displayRow = false;
                    
                }
                if (currencyFilter != 'all' && currencyValue.toUpperCase() != currencyFilter.toUpperCase()) {
                    displayRow = false;
                    
                }
                if (countryFilter != 'all' && countryValue.toUpperCase() != countryFilter.toUpperCase()) {
                    displayRow = false;
                    
                }

                // check if the row is true (show) or false (hide).
                if (displayRow == true) {
                    tr[i].style.display = "table-row";
                } 
                else {
                    tr[i].style.display = "none";
                }
                
            }
        }
    }

    function search() {
        // Declare variables
        let input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("search");
        filter = input.value.toUpperCase();
        table = document.getElementById("table");
        tr = table.getElementsByTagName("tr");

        // Loop through all table rows, and hide those who don't match the search query
        if(filter == '') {
            applyFilter();
            updateChart();
            return;
        }

        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[5];
            if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
            }
        }

        updateChart();
        
    }

    let searchBar = document.getElementById("search");

    searchBar.addEventListener(
        "input", (e) => {
                search();

        },
    false
    )
     
    /* HORIZONTAL BARS INCOME/EXPENSE RESULT CHART */
    var options = {
        series: [{
        data: [totalIncome, totalExpense],
        
    }],
        chart: {
        type: 'bar',
    },
    plotOptions: {
        bar: {
        borderRadius: 4,
        horizontal: true,
        }
    },
    dataLabels: {
        enabled: false
    },
    xaxis: {
        categories: ['Income', 'Expense',],
    }
    };

    var chart = new ApexCharts(document.querySelector("#chart-income-expense"), options);
    chart.render();


    /* DONUT CATEGORY CHART */
    var options = {
        series: categoriesTotal,
        labels: uniqueCategories,
        chart: {
        type: 'donut',
        height: 170,
    },
    responsive: [{
        options: {
        legend: {
            position: 'bottom'
        }
        }
    }],
    noData: {
        text: 'No expense',
        align: 'center',
        verticalAlign: 'middle',
        offsetX: 0,
        offsetY: 0,
        style: {
            color: '#b6b6b6',
            fontFamily: 'Poppins',
            fontSize: '2em'
        }
        },
    legend: {
        show: true,
        horizontalAlign: 'left'
    }
    };

    var donut = new ApexCharts(document.querySelector("#chart-categories"), options);
    donut.render();


    function updateChart() {
    periodTotal(); 
    chart.updateSeries(chart.updateSeries([{data: [totalIncome, totalExpense]}]));
    donut.updateOptions({
        labels: uniqueCategories,
        series: categoriesTotal,
    });
    }


    function reply_click(clicked_id) {
        let inputId = document.getElementById('id-input-delete');
        inputId.value = clicked_id;
    }
   
    setTimeout(function () {
        showAllDates();
        applyFilter();
        updateChart();
    }, 1000)
console.log(categoriesTotal);
</script>

</body>

</html>
