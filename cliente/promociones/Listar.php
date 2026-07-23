<?php

include("../../includes/header.php");
include("../../includes/conexion.php");

$registrosPorPagina = 10;

$filtroDescripcion = $_GET['descripcion'] ?? '';
$filtroAerolinea   = $_GET['aerolinea'] ?? '';

$pagina = isset($_GET['pagina']) 
? (int)$_GET['pagina'] : 
1; 

if($pagina < 1) 
{ 
    $pagina = 1; 
} 

$inicio = 
($pagina - 1) 
* 
$registrosPorPagina; 

$sqlConteo = "

SELECT COUNT(*) AS total

FROM promociones p

INNER JOIN aerolineas a
ON p.codAerolinea = a.codAerolinea

WHERE p.estadoPromocion = 'APROBADA'

AND p.fechaLimitePromocion >= CURDATE()

";

if($filtroDescripcion != '')
{
    $sqlConteo .= "
    AND p.descripcionPromocion
    LIKE '%$filtroDescripcion%'
    ";
}

if($filtroAerolinea != '')
{
    $sqlConteo .= "
    AND a.nombreAerolinea
    LIKE '%$filtroAerolinea%'
    ";
}

$resultadoConteo = mysqli_query($link,$sqlConteo); 
$filaConteo = mysqli_fetch_assoc($resultadoConteo); 
$totalRegistros = $filaConteo['total']; 
$totalPaginas = ceil( $totalRegistros / $registrosPorPagina );


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
LIMIT $inicio, $registrosPorPagina;
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
                <div class="d-flex justify-content-center mt-4">

                <nav>

                <ul class="pagination">

                <?php if($pagina > 1){ ?>

                <li class="page-item">
                <a class="page-link" href="?pagina=<?= $pagina-1 ?>&descripcion=<?= urlencode($filtroDescripcion) ?>&aerolinea=<?= urlencode($filtroAerolinea) ?>">
                Anterior
                </a>
                </li>

                <?php } ?>

                <?php
                for($i=1;$i<=$totalPaginas;$i++){
                ?>

                <li class="page-item <?= $i==$pagina ? 'active' : '' ?>">
                <a class="page-link" href="?pagina=<?= $i ?>&descripcion=<?= urlencode($filtroDescripcion) ?>&aerolinea=<?= urlencode($filtroAerolinea) ?>">
                <?= $i ?>
                </a>
                </li>

                <?php } ?>

                <?php if($pagina < $totalPaginas){ ?>

                <li class="page-item">
                <a class="page-link" href="?pagina=<?= $pagina+1 ?>&descripcion=<?= urlencode($filtroDescripcion) ?>&aerolinea=<?= urlencode($filtroAerolinea) ?>">
                Siguiente
                </a>
                </li>

                <?php } ?>

                </ul>

                </nav>

                </div>
            </div>

        </div>
    <?php } else { ?>

        <div class="alert alert-info">
            No hay promociones disponibles<?= ($filtroDescripcion || $filtroAerolinea) ? ' para los filtros aplicados' : '' ?>.
        </div>

    <?php } ?>

    

</div>
<?php include("../../includes/footer.php"); ?>