<?php
include("config.php");
session_start();

if(isset($_SESSION['uid'])){
    if(isset($_POST['theme'])){
        $theme = $_POST['theme'];
        $uid = $_SESSION['uid'];

        $allowed_themes = ['light', 'dark'];
        if (!in_array($theme, $allowed_themes)) {
            $theme = 'light';
        }

        echo htmlspecialchars($theme, ENT_QUOTES, 'UTF-8');

        $query = "UPDATE `users` SET `theme` = ? WHERE `users`.`id` = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $theme, $uid);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}
?>
