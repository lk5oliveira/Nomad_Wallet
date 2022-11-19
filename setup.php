<?php
    session_start();
    include('include/world-currency.php');

    if($_SESSION['register'] != 'register') {
      header('location: panel.php');
    }
?>
<style>
/* The switch - the box around the slider */
.switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
  }
  
  /* Hide default HTML checkbox */
  .switch input {
    opacity: 0;
    width: 0;
    height: 0;
  }
  
  /* The slider */
  .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    -webkit-transition: .4s;
    transition: .4s;
  }
  
  .slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    -webkit-transition: .4s;
    transition: .4s;
  }
  
  input:checked + .slider {
    background-color: #2196F3;
  }
  
  input:focus + .slider {
    box-shadow: 0 0 1px #2196F3;
  }
  
  input:checked + .slider:before {
    -webkit-transform: translateX(26px);
    -ms-transform: translateX(26px);
    transform: translateX(26px);
  }
  
  /* Rounded sliders */
  .slider.round {
    border-radius: 34px;
  }
  
  .slider.round:before {
    border-radius: 50%;
  }

  select:required:invalid, input#initial-value::placeholder {
  color: gray;
}

/* Tooltip container */
.tooltip {
  position: relative;
  display: inline-block;
  border-bottom: 1px dotted black; /* If you want dots under the hoverable text */
}

/* Tooltip text */
.tooltip .tooltiptext {
  visibility: hidden;
  width: 120px;
  background-color: #555;
  color: #fff;
  text-align: center;
  padding: 5px 0;
  border-radius: 6px;

  /* Position the tooltip text */
  position: absolute;
  z-index: 1;
  bottom: 125%;
  left: 50%;
  margin-left: -60px;

  /* Fade in tooltip */
  opacity: 0;
  transition: opacity 0.3s;
}

/* Tooltip arrow */
.tooltip .tooltiptext::after {
  content: "";
  position: absolute;
  top: 100%;
  left: 50%;
  margin-left: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: #555 transparent transparent transparent;
}

/* Show the tooltip text when you mouse over the tooltip container */
.tooltip:hover .tooltiptext {
  visibility: visible;
  opacity: 1;
}


</style>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/a440aae6fe.js" defer crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="css/login-register-style.css">
    <script src="https://plentz.github.io/jquery-maskmoney/javascripts/jquery.maskMoney.min.js" type="text/javascript"></script>
    <title>Register</title>
</head>
<body>
    <main>
        <div class="container">
            <div class="signup-form">

            <h3 style="text-align:center;margin-bottom: 2rem;">Getting start</h3>

                <form action="include/login/setup.php" method="POST">
                  <div class="mb-3">
                    <i class="fa-solid fa-location-dot"></i>
                    <select type="text" name="defaultCountry" id="defaultCountry" class="login-input" placeholder="Enter your name" autocomplete="off" required>
                      <option value="" style="color: gray;" selected disabled>Home country</option>
                      <?php for($i = 0;$i <= $countryArraySize;$i++) {
                          echo '<option value="' . ucfirst(strtolower($countryCurrency[$i]['country'])) . '">' . ucfirst(strtolower($countryCurrency[$i]['country'])) . '</option>';
                      }
                      ?>
                    </select>
                  </div>
                  <div class="mb-3">
                  <i class="fa-solid fa-money-bill"></i>
                  <select name="defaultCurrency" type="text" id="currency-field" class="login-input" required>
                    <option value="" disabled selected>Default currency</option>
                      <?php for($i = 0;$i <= $countryArraySize;$i++) {
                        echo '<option value="' . $countryCurrency[$i]['currency_code'] . '">' . $countryCurrency[$i]['currency_code'] . '</option>';
                      }
                      ?>
                    </select>
                  </div>
                  <div class="mb-3">
                    <i class="fa-solid fa-piggy-bank"></i>
                    <input type="text" name="initial-value" id="initial-value" placeholder="Initial balance" class="login-input">
                  </div>
                  <div class="mb-3" id="container-password">
                  <span><i class="fa-solid fa-plane-departure" style="position: initial;"></i> Are you travelling right now?</span>
                  <label class="switch">
                    <input id="checkbox" type="checkbox" name="mode" value="travel">
                    <span class="slider round"></span>
                  </label>
                  </div>
                  <button type="submit" name="submit">Start</button>
                </form>
            </div>
        </div>
    </main>
    <script>
        jQuery(function() {
            
            jQuery("#initial-value").maskMoney({ 
            thousands:',', 
            decimal:'.'
            })

        });
    </script>

</body>
</html>