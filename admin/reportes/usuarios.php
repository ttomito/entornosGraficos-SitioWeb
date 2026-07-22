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

FROM usuarios

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
codUsuario,
nombreUsuario,
emailUsuario,
tipoUsuario,
estadoCuenta

FROM usuarios

ORDER BY nombreUsuario

LIMIT $inicio,
$registrosPorPagina

";

$resultado = mysqli_query($link,$sql);

if(!$resultado)
{
    die("Error en la consulta: ".mysqli_error($link));
}

?>

<div class="container mt-5">

    <h2>Reporte de Usuarios</h2>

    <div class="card card-custom mt-4">

        <div class="card-body">

<?php if(mysqli_num_rows($resultado)==0){ ?>

<p class="text-muted">

No hay usuarios registrados.

</p>

<?php } else { ?>

            <table class="table table-hover">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Tipo</th>
                        <th>Estado</th>

                    </tr>

                </thead>

                <tbody>

                <?php while($fila = mysqli_fetch_assoc($resultado)){ ?>

                    <tr>

                        <td><?= $fila['codUsuario'] ?></td>
                        <td><?= $fila['nombreUsuario'] ?></td>
                        <td><?= $fila['emailUsuario'] ?></td>
                        <td><?= $fila['tipoUsuario'] ?></td>
                        <td><?= $fila['estadoCuenta'] ?></td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>
            <?php } ?>
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

        </div>

    </div>

</div>

<?php
include("../../includes/footer.php");
?>