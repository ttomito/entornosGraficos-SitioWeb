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
$descuento=(($vuelo['precioVuelo']-($reserva['precioFinal']/$reserva['cantAsientos']))/$vuelo['precioVuelo'])*100;
$precioFinal = $vuelo['precioVuelo'] - ($vuelo['precioVuelo'] * $descuento / 100);
?>


<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-8">

<div class="card card-custom">

<div class="card-body p-5">

<h2>Modificar reserva</h2>

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
    Precio asientos: $<?= $vuelo['precioVuelo'] ?>
</div>

<div class="mb-3">
    Descuento: <?= $descuento ?>%
</div>
<div class="mb-3">
    Precio final:$<?= $precioFinal ?>
</div>
<div class="mb-3">
    Asientos disponibles: <?= $vuelo['asientosDisponibles'] ?>
</div>

<form action="guardarModificaciones.php" method="post">

<div class="mb-3 d-flex">

<input type="hidden" name="codReserva" value="<?=$reserva['codReserva']?>">

<label>Asiento reservados:</label>

<input
type="number"
name="cantAsientos"
class="form-control w-50"
min="1"
max="<?= $vuelo['asientosDisponibles']+$reserva['cantAsientos'] ?>"
value="<?= $reserva['cantAsientos'] ?>" 
required>
</div>
<button class="btn btn-primary">

Guardar cambios

</button>
</form>
</div>
</div>
</div>
</div>
</div>


<div>