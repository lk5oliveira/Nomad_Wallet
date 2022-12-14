<?php
    session_start();
    include('connect.inc.php');
    include('prepare_data.php');

    if(isset($_POST)) {
        if(isset($_POST['complete'])) {
            $complete = mysqli_real_escape_string($connection, $_POST['complete']);
        } else {
            $complete = '0';
        }
        
        $transactionId = prepareData($_GET['id']);
        $userID = prepareData($_SESSION['userID']);
        $previousPage = $_SERVER['HTTP_REFERER'];

        $query = "UPDATE transactions SET `transactions_complete` = ? WHERE `user_id` = ? AND `transactions_id` = ?;";
        $stmt = $connection->prepare($query);
        $stmt->bind_param('sss', $complete, $userID, $transactionId);

        echo '<script>history.go(-1);</script>';
    } else {
        echo 'error';
        echo '<script>history.go(-1);</script>';
    }
?>