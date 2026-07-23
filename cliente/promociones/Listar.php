<?php

include("../../includes/header.php");
include("../../includes/conexion.php");

$filtroDescripcion = $_GET['descripcion'] ?? '';
$filtroAerolinea   = $_GET['aerolinea'] ?? '';

$sql = "

SELECT
p.*,
a.nombreAerolinea

FROM promociones p

INNER JOIN aerolineas a
ON p.codAerolinea = a.codAerolinea

WHERE p.estadoPromocion = 'APROBADA'

AND p.fechaLimitePromocion >= CURDATE()

";

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

$sql .= "

ORDER BY p.codPromocion DESC

";
$resultado = mysqli_query($link, $sql);

if(!$resultado)
{
    die("Error en la consulta: " . mysqli_error($link));
}


?>




<div class="container mt-4">
<div class="row align-items-start mb-4">
    <div class="col-md-4">

    <h2>

        Promociones disponibles

    </h2>

</div>

    <div>

        <p class="text-muted mb-3">

            Busque promociones por descripción o por nombre de la aerolínea.

        </p>

        <form method="GET">

            <div class="row g-3">

                <div class="col-md-5">

                    <label class="form-label">

                        Descripción

                    </label>

                    <input
                    type="text"
                    name="descripcion"
                    class="form-control"
                    placeholder="Ej.: Europa, Dubái, Bariloche..."
                    value="<?= $filtroDescripcion ?>">

                </div>

                <div class="col-md-5">

                    <label class="form-label">

                        Aerolínea

                    </label>

                    <input
                    type="text"
                    name="aerolinea"
                    class="form-control"
                    placeholder="Ej.: Emirates, LATAM, Iberia..."
                    value="<?= $filtroAerolinea ?>">

                </div>

                <div class="col-md-2 d-flex align-items-end">

                    <button
                    class="btn btn-primary w-100">

                        Buscar

                    </button>

                </div>

            </div>

        </form>


</div>

        

    </div>

    <?php
if(
    !empty($filtroDescripcion)
    ||
    !empty($filtroAerolinea)
){
?>

<div class="alert alert-info d-flex justify-content-between align-items-center">

<span>

Mostrando promociones según los filtros seleccionados.

</span>

<a
href="listar.php"
class="btn btn-sm btn-outline-primary">

Limpiar filtros

</a>

</div>

<?php
}
?>

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