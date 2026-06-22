<?php

include("../../includes/header.php");
include("../../includes/conexion.php");

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
| CONSULTA PRINCIPAL
*/

$sql = "

SELECT
v.*,
COALESCE(
MAX(
CASE
WHEN p.estadoPromocion='APROBADA'
AND p.fechaLimitePromocion >= CURDATE()
THEN p.descuentoPromocion
ELSE 0
END
),
0
) AS descuento

FROM vuelos v

LEFT JOIN promociones p
ON v.codAerolinea = p.codAerolinea

WHERE 1=1

";

if(!empty($_GET['promo']))
{
$sql .= "

AND v.codAerolinea =
(
    SELECT codAerolinea

    FROM promociones

    WHERE codPromocion =
    ".$_GET['promo']."
)

";
}

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


/*
| CONTEO PARA PAGINACIÓN
*/

$sqlConteo = "

SELECT COUNT(*) AS total

FROM vuelos v

WHERE 1=1

";

if(!empty($_GET['promo']))
{
$sqlConteo .= "

AND v.codAerolinea =
(
    SELECT codAerolinea

    FROM promociones

    WHERE codPromocion =
    ".$_GET['promo']."
)

";
}

if(!empty($_GET['origen']))
{
$sqlConteo .= "
AND origenVuelo
LIKE '%".$_GET['origen']."%'
";
}

if(!empty($_GET['destino']))
{
$sqlConteo .= "
AND destinoVuelo
LIKE '%".$_GET['destino']."%'
";
}

if(!empty($_GET['fecha']))
{
$sqlConteo .= "
AND fechaVuelo='".$_GET['fecha']."'
";
}

$resultadoConteo =
mysqli_query(
$link,
$sqlConteo
);

$filaConteo =
mysqli_fetch_assoc(
$resultadoConteo
);

$totalRegistros =
$filaConteo['total'];

$totalPaginas =
ceil(
$totalRegistros
/
$registrosPorPagina
);


/*
| PAGINACIÓN
*/



$queryString = '';

if(!empty($_GET['origen']))
{
    $queryString .= '&origen=' . $_GET['origen'];
}

if(!empty($_GET['destino']))
{
    $queryString .= '&destino=' . $_GET['destino'];
}

if(!empty($_GET['fecha']))
{
    $queryString .= '&fecha=' . $_GET['fecha'];
}

if(!empty($_GET['promo']))
{
    $queryString .= '&promo=' . $_GET['promo'];
}




$sql .= "

GROUP BY v.codVuelo

ORDER BY v.codVuelo DESC

LIMIT $inicio,
$registrosPorPagina

";



$resultado = mysqli_query($link, $sql);

if(!$resultado)
{
    die(mysqli_error($link));
}
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

    <?php

if(isset($_GET['promo']))
{
?>

<div
class="alert alert-info">

Mostrando vuelos asociados a la promoción seleccionada.

<a
href="listar.php"
class="btn btn-sm btn-outline-primary ms-3">

Quitar filtro

</a>

</div>

<?php
}
?>

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
<th>Promoción</th>
<th>Asientos</th>
<th>Acción</th>

</tr>

</thead>

<tbody>

<?php

$hoy = new DateTime();

while($fila = mysqli_fetch_assoc($resultado))
{
    $fechaVuelo = new DateTime($fila['fechaVuelo']);

    if(
        $fila['asientosDisponibles'] > 0
        &&
        $fechaVuelo >= $hoy
    )
    {

        $precioFinal = $fila['precioVuelo'];

        if($fila['descuento'] > 0)
        {
            $precioFinal =
            $fila['precioVuelo']
            -
            (
                $fila['precioVuelo']
                *
                $fila['descuento']
                / 100
            );
        }
?>

<tr>

    <td>

        <img
        src="<?= $fila['imagenVuelo'] ?>"
        style="
        width:12vw;
        height:7vw;
        object-fit:cover;
        border-radius:7px;">

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

        <?php if($fila['descuento'] > 0){ ?>

            <span class="text-danger text-decoration-line-through">

                $<?= number_format(
                    $fila['precioVuelo'],
                    0,
                    ',',
                    '.'
                ) ?>

            </span>

            <br>

            <span class="fw-bold text-success">

                $<?= number_format(
                    $precioFinal,
                    0,
                    ',',
                    '.'
                ) ?>

            </span>

        <?php } else { ?>

            $<?= number_format(
                $fila['precioVuelo'],
                0,
                ',',
                '.'
            ) ?>

        <?php } ?>

    </td>

    <td>

        <?php if($fila['descuento'] > 0){ ?>

            <span class="badge bg-danger">

                🔥 <?= $fila['descuento'] ?>% OFF

            </span>

        <?php } else { ?>

            <span class="badge bg-secondary">

                Sin promo

            </span>

        <?php } ?>

    </td>

    <td>

        <?= $fila['asientosDisponibles'] ?>

    </td>

    <td>

        <?php

if(
    isset($_SESSION['tipo'])
    &&
    $_SESSION['tipo'] == 'CLIENTE'
)
{

$idUsuario = $_SESSION['id'];

$sqlReserva = "

SELECT *

FROM reservas

WHERE codUsuario = $idUsuario

AND codVuelo = {$fila['codVuelo']}

AND estadoReserva != 'CANCELADA'

LIMIT 1

";

$resultadoReserva =
mysqli_query(
$link,
$sqlReserva
);

if(
mysqli_num_rows(
$resultadoReserva
) > 0
)
{
?>

<a
href="../reservas/listar.php"
class="btn btn-primary btn-sm">

Ver reservas

</a>

<?php
}
else
{
?>

<a
href="../reservas/reservar.php?codVuelo=<?= $fila['codVuelo'] ?>"
class="btn btn-success btn-sm">

Reservar

</a>

<?php
}

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
href="?pagina=<?= $i ?><?= $queryString ?>">
<?= $i ?>

</a>

</li>

<?php
}
?>

<?php if($pagina < $totalPaginas){ ?>

<li class="page-item">

<a
class="page-link"
href="?pagina=<?= $pagina+1 ?><?= $queryString ?>">
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
<?php include("../../includes/footer.php"); ?>

