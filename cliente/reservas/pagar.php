<?php

include("../../includes/verificarSession.php");
include("../../includes/header.php");

$codReserva = $_GET['codReserva'];


$sql = "

SELECT *

FROM reservas

WHERE codReserva = $codReserva

";
$resultado = mysqli_query(
    $link, 
    $sql);

$reserva = mysqli_fetch_assoc($resultado);

$codVuelo= $reserva['codVuelo'];

$sqlvuelos = "
SELECT *

FROM vuelos

WHERE codVuelo = $codVuelo

";
$resultadoVuelo= mysqli_query(
    $link,
    $sqlvuelos);

$vuelo= mysqli_fetch_assoc($resultadoVuelo);
?>


<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-8">

<div class="card card-custom">

<div class="card-body p-5">

<h2>Pagar reserva</h2>

<?php if(isset($_GET['error'])){ ?>

<div class="alert alert-danger">

    <?= $_GET['error'] ?>

</div>

<?php } ?>

<div class="mb-3">
    Origen: <?= $vuelo['origenVuelo'] ?>
</div>

<div class="mb-3">
    Destino: <?= $vuelo['destinoVuelo'] ?>
</div>

<div class="mb-3">
    Fecha: <?= $vuelo['fechaVuelo'] ?>
</div>

<div class="mb-3">
    Cantidad de asientos: <?= $reserva['cantAsientos'] ?>
</div>

<div class="mb-3">
    Precio total: $<?= $reserva['precioFinal'] ?>
</div>

<form action="confirmarPago.php" method="post">

    <input
        type="hidden"
        name="codReserva"
        value="<?= $codReserva ?>"
    >

    <button
        type="submit"
        class="btn btn-success"
    >
        Confirmar pago
    </button>

</form>