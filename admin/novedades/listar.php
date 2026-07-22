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

FROM novedades

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

SELECT *

FROM novedades

ORDER BY codNovedad DESC

LIMIT $inicio,
$registrosPorPagina

";

$resultado = mysqli_query($link,$sql);

if(!$resultado)
{
    die("Error en la consulta: ".mysqli_error($link));
}

?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<div class="container mt-4">

    <div class="d-flex justify-content-between mb-4">

        <h2>Gestión de Novedades</h2>

        <a href="crear.php" class="btn btn-success">Nueva Novedad</a>

    </div>

    <div class="card card-custom">

        <div class="card-body">

<?php if(mysqli_num_rows($resultado)==0){ ?>

<p class="text-muted">

No hay novedades registradas.

</p>

<?php } else { ?>

<table class="table table-hover">
                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Novedad</th>
                        <th>Publicación</th>
                        <th>Expiración</th>
                        <th>Acciones</th>

                    </tr>

                </thead>

                <tbody>

                <?php while($fila = mysqli_fetch_assoc($resultado)){ ?>

                    <tr>

                        <td><?= $fila['codNovedad'] ?></td>
                        <td><?= $fila['textoNovedad'] ?></td>
                        <td><?= $fila['fechaPublicacion'] ?></td>
                        <td><?= $fila['fechaExpiracion'] ?></td>
                        <td>

                            <a href="editar.php?id=<?= $fila['codNovedad'] ?>" class="btn btn-warning btn-sm">
                            Editar
                            </a>

                            <a href="eliminar.php?id=<?= $fila['codNovedad'] ?>" class="btn btn-danger btn-sm"
                            onclick="eliminarNovedad(event, this, '<?= $fila['textoNovedad'] ?>')">
                            Eliminar
                            </a>

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

$alertas = [
    'eliminada' => [
        'icon'  => 'success',
        'title' => '¡Eliminada!',
        'text'  => 'La novedad fue eliminada correctamente.'
    ],
    'actualizada' => [
        'icon'  => 'success',
        'title' => '¡Actualizada!',
        'text'  => 'La novedad fue actualizada correctamente.'
    ],
    'error_servidor' => [
        'icon'  => 'error',
        'title' => 'Error',
        'text'  => 'Ocurrió un error inesperado. Intente nuevamente.'
    ],
    'creada' => [
        'icon'  => 'success',
        'title' => '¡Creada!',
        'text'  => 'La novedad fue creada correctamente.'
    ],
    'campos_vacios' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'Los campos no pueden ser vacíos.'
    ],
    'imagen_invalida' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'Formato de la imagen inválido.'
    ],
    'imagen_muy_grande' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'La imagen puede pesar hasta 3MB.'
    ],
    'error_imagen' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'Ocurrió un error con la imagen.'
    ],
];

if (isset($_GET['alerta']) && array_key_exists($_GET['alerta'], $alertas)){
    $alerta = $alertas[$_GET['alerta']];
?>

<script>
    Swal.fire({
        icon:              '<?= $alerta['icon'] ?>',
        title:             '<?= $alerta['title'] ?>',
        text:              '<?= $alerta['text'] ?>',
        confirmButtonText: 'Aceptar'
    }).then((result) => {
        if (result.isConfirmed)
        {
            window.location.href = 'listar.php';
        }
    });
</script>
<?php }; ?>

<script>
    function eliminarNovedad(event, elemento, nombre)
    {
        event.preventDefault();

        Swal.fire({
            title:               '¿Estás seguro?',
            text:                `¿Desea eliminar la novedad "${nombre}"?`,
            icon:                'warning',
            showCancelButton:    true,
            confirmButtonColor:  '#dc3545',
            cancelButtonColor:   '#6c757d',
            confirmButtonText:   'Sí, eliminar',
            cancelButtonText:    'Cancelar'
        }).then((result) => {
            if (result.isConfirmed)
            {
                window.location.href = elemento.href;
            }
        });
    }
</script>

<?php
include("../../includes/footer.php");
?>