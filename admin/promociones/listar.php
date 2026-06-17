<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

$sql = "

SELECT
p.*,
a.nombreAerolinea

FROM promociones p

INNER JOIN aerolineas a
ON p.codAerolinea = a.codAerolinea

ORDER BY p.estadoPromocion,
         p.codPromocion DESC

";

$resultado = mysqli_query(
    $link,
    $sql
);

?>

<div class="container mt-4">

<div class="card card-custom">

<div class="card-body">

<h2>

Aprobación de Promociones

</h2>

<table class="table table-hover">

<thead>

<tr>

<th>ID</th>
<th>Aerolínea</th>
<th>Descripción</th>
<th>Descuento</th>
<th>Estado</th>
<th>Acciones</th>

</tr>

</thead>

<tbody>

<?php while($fila = mysqli_fetch_assoc($resultado)){ ?>

<tr>

<td><?= $fila['codPromocion'] ?></td>

<td><?= $fila['nombreAerolinea'] ?></td>

<td><?= $fila['descripcionPromocion'] ?></td>

<td><?= $fila['descuentoPromocion'] ?>%</td>

<td><?= $fila['estadoPromocion'] ?></td>

<td>

<?php
if($fila['estadoPromocion'] == 'PENDIENTE')
{
?>

<a
href="aprobar.php?id=<?= $fila['codPromocion'] ?>"
class="btn btn-success btn-sm">

Aprobar

</a>

<a
href="rechazar.php?id=<?= $fila['codPromocion'] ?>"
class="btn btn-danger btn-sm">

Rechazar

</a>

<?php
}
?>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

<?php
include("../../includes/footer.php");
?>