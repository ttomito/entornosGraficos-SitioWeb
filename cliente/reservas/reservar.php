<?php

include("../../includes/verificarSession.php");
include("../../includes/header.php");

$codVuelo = $_GET['codVuelo'];


$sql = "

SELECT *

FROM vuelos

WHERE codVuelo = $codVuelo

";
$resultado = mysqli_query(
    $link, 
    $sql);
$vuelo = mysqli_fetch_assoc($resultado);
$codAerolinea=$vuelo['codAerolinea'];

$sqlProm = "

SELECT *

FROM promociones

WHERE codAerolinea = $codAerolinea

AND estadoPromocion = 'APROBADA'

";

$resultado_prom = mysqli_query(
    $link,
    $sqlProm);

$descuentoMaximo = 0;
$hoy = date("Y-m-d");
while($promocion=mysqli_fetch_assoc($resultado_prom))
    {
        if(
            $promocion['descuentoPromocion']>$descuentoMaximo 
            && 
            $promocion['fechaLimitePromocion']>=$hoy)
            {
                $descuentoMaximo = $promocion['descuentoPromocion'];
            }
    };


$idUsuario = $_SESSION['id'];
$precio= $vuelo['precioVuelo'];
$precioFinal = $precio - ($precio * $descuentoMaximo / 100);
?>

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-8">

<div class="card card-custom">

<div class="card-body p-5">

<h2>Reservar Vuelo</h2>

<?php if(isset($_GET['error'])){ ?>

<div class="alert alert-danger">

    <?= $_GET['error'] ?>

</div>

<?php } ?>

<form action="guardar.php" method="post">

<div class="mb-3">

<input type="hidden" name="codVuelo" value="<?=$codVuelo?>">
<input type="hidden" name="precio" value="<?= $precioFinal ?>">
</div>

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
    Descuentos(solo aplica mayor): <?= $descuentoMaximo ?>%
</div>
<div class="mb-3">
    Precio final por asiento: $<?= $precioFinal ?>
</div>
<div class="mb-3">
    Asientos disponibles: <?= $vuelo['asientosDisponibles'] ?>
</div>
<div class="mb-3 d-flex">
<label>Cantidad de asientos:</label>
<input
type="number"
name="cantAsientos"
class="form-control w-50"
min="1"
max="<?= $vuelo['asientosDisponibles'] ?>"
required>

</div>

<button class="btn btn-primary">

Reservar

</button>
<a href="../vuelos/listar.php" class="btn btn-danger">

Volver

</a>
</form>

</div>

</div>

</div>

</div>

</div>

<?php
include("../../includes/footer.php");
?>