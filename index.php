<?php
    session_start();
    include('include/login/verify_login.inc.php');
    backToPanel();

    $user = '';

    if(isset($_SESSION['return_user'])) {
        $user = htmlspecialchars($_SESSION['return_user']);
    }

    if(!isset($_SESSION['attempts'])) {
        $_SESSION['attempts'] = 0;
        $_SESSION['last_attempt'] = 0;
    } elseif($_SESSION['attempts'] > 2) {
        if((time() - $_SESSION['last_attempt']) > 300) {
            $_SESSION['attempts'] = 0;
        }
    }


?>

<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="img/fav_icons/fav_icon.ico">
    <meta name="theme-color" content="#030303">
    <link rel="manifest" crossorigin="use-credentials" href="manifest.json"/>
    <link rel="apple-touch-icon" href="/imgs/icon-192x192.png">
    <link rel="canonical" href="https://nomadwallet.zya.me/">
    <script src="https://kit.fontawesome.com/a440aae6fe.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="css/login-register-style.css">
    <title>Login page</title>

</head>
<body>
    <main>
        <div class="container">
            <div class="now-logo">
                    <img src="img/logo.webp" alt="nomad-wallet-logo" class="img-logo">
                </div>
            <div class="signup-form">
                <div id="register-signup">
                    <p id="sign-up">Sing up</p>
                    <a href="register.php" id="register-link">Register</a>
                </div>

                <?php
                if(isset($_SESSION['no-auth']) || $_SESSION['attempts'] > 2) :?>
                <div class="alert">
                    <span><?php if($_SESSION['attempts'] > 2) : ?> Too many attempts. Try again later. <?php else : ?> Error: wrong login or password <?php endif ?></span>
                </div>
                <?php endif;
                unset($_SESSION['no-auth']);
                ?>
                <form action="include/login/login.inc.php" method="POST">
                    <div class="mb-3">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" name="user" id="username" placeholder="username" class="login-input" value="<?= $user ?>" autocomplete="off" required>
                    </div>
                    <div class="mb-3">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" id="password" placeholder="password" class="login-input" required>
                        <div class="caps-on-alert" id="caps-on-alert"></div>
                    </div>
                    <button type="submit" name="submit">Log in</button>
                </form>
            </div>
        </div>
    </main>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
<script>

    const alertMessage = document.getElementById('caps-on-alert');
    const passwordInput = document.getElementById('password');

    passwordInput.addEventListener('keyup', function (e) {
        if (e.getModifierState('CapsLock')) {
            alertMessage.textContent = 'Caps lock is on';
        } else {
            alertMessage.textContent = '';
        }
    })



</script>

</html>