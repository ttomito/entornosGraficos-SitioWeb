<?php

include("../../includes/header.php");
include("../../includes/conexion.php");

$idCliente = $_SESSION['id'];

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

WHERE codUsuario = $idCliente

";

$resultadoConteo =
mysqli_query($link,$sqlConteo);

$filaConteo =
mysqli_fetch_assoc($resultadoConteo);

$totalRegistros =
$filaConteo['total'];

$totalPaginas =
ceil(
$totalRegistros
/
$registrosPorPagina
);

$sql = "

SELECT *

FROM reservas

$sql = "SELECT * FROM reservas
WHERE codUsuario = $idCliente

ORDER BY codReserva DESC

LIMIT $inicio,
$registrosPorPagina

";

$resultado =
mysqli_query(
    $link,
    $sql
);

?>

<div class="container mt-4">

    <div class="d-flex justify-content-between mb-4">

        <h2>

            Historial de reservas

        </h2>

    </div>

    <?php if (mysqli_num_rows($resultado) === 0) { ?>

        <div class="alert alert-info" role="status">
            Todavía no tenés reservas.
        </div>

    <?php } else { ?>

        <div class="card card-custom">

            <div class="card-body">

           <?php if(mysqli_num_rows($resultado) == 0){ ?>

    <p class="text-muted">

        No posee reservas registradas.

    </p>

<?php } else { ?>

    <table class="table table-hover">

                    <table class="table table-hover">

                        <caption class="visually-hidden">Historial de reservas del usuario</caption>

                        <thead>

                            <tr>

                                <th scope="col">Imagen</th>
                                <th scope="col">Asientos</th>
                                <th scope="col">Origen</th>
                                <th scope="col">Destino</th>
                                <th scope="col">Fecha vuelo</th>
                                <th scope="col">Fecha reserva</th>
                                <th scope="col">Precio Final</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Acción</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php while ($fila = mysqli_fetch_assoc($resultado)) { ?>

                                <?php

                                $codVueloInt = (int) $fila['codVuelo'];

                                $sqlVuelo = "

                SELECT *

                FROM vuelos

                WHERE codVuelo = $codVueloInt

                ";

                                $resultadoVuelo = mysqli_query(
                                    $link,
                                    $sqlVuelo
                                );

                                $vuelo = mysqli_fetch_assoc(
                                    $resultadoVuelo
                                );

                                // Por si el vuelo fue eliminado pero la reserva sigue existiendo
                                $origenOut = $vuelo ? htmlspecialchars($vuelo['origenVuelo'], ENT_QUOTES, 'UTF-8') : 'No disponible';
                                $destinoOut = $vuelo ? htmlspecialchars($vuelo['destinoVuelo'], ENT_QUOTES, 'UTF-8') : 'No disponible';
                                $fechaVueloOut = $vuelo ? htmlspecialchars($vuelo['fechaVuelo'], ENT_QUOTES, 'UTF-8') : '';
                                $imagenOut = $vuelo ? htmlspecialchars($vuelo['imagenVuelo'], ENT_QUOTES, 'UTF-8') : '';

                                $fechaReservaOut = htmlspecialchars($fila['fechaReserva'], ENT_QUOTES, 'UTF-8');
                                $codReservaInt = (int) $fila['codReserva'];

                                ?>

                                <tr>

                                    <td>

                                        <?php if ($vuelo) { ?>
                                            <img
                                                src="../../uploads/vuelos/<?= $imagenOut ?>"
                                                alt="Vuelo de <?= $origenOut ?> a <?= $destinoOut ?>"
                                                style="
                            width:120px;
                            height:80px;
                            border-radius:7px;
                            object-fit:cover;
                            ">
                                        <?php } ?>

                                    </td>

                                    <td>

                                        <?= (int) $fila['cantAsientos'] ?>

                                    </td>

                                    <td>

                                        <?= $origenOut ?>

                                    </td>

                                    <td>

                                        <?= $destinoOut ?>

                   <td>

<?= date(
"d/m/Y",
strtotime($vuelo['fechaVuelo'])
) ?>

</td>

<td>

<?= date(
"d/m/Y",
strtotime($fila['fechaReserva'])
) ?>

</td>

                                    </td>

                                    <td>

                                        $<?= number_format(
                                                $fila['precioFinal'],
                                                0,
                                                ',',
                                                '.'
                                            ) ?>

                                    </td>

                                    <td>

                                        <?php

                                        if (
                                            $fila['estadoReserva']
                                            ==
                                            'CONFIRMADA'
                                        ) {
                                            echo
                                            '<span class="badge bg-success">Confirmada</span>';
                                        } elseif (
                                            $fila['estadoReserva']
                                            ==
                                            'PENDIENTE'
                                        ) {
                                            echo
                                            '<span class="badge bg-warning text-dark">Pendiente</span>';
                                        } else {
                                            echo
                                            '<span class="badge bg-danger">Cancelada</span>';
                                        }

                                        ?>

                                    </td>

                                    <td>

                                        <a
                                            href="verReserva.php?codReserva=<?= $codReservaInt ?>"
                                            class="btn btn-primary btn-sm">

                                            Seguir solicitud
                                            <span class="visually-hidden"> del vuelo <?= $origenOut ?> a <?= $destinoOut ?>, reservado el <?= $fechaReservaOut ?></span>

                                        </a>

                                    </td>

                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>

                </div>

            </div>

            <div class="d-flex justify-content-center mt-4">

<nav>

<ul class="pagination">

<?php if($pagina > 1){ ?>

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

<?php if($pagina < $totalPaginas){ ?>

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

    <?php } ?>

</div>

<?php include("../../includes/footer.php"); ?>