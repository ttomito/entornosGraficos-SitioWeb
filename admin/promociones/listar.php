<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

$registrosPorPagina = 10;

$pagina = isset($_GET['pagina'])
? (int)$_GET['pagina']
: 1;

if($pagina < 1)
{
    $pagina = 1;
}

$inicio =
($pagina - 1)
*
$registrosPorPagina;


/*
| Conteo
*/

$sqlConteo = "

SELECT COUNT(*) AS total

FROM promociones

";

$resultadoConteo = mysqli_query($link,$sqlConteo);

$filaConteo = mysqli_fetch_assoc($resultadoConteo);

$totalRegistros = $filaConteo['total'];

$totalPaginas = ceil(
$totalRegistros
/
$registrosPorPagina
);


/*
| Consulta principal
*/

$sql = "

SELECT
p.*,
a.nombreAerolinea

FROM promociones p

INNER JOIN aerolineas a
ON p.codAerolinea = a.codAerolinea

ORDER BY
p.estadoPromocion,
p.codPromocion DESC

LIMIT $inicio,
$registrosPorPagina

";

$resultado = mysqli_query($link,$sql);

if(!$resultado)
{
    die("Error en la consulta: ".mysqli_error($link));
}

?>

<div class="container mt-4">

<div class="d-flex justify-content-between mb-4">

    <h2>

        Aprobación de Promociones

    </h2>

</div>

<div class="card card-custom">

<div class="card-body">


<?php if(mysqli_num_rows($resultado)==0){ ?>

<p class="text-muted">

No hay promociones registradas.

</p>

<?php } else { ?>

<table class="table table-hover">

<thead>

<tr>

<th>ID</th>
<th>Aerolínea</th>
<th>Descripción</th>
<th>Descuento</th>
<th>Fecha limite</th>
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

<td><?= $fila['fechaLimitePromocion'] ?></td>

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

<div class="d-flex justify-content-center mt-4">

<nav>

<ul class="pagination">

<?php if($pagina>1){ ?>

<li class="page-item">

<a
class="page-link"
href="?pagina=<?= $pagina-1 ?>">

Anterior

</a>

</li>

<?php } ?>

<?php

for(
$i=1;
$i<=$totalPaginas;
$i++
)
{

?>

<li class="page-item <?= $i==$pagina ? 'active' : '' ?>">

<a
class="page-link"
href="?pagina=<?= $i ?>">

<?= $i ?>

</a>

</li>

<?php } ?>

<?php if($pagina<$totalPaginas){ ?>

<li class="page-item">

<a
class="page-link"
href="?pagina=<?= $pagina+1 ?>">

Siguiente

</a>

</li>

<?php } ?>

</ul>

</nav>

</div>

<?php } ?>
</div>

</div>

</div>

<?php
include("../../includes/footer.php");
?>