<?php

include("verificarSession.php");

$rolesPermitidos = ['CEO'];

if (!isset($_SESSION['tipo']) || !in_array($_SESSION['tipo'], $rolesPermitidos, true)) {
    header("Location: ../auth/login.php");
    exit();
}