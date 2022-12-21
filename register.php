<?php
    session_start();
    
    include('include/login/verify_login.inc.php');
    include_once ("include/connect.inc.php");
    include_once ("include/prepare_data.php");
    include("include/gravatar.php");

    backToPanel();
    $userName = "";
    $userEmail = "";
    $userPassword = "";
    $userPasswordRepeat= "";
    $validationMessage = "";
    $classError_name = "";
    $classError_email = "";
    $classError_password = "";



if (isset($_POST["submit"])) {
    
    $userName = prepareData($_POST["username"]);
    $userEmail = prepareData(strtolower($_POST["email"]));
    $userPassword = mysqli_real_escape_string($connection, $_POST["password"]);
    $userPasswordRepeat = mysqli_real_escape_string($connection, $_POST["confirm-password"]);

        // Checking for register under the email
        $sql_select = $connection->prepare("SELECT * FROM users WHERE usersEmail = ?");
        $sql_select->bind_param("s", $userEmail);
        $sql_select->execute();
        $sql_result = $sql_select->get_result()->num_rows;

        if($sql_result === 0) { // if the email is not registered

            if($userPassword == $userPasswordRepeat) {

                if(strlen($userPassword) < 4) {

                    $validationMessage = "Password must be at least 4 characters in length.";
                    $classError_password = "validation-error";

                } elseif(empty($userName)) {

                    $validationMessage  = 'name can not be empty';
                    $classError_name = "validation-error";

                } elseif (!preg_match("/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ ]+$/", $userName)) {

                    $validationMessage  = 'name can only contains letters and space';
                    $classError_name = "validation-error";

                } else {

                    $sql_insert = $connection->prepare("INSERT INTO users (usersName, usersEmail, usersPwd) VALUES (?, ?, ?)");
                    $sql_insert->bind_param("sss", $userName, $userEmail, $userPassword);
                    $sql_insert->execute();

                    $_SESSION['register'] = 'register'; // THE USER JUST REGISTER AND IS ALLOWED TO GO TO SETUP PAGE
                    
                    //Discover the user ID - necessary to query some data from de DB
                    $sql_select = $connection->prepare("SELECT * FROM users WHERE usersEmail = ?");
                    $sql_select->bind_param("s", $userEmail);
                    $sql_select->execute();
                    $sqlResult = $sql_select->get_result()->fetch_array();

                    $_SESSION['userID'] = $sqlResult['usersID']; //Create a session with the user ID.
                    $_SESSION['user'] = $userName;
                    $_SESSION['email'] = $userEmail;
                    $_SESSION['gravatar'] = get_gravatar($_SESSION['email']);

                    header('location: setup.php');

                }
                
            } else {

                $validationMessage = "Passwords doesn't match.";
                $classError_password = "validation-error";

            }

            
        } else {

            $validationMessage = "This email is already registered";
            $classError_email= "validation-error";
        }

} else {

    $validationMessage = "";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="img/fav_icons/fav_icon.ico">
    <script src="https://kit.fontawesome.com/a440aae6fe.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="css/login-register-style.css">
    <title>Register</title>
</head>
<body>
    <main>
        <div class="container">
            <div class="signup-form">
            <div id="register-signup">
                <a href="index.php" id="register-link">Sign up</a>
                <p id="sign-up">Register</p>
                </div>
                <?php if($validationMessage != "") : ?>
                <div class="alert">
                    <span> <?= $validationMessage ?> </span>
                </div>
                <?php endif ?>
                <form action="" method="POST">
                    <div class="mb-3 <?= $classError_name ?>">
                        <i class="fa-solid fa-person"></i>
                        <input type="text" name="username" id="username" class="login-input" placeholder="Name" value='<?php if(isset($_POST["username"])) { echo htmlentities($_POST["username"]); }?>' autocomplete="off" required>
                    </div>
                    <div class="mb-3 <?= $classError_email ?>">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="email" name="email" id="email" placeholder="E-mail" class="login-input" value='<?php if(isset($_POST["email"])) { echo htmlentities($_POST["email"]); }?>' required>
                    </div>
                    <div class="mb-3 <?= $classError_password ?>" id="container-password">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" id="password" placeholder="Password" class="login-input password" required>
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="confirm-password" id="confirm-password" placeholder="Confirm password" class="login-input password" required>
                    </div>
                    <button type="submit" name="submit">Sign up</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>