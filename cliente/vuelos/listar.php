<?php

include("../../includes/header.php");
include("../../includes/conexion.php");

$sql = "

SELECT *

FROM vuelos

WHERE 1=1

";

if(!empty($_GET['origen']))
{
$sql .= "
AND origenVuelo
LIKE '%".$_GET['origen']."%'
";
}

if(!empty($_GET['destino']))
{
$sql .= "
AND destinoVuelo
LIKE '%".$_GET['destino']."%'
";
}

if(!empty($_GET['fecha']))
{
$sql .= "
AND fechaVuelo='".$_GET['fecha']."'
";
}

$sql .= "

ORDER BY codVuelo DESC

";

$resultado = mysqli_query($link, $sql);
?>

<div class="container mt-4">
    

    <div class="d-flex justify-content-between mb-4">

        <h2>

            Vuelos disponibles

        </h2>

        <form method="GET">

<div class="row g-3 mb-4">

<div class="col-md-3">

<input
type="text"
name="origen"
class="form-control"
placeholder="Origen"
value="<?= $_GET['origen'] ?? '' ?>">

</div>

<div class="col-md-3">

<input
type="text"
name="destino"
class="form-control"
placeholder="Destino"
value="<?= $_GET['destino'] ?? '' ?>">

</div>

<div class="col-md-3">

<input
type="date"
name="fecha"
class="form-control"
value="<?= $_GET['fecha'] ?? '' ?>">

</div>

<div class="col-md-3">

<button
class="btn btn-primary w-100">

Buscar

</button>

</div>

</div>

</form>

    </div>

    <div class="card card-custom">

        <div class="card-body">

            <table class="table table-hover">

               <thead>

<tr>

<th>Imagen</th>
<th>Origen</th>
<th>Destino</th>
<th>Fecha</th>
<th>Precio</th>
<th>Asientos</th>
<th>Acción</th>

</tr>

</thead>

<tbody>

<?php

$hoy = new DateTime();

while($fila = mysqli_fetch_assoc($resultado))
{

$fechaVuelo =
new DateTime(
$fila['fechaVuelo']
);

if(
$fila['asientosDisponibles'] > 0
&&
$fechaVuelo >= $hoy
)
{
?>

<tr>

<td>

<img
src="<?= $fila['imagenVuelo'] ?>"
style="
width:12vw;
height:7vw;
border-radius:7px">

</td>

<td>

<?= $fila['origenVuelo'] ?>

</td>

<td>

<?= $fila['destinoVuelo'] ?>

</td>

<td>

<?= $fila['fechaVuelo'] ?>

</td>

<td>

$<?= number_format(
$fila['precioVuelo'],
0,
',',
'.'
) ?>

</td>

<td>

<?= $fila['asientosDisponibles'] ?>

</td>

<td>

<?php

if(
isset($_SESSION['tipo'])
&&
$_SESSION['tipo']=='CLIENTE'
)
{
?>

<a
href="../reservas/reservar.php?codVuelo=<?= $fila['codVuelo'] ?>"
class="btn btn-success btn-sm">

Reservar

</a>

<?php
}
else
{
?>

<a
href="/entornosGraficos-SitioWeb/auth/login.php"
class="btn btn-warning btn-sm">

Iniciar sesión

</a>

<?php
}
?>

</td>

</tr>

<?php
}
}
?>

</tbody>


            </table>

        </div>

    </div>

</div>
<?php include("../../includes/footer.php"); ?>