<?php

include("../../includes/verificarSession.php");
include("../../includes/header.php");

$codReserva = $_GET['codNovedad'];


$sql = "

SELECT *

FROM novedades

WHERE codNovedad = $codReserva

";
$resultado = mysqli_query(
    $link, 
    $sql);

$novedad = mysqli_fetch_assoc($resultado);

?>

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-8">

<div class="card card-custom">

<div class="card-body p-5">

<h2><?= $novedad['tituloNovedad'] ?></h2>

<?php if(isset($_GET['error'])){ ?>

<div class="alert alert-danger">

    <?= $_GET['error'] ?>

</div>

<?php } ?>

<div class="mb-3 ">
    Fecha de publicacion: <?= $novedad['fechaPublicacion'] ?>
</div>
<div class="mb-3">
    Fecha de expiracion: <?= $novedad['fechaExpiracion'] ?>
</div>
<div class="mb-3">
    Novedad:<br>
    <?= $novedad['textoNovedad'] ?>
</div>

