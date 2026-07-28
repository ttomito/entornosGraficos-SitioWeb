<?php

include("verificarSession.php");

// confirmar que solo el admin tenga acceso
$rolesPermitidos = ['ADMIN'];

if (!isset($_SESSION['tipo']) || !in_array($_SESSION['tipo'], $rolesPermitidos, true)) {
    header("Location: ../auth/login.php");
    exit();
}