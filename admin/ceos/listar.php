<?php

include("../../includes/conexion.php");
include("../../includes/verificarSession.php");
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

FROM usuarios

WHERE tipoUsuario = 'CEO'

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


/*
| Consulta principal
*/

$sql = "

SELECT *

FROM usuarios

WHERE tipoUsuario = 'CEO'

ORDER BY
estadoCuenta,
nombreUsuario

LIMIT $inicio,
$registrosPorPagina

";

$resultado =
mysqli_query($link,$sql);

if(!$resultado)
{
    die("Error en la consulta: ".mysqli_error($link));
}

?>

<div class="container mt-4">
<div class="d-flex justify-content-between mb-4">

    <h2>

        Gestión de CEOs

    </h2>

</div>


    <div class="card card-custom">

        <div class="card-body">

        <?php if(mysqli_num_rows($resultado)==0){ ?>

<p class="text-muted">

No hay CEOs registrados.

</p>

<?php } else { ?>

            <table class="table table-hover">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Estado</th>
                        <th>Acciones</th>

                    </tr>

                </thead>

                <tbody>

                <?php while($fila = mysqli_fetch_assoc($resultado)){ ?>

                    <tr>

                        <td>

                            <?= $fila['codUsuario'] ?>

                        </td>

                        <td>

                            <?= $fila['nombreUsuario'] ?>

                        </td>

                        <td>

                            <?= $fila['emailUsuario'] ?>

                        </td>

                        <td>

                            <?= $fila['estadoCuenta'] ?>

                        </td>

                        <td>
<?php

if($fila['estadoCuenta'] == 'PENDIENTE')
{
?>

<a
href="aprobar.php?id=<?= $fila['codUsuario'] ?>"
class="btn btn-success btn-sm">

    Aprobar

</a>

<a
href="rechazar.php?id=<?= $fila['codUsuario'] ?>"
class="btn btn-danger btn-sm">

    Rechazar

</a>

<?php
}
else
{
?>

<span class="text-muted">

Sin acciones

</span>

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

    </div>

</div>

<?php
include("../../includes/footer.php");
?>