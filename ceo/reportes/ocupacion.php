<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

$registrosPorPagina = 10;

$pagina = isset($_GET['pagina'])
? (int)$_GET['pagina']
: 1;

if($pagina < 1){
    $pagina = 1;
}

$inicio = ($pagina - 1) * $registrosPorPagina;

$idCEO = $_SESSION['id'];

$sqlConteo = "
SELECT COUNT(*) AS total
FROM vuelos
WHERE codAerolinea = (
    SELECT codAerolinea
    FROM usuarios
    WHERE codUsuario = $idCEO
)";

$resultadoConteo = mysqli_query($link, $sqlConteo);

$filaConteo = mysqli_fetch_assoc($resultadoConteo);

$totalRegistros = $filaConteo['total'];

$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

$sql = "SELECT
v.codVuelo,
v.origenVuelo,
v.destinoVuelo,
v.fechaVuelo,
v.asientosDisponibles,

COALESCE(SUM(r.cantAsientos),0) AS ocupados

FROM vuelos v
LEFT JOIN reservas r
ON v.codVuelo = r.codVuelo
AND r.estadoReserva = 'CONFIRMADA'
WHERE v.codAerolinea = (SELECT codAerolinea FROM usuarios WHERE codUsuario = $idCEO)
GROUP BY v.codVuelo 
ORDER BY v.fechaVuelo
LIMIT $inicio, $registrosPorPagina";

$resultado = mysqli_query($link, $sql);

if(!$resultado){
    die(mysqli_error($link));
}

?>

<div class="container mt-5">

    <h2>Ocupación de Vuelos</h2>

    <div class="card card-custom">

        <div class="card-body">
        <?php if(mysqli_num_rows($resultado) > 0){ ?>

            <table class="table table-hover">

                <thead>

                    <tr>

                        <th>Vuelo</th>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Fecha</th>
                        <th>Reservas</th>
                        <th>Asientos Disponibles</th>

                    </tr>

                </thead>

                <tbody>

                <?php while($fila = mysqli_fetch_assoc($resultado)){ ?>

                    <tr>
                        <td>
                            <?= $fila['codVuelo'] ?>
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

                            <?= $fila['ocupados'] ?>

                        </td>
                        <td>
                            <?= $fila['asientosDisponibles'] ?>
                        </td>
                    </tr>

                <?php } ?>

                </tbody>

            </table>
            <div class="d-flex justify-content-center mt-4">

            <nav>

            <ul class="pagination">

            <?php if($pagina > 1){ ?>

            <li class="page-item">
            <a class="page-link" href="?pagina=<?= $pagina-1 ?>">
            Anterior
            </a>
            </li>

            <?php } ?>

            <?php
            for($i=1;$i<=$totalPaginas;$i++){
            ?>

            <li class="page-item <?= $i==$pagina ? 'active' : '' ?>">
            <a class="page-link" href="?pagina=<?= $i ?>">
            <?= $i ?>
            </a>
            </li>

            <?php } ?>

            <?php if($pagina < $totalPaginas){ ?>

            <li class="page-item">
            <a class="page-link" href="?pagina=<?= $pagina+1 ?>">
            Siguiente
            </a>
            </li>

            <?php } ?>

            </ul>

            </nav>

            </div>
        <?php } else { ?>

        <div class="alert alert-info">
            No hay vuelos registrados.
        <?php } ?>
        </div>
        </div>

    </div>

</div>

<?php
include("../../includes/footer.php");
?>