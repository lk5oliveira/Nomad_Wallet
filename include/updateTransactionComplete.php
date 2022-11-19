<?php
    session_start();
    include('connect.inc.php');

    if(isset($_POST)) {
        if(isset($_POST['complete'])) {
            $complete = mysqli_real_escape_string($connection, $_POST['complete']);
        } else {
            $complete = '0';
        }
        
        $transactionId = $_GET['id'];
        $userID = $_SESSION['userID'];
        $previousPage = $_SERVER['HTTP_REFERER'];


        $query = "UPDATE transactions SET `transactions_complete` = '$complete' WHERE `user_id` = '$userID' AND `transactions_id` = '$transactionId';";

        $execQuery = mysqli_query($connection, $query);

        echo '<script>history.go(-1);</script>';
    } else {
        echo 'error';
        echo '<script>history.go(-1);</script>';
    }
?>