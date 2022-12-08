<?php 
    session_start();


    include('include/login/verify_login.inc.php');
    backToIndex();  

    if(!isset($_GET['currencyFilter'])) {
        $currencyFilter = $_SESSION['currencyCode'];
    } else {
        $currencyFilter = $_GET['currencyFilter'];
    }
    
    include('include/total.php');
    include('include/chart.php');
    include('include/generate_account_list.php');

    $currencyList = getCurrencyList();
    if(empty($currencyList)) {
        $currencyList = array(array($currencyFilter));
    }

    $total = getTotal('all', 'all', $currencyFilter); // balance total

    if(strtolower($currencyFilter )== strtolower($_SESSION['defaultCurrency'])) {
        $total += floatval($_SESSION['initialValue']); // if balance total is equal to 
    }

    if(!isset($yearDiff)) {
        $yearDiff = '';
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
    <link rel="stylesheet" href="css/dashboard-style.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts" ></script>
    <script src="https://kit.fontawesome.com/a440aae6fe.js"  crossorigin="anonymous"></script>
    <script src="include/JS/menu.js" defer></script>
    <script src="include/JS/transactions-button.js" defer></script>
    <title>Dashboard</title>
</head>
<body onresize="graph();resize();">


    <div class="" id="main-container">

        <?php include('include/transaction-form.php'); ?>

        <div class="page-title">
            <h2 class="title-text">Dashboard</h2>
        </div>
        <!---LEFT SIDE MENU-->

        <?php include('menu.php'); ?>

        <!--Main dashboard - Using grid system to organize. Each child is a grid row-->
        
        <div id="dashboard">
     
        <!--row 1-->
        <div id="greetings">
            <form action="" method="get">
                <select name="currencyFilter" id="currencyFilter" class="filter-currency" onchange="this.form.submit()">
                        <?php
                            foreach($currencyList as $key => $currency) {
                                if(!isset($_GET['currencyFilter'])) {
                                    if(strtolower($currency[0]) == strtolower($_SESSION['currencyCode'])) {
                                        echo
                                        '<option value="' . strtolower($currency[0]) . '" selected>' . strtoupper($currency[0]) . '</option>';
                                    } else {
                                    echo
                                    '<option value="' . strtolower($currency[0]) . '">' . strtoupper($currency[0]) . '</option>';
                                    }
                                } else {
                                    if(strtolower($currency[0]) == strtolower($_GET['currencyFilter'])) {
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
            </form>
        </div>
        <?php include ('include/button-transactions.php'); ?>

        <!--row 2 - Balance and month comparison-->

            <div class="balance-container" id="balance">
            <h6 class="grid-title">Balance</h6>
                <div class="dash-card balance-div" id="balance">
                    <h5 class="dolar-text"><?= $_SESSION['defaultSymbol']?><?= number_format(($total), 2, ',', '.');?></h5>
                </div>
            </div>
            <div class="month-comparison-container" id="month-comparison">
                <h6 class="grid-title">Overview</h6>
                <div class="month-comparison-div">
                    <div id="current">
                        <h6 class="currently-title"><b>CURRENTLY</b> <br>month result</h6>
                        <h5 class="result-text"><?= $_SESSION['defaultSymbol'] . number_format($resultArray[$currentMonth],2, ",", ".")?></h5>
                        <span class="dif-container"><h6 class="dif-text">
                        <?php
                        if ($resultArray[$previousMonth] != 0) {
                            $percentageDif = floatval(($resultArray[$currentMonth] - $resultArray[$previousMonth]) / $resultArray[$previousMonth] * 100);
                            echo number_format($percentageDif, 2) . "%";
                        } else {
                            echo '-';
                        }?>
                        </h6><p>Compared with last month</p></span>
                    </div>
                </div>
            </div>

        <!--row 3 - Chart and upcoming-->

            <div class="upcoming-container">
                <h5 class="grid-title">Upcoming</h5>
                <div class="dash-card" id="upcoming">
                    <div class="table" id="table">
                        <?php 
                        if(showTransactionsTable(5, 'upcoming') == 0) {
                            echo '<div class="no-data-div"><p class="no-data">No upcoming transaction</p></div>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="dash-card chart-div" id="result-chart">
                <h5>Month by month</h5>
                <div id="chart"></div>
            </div>

            <div class="dash-card chart-div" id="year-chart">
                    <h5>Year overview</h5>
                    <h6><b>This year:</b> 
                    <?php if ($yearDiff > 0): echo "<span class='signal signal-positive'>+</span>" . " " . $_SESSION['defaultSymbol'] . number_format($yearDiff,2, ",", "."); 
                    elseif ($yearDiff < 0 ): echo "<span class='signal signal-negative'>-</span>" . " " . $_SESSION['defaultSymbol'] . ltrim(number_format(floatval($yearDiff),2, ",", "."), "-"); 
                    else: echo $_SESSION['defaultSymbol'] . $yearDiff; endif?></h6>
                <div id="growth"></div>
            </div>

        </div>
    </div>

<script>
        let menu = document.getElementById('menu-bar');


        //CLOSE MENU WHEN CLICK OUTSIDE THE ELEMENT.
        document.addEventListener('click', function(event) {
            if (menu.className === 'menu-slideIn' || menu.className === 'menu-open') {
                let isClickInsideElement = menu.contains(event.target);
                if (!isClickInsideElement) {
                    console.log('outside');
                    //PHONE SCREEN.
                    if (window.matchMedia("(max-width: 480px)").matches) {
                        if (menu.className = 'menu-slideIn') {
                            menu.classList.remove('menu-slideIn');
                            menu.classList.add('menu-slideOut');
                            menu.style.left = -999;
                            console.log('480px');
                        }
                    //TABLET SCREEN.    
                    } else if ((window.matchMedia("(min-width: 481px)").matches)) {
                        menu.classList.remove('menu-open');
                        menu.classList.add('menu-close');
                        menu.classList.remove('menu.text-display-none');
                        menu.style = '';
                        console.log('>481px')
                    }
                }
            }
        });
        // UPDATE THE MENU FIELDS WHEN THE DEVICE ORIENTATION CHANGES
        window.addEventListener("orientationchange", event => {
            menu.className = '';
            menu.style = '';
            console.log('New class name' + menu.className);
        });

        function slideIn() {
            // PHONE SCREEN - VERTICAL
            if (window.matchMedia("(max-width: 480px)").matches) {
                console.log('480');
                // CLOSE THE MENU - if open class is on, remove class add close class.
                if (menu.classList.contains('menu-slideIn')) {
                    menu.classList.remove('menu-slideIn');
                    menu.classList.add('menu-slideOut');
                    menu.style.left = -999;
                // OPEN THE MENU - if close class is on, remove class and add open class.
                } else {
                    menu.classList.remove('menu-slideOut');
                    menu.classList.add('menu-slideIn');
                    menu.style.left = 0;
                }
            // TABLET AND HORIZONTAL PHONE SCREEN.
            } else if ((window.matchMedia("(min-width: 481px)").matches)) {
                //Remover any possible style from vertical mobile device.
                menu.classList.remove('menu-slideIn');
                menu.classList.remove('menu-slideOut');
                menu.style.left = 0;
                //CLOSE THE MENU - If it's open, remove the open class and add the close class.
                if (menu.classList.contains('menu-open')) {
                    menu.classList.remove('menu-open');
                    menu.classList.add('menu-close');
                    menu.style.width = '';
                    /*changeAllMenuText('none');*/
                } else {
                //OPEN THE MENU - If it's close, remove the close class and add the open class.
                    menu.classList.remove('menu-close');
                    menu.classList.add('menu-open');
                    menu.style.width = 250;
                }
            }
        }

        function resize() {
            menu.className = '';
            menu.style = '';
        }
</script>


<script>
/* 
* VERTICAL GRAPH
* PHP preparation first
* JS apex graphs options and rendering
*/
<?php
    $js_array = json_encode($resultArray);
    echo "const dataArray = " . $js_array . "\n";
?>
console.log (dataArray);

function graph() {
    var options = {
          series: [{
          name: 'Result',
          data: dataArray
        }],
          chart: {
          type: 'bar',
          height: '90%'
        },
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: '55%',
            borderRadius: 10
          },
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          show: true,
          width: 2,
          colors: ['transparent']
        },
        xaxis: {
          categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dez'],
        },
        yaxis: {
          title: {
            text: '$'
          }
        },
        fill: {
          opacity: 1
        },
        colors: [function({ value, seriesIndex, w }) {
        if (value < 0) {
            return '#d9534f'
        } else {
            return '#5f96e5'
        }
        }],
        tooltip: {
          y: {
            formatter: function (val) {
              return "$ " + val
            }
          }
        }
        };

var chart = new ApexCharts(document.querySelector("#chart"), options);


chart.render();
}

graph();
</script>

<script>
/* 
* LINE GRAPH
* PHP preparation first
* JS apex graphs options and rendering
*/
<?php

    $yearOverViewArray = json_encode($arrayToEnconde);
    echo "const yearOverViewArray = " . $yearOverViewArray . "\n";
?>

const currentYearOverViewData = yearOverViewArray[2022];
console.log(currentYearOverViewData);


var options = {
  chart: {
    height: "80%",
    type: "area",
    toolbar: {
        show: true,
            offsetX: 0,
            offsetY: 0,
            tools: {
                download: true,
                selection: false,
                zoom: false,
                zoomin: false,
                zoomout: false,
                pan: false,
                reset: false | '<img src="/static/icons/reset.png" width="20">',
                customIcons: []
            }
  }
  },
  dataLabels: {
    enabled: false
  },
  series: [
    {
      name: "Series 1",
      data: currentYearOverViewData
    }
  ],
  fill: {
    type: "gradient",
    gradient: {
      shadeIntensity: 1,
      opacityFrom: 0.7,
      opacityTo: 0.9,
      stops: [0, 90, 100]
    }
  },
  xaxis: {
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dez']
  }
};



var chart = new ApexCharts(document.querySelector('#growth'), options);

chart.render();

</script>

</body>
</html>