<?php

session_start();

if(!isset($_SESSION['id']))
{
    header(
        "Location: ../auth/login.php"
    );

    exit();
}

?>