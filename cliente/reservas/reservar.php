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
$idUsuario = $_SESSION['id'];
$hoy = new DateTime();
$precio= $vuelo['precioVuelo'];
?>

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-8">

<div class="card card-custom">

<div class="card-body p-5">

<h2>Reservar Vuelo</h2>

<form action="guardar.php" method="post">

<div class="mb-3">

<input type="hidden" name="codVuelo" value="<?=$codVuelo?>">
<input type="hidden" name="precio" value="<?=$precio?>">
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
    Precio final: <?= $vuelo['precioVuelo'] ?>
</div>
<div class="mb-3"></div>
<button class="btn btn-primary">

Guardar Reserva

</button>

</form>

</div>

</div>

</div>

</div>

</div>

<?php
include("../../includes/footer.php");
?>