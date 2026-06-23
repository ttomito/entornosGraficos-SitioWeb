<?php

include("../../includes/header.php");
include("../../includes/conexion.php");

$sql = "SELECT
p.*,
a.nombreAerolinea
FROM promociones p
INNER JOIN aerolineas a
ON p.codAerolinea = a.codAerolinea
WHERE p.estadoPromocion = 'APROBADA'
AND p.fechaLimitePromocion >= CURDATE()
ORDER BY p.codPromocion DESC";


$resultado = mysqli_query($link, $sql);

if (!$resultado) {
    die(mysqli_error($link));
}
?>

<?php

$filtroDescripcion = $_GET['descripcion'] ?? '';
$filtroAerolinea = $_GET['aerolinea'] ?? '';


if($filtroDescripcion != '')
{
    $sql .= "
    AND p.descripcionPromocion
    LIKE '%$filtroDescripcion%'
    ";
}

if($filtroAerolinea != '')
{
    $sql .= "
    AND a.nombreAerolinea
    LIKE '%$filtroAerolinea%'
    ";
}

$resultado = mysqli_query($link,$sql);

if (!$resultado) {
    die("Error en la consulta: " . mysqli_error($link));
}

?>


<div class="container mt-4">

    <div class="d-flex justify-content-between mb-4">

        <h2>Promociones disponibles</h2>

        <form method="GET" class="row mb-4">

            <div class="col-md-5">

                <input
                type="text"
                name="descripcion"
                class="form-control"
                placeholder="Buscar descripción"
                value="<?= $filtroDescripcion ?>">

            </div>

            <div class="col-md-5">

                <input
                type="text"
                name="aerolinea"
                class="form-control"
                placeholder="Buscar aerolínea"
                value="<?= $filtroAerolinea ?>">

            </div>

            <div class="col-md-2">

                <button
                class="btn btn-primary w-100">

                    Buscar

                </button>

            </div>

        </form>

    </div>

    <?php
    if(mysqli_num_rows($resultado) > 0){ ?>
        <div class="card card-custom">

            <div class="card-body">

                <table class="table table-hover">

                    <thead>

                        <tr>
                            <th>Promocion</th>
                            <th>Descripcion</th>
                            <th>Fecha limite</th>
                            <th>Aerolinea</th>
                            <th>Acción</th>
                        </tr>

                    </thead>

                    <tbody>
                    <?php $hoy = new DateTime() ?>
                    <?php while($fila = mysqli_fetch_assoc($resultado)){ ?>
                        <?php 
                        $fechaLimite = new DateTime($fila['fechaLimitePromocion']);
                        if ($fila['estadoPromocion'] =="APROBADA" && $fechaLimite >= $hoy){

                        ?>
                    
                            <tr>
                                <td>
                                    <?= $fila['descuentoPromocion']?>%
                                </td>
                                <td>
                                    <?= $fila['descripcionPromocion'] ?>
                                </td>
                                <td>
                                    <?= $fila['fechaLimitePromocion'] ?>
                                </td>
                                
                                <td>

                                    <?= $fila['nombreAerolinea'] ?>

                                </td>
                                <td>

                                    <a
                                    href="../vuelos/listar.php?promo=<?= $fila['codPromocion'] ?>"
                                    class="btn btn-primary btn-sm">

                                    Ver vuelos

                                    </a>

                                </td>

                            </tr>
                            
                        <?php } ?>
                    <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>
    <?php } else { ?>

        <div class="alert alert-info">
            No hay promociones disponibles<?= ($filtroDescripcion || $filtroAerolinea) ? ' para los filtros aplicados' : '' ?>.
        </div>

    <?php } ?>

    

</div>
<?php include("../../includes/footer.php"); ?>