<?php

include("../../includes/verificarSession.php");
include("../../includes/header.php");

$codReserva = $_GET['codReserva'];


$idUsuario =
$_SESSION['id'];

$sql = "

SELECT *

FROM reservas

WHERE codReserva = $codReserva

AND codUsuario = $idUsuario

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

<h2>Datos reserva</h2>

<?php if(isset($_GET['error'])){ ?>

<div class="alert alert-danger">

    <?= $_GET['error'] ?>

</div>

<?php } ?>



<div class="mb-3">
    Fecha de vuelo: <?= $vuelo['fechaVuelo'] ?>
    
</div>
<div class="mb-3">
    Horario del vuelo: <?= $vuelo['horaSalida'] ?>
</div>
<div class="mb-3">
    Origen: <?= $vuelo['origenVuelo'] ?>
</div>
<div class="mb-3">
    Destino: <?= $vuelo['destinoVuelo'] ?>
</div>
<div class="mb-3">
    Precio: <?= $vuelo['precioVuelo'] ?>
</div>
<div class="mb-3">
    Asientos disponibles: <?= $vuelo['asientosDisponibles'] ?>
</div>
<div class="mb-3 d-flex">
    Asientos reservados: <?= $reserva['cantAsientos'] ?>
</div>
<div class="mb-3">
    Estado: <?= $reserva['estadoReserva'] ?>
</div>
<div>
<?php if ($reserva['estadoReserva']=='PENDIENTE'){ ?>
<a href="pagar.php?codReserva=<?= $codReserva ?>" class="btn btn-primary">

Pagar reserva

</a>

<a href="modificar.php?codReserva=<?= $codReserva ?>" class="btn btn-secondary">

Modificar reserva

</a>

<a href="cancelarReserva.php?codReserva=<?= $codReserva ?>" class="btn btn-danger" onclick="return confirm('¿Está seguro que desea cancelar la reserva?')">

Cancelar Reserva

</a>

<?php } elseif ($reserva['estadoReserva']=='CANCELADA') { ?>

<a href="listar.php" class="btn btn-danger">

Volver atras

</a>

<?php } elseif ($reserva['estadoReserva']=='CONFIRMADA') { ?>

<a href="listar.php" class="btn btn-danger">

Volver atras

</a>

<?php } ?>
</div>

</div>

</div>

</div>

</div>

</div>

<?php
include("../../includes/footer.php");
?>

