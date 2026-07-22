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

FROM reservas

WHERE estadoReserva = 'CONFIRMADA'

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
r.codReserva,
u.nombreUsuario,
a.nombreAerolinea,
v.origenVuelo,
v.destinoVuelo,
r.fechaReserva,
r.precioFinal

FROM reservas r

INNER JOIN usuarios u
ON r.codUsuario = u.codUsuario

INNER JOIN vuelos v
ON r.codVuelo = v.codVuelo

INNER JOIN aerolineas a
ON v.codAerolinea = a.codAerolinea

WHERE r.estadoReserva = 'CONFIRMADA'

ORDER BY r.fechaReserva DESC

LIMIT $inicio,
$registrosPorPagina

";

$resultado = mysqli_query($link,$sql);

if(!$resultado)
{
    die("Error en la consulta: ".mysqli_error($link));
}

$sqlTotal = " SELECT SUM(precioFinal) total FROM reservas WHERE estadoReserva = 'CONFIRMADA'";
$totalVentas = mysqli_fetch_assoc(mysqli_query($link, $sqlTotal));

?>


<div class="container mt-5">

    <h2>Reporte de Ventas</h2>

    <div class="card card-custom mt-4">

        <div class="card-body">

            <div class="alert alert-info">

                <strong>Total vendido:</strong>
                $<?= $totalVentas['total'] ?? 0 ?>

            </div>

<?php if(mysqli_num_rows($resultado)==0){ ?>

<p class="text-muted">

No hay ventas registradas.

</p>

<?php } else { ?>

<table class="table table-hover">
                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Aerolínea</th>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Fecha</th>
                        <th>Importe</th>

                    </tr>

                </thead>

                <tbody>

                    <?php while ($fila = mysqli_fetch_assoc($resultado)) { ?>

                        <tr>

                            <td><?= $fila['codReserva'] ?></td>
                            <td><?= $fila['nombreUsuario'] ?></td>
                            <td><?= $fila['nombreAerolinea'] ?></td>
                            <td><?= $fila['origenVuelo'] ?></td>
                            <td><?= $fila['destinoVuelo'] ?></td>
                            <td><?= $fila['fechaReserva'] ?></td>
                            <td>$<?= $fila['precioFinal'] ?></td>

                        </tr>

                    <?php } ?>

                </tbody>

            </table>

            <?php } ?>

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

<li
class="page-item
<?= $i==$pagina ? 'active' : '' ?>">

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

        </div>

    </div>

</div>

<?php
include("../../includes/footer.php");
?>