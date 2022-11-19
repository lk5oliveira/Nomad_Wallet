<?php

// To use on the Panel page - If the user is not logged in he is back to index/login page.
function backToIndex() {
    if(!$_SESSION['email']) {

        header('location: index.php');
        exit();

    } elseif ($_SESSION['register'] == "register") {

        header('location: setup.php');
        exit();
    }
}

// to use in other pages where a logged user should not have access.
function backToPanel() {
    if(isset($_SESSION['email'])) {
        header('Location: panel.php');
    }
}


?>