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

$inicio = ($pagina - 1) * $registrosPorPagina;


$idCEO = (int) ($_SESSION['id'] ?? 0);

if($idCEO <= 0){
    die("Acceso denegado");
}

$sqlCEO = "
SELECT codAerolinea 
FROM usuarios 
WHERE codUsuario = $idCEO";

$resultadoCEO = mysqli_query($link, $sqlCEO);

if (!$resultadoCEO) {
    die(mysqli_error($link));
}

$ceo = mysqli_fetch_assoc($resultadoCEO);

if (!$ceo || $ceo['codAerolinea'] === null) {
?>

<main id="contenido-principal">

    <div class="container mt-5">

        <div class="row justify-content-center">

            <div class="col-md-8">

                <div class="card card-custom">

                    <div class="card-body p-5 text-center" role="alert">

                        <h2 class="text-danger">

                            Tu cuenta todavía no está vinculada a una aerolínea

                        </h2>

                        <p>

                            Un administrador tiene que asociar tu cuenta a una aerolínea antes de que puedas gestionar promociones.

                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</main>

<?php

    include("../../includes/footer.php");
    exit();
}

$codAerolinea = (int) $ceo['codAerolinea'];

$sqlConteo = " 
SELECT COUNT(*) AS total 
FROM promociones 
WHERE codAerolinea = $codAerolinea
"; 

$resultadoConteo = mysqli_query($link,$sqlConteo); 

if (!$resultadoConteo) {
    die(mysqli_error($link));
}

$filaConteo = mysqli_fetch_assoc($resultadoConteo); 
$totalRegistros = $filaConteo['total']; 
$totalPaginas = ceil( $totalRegistros / $registrosPorPagina);

$sql = "
SELECT * 
FROM promociones 
WHERE codAerolinea = $codAerolinea 
ORDER BY codPromocion DESC
LIMIT $inicio, $registrosPorPagina";

$resultado = mysqli_query($link, $sql);
if (!$resultado) {
    die(mysqli_error($link));
}

?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<main id="contenido-principal">

<div class="container mt-4">

    <div class="d-flex justify-content-between mb-4">

        <h2>Gestión de Promociones</h2>
        <a href="crear.php" class="btn btn-success">Nueva Promoción</a>

    </div>

    <?php if(mysqli_num_rows($resultado) > 0) { ?>

    <div class="card card-custom">

        <div class="card-body">

            <table class="table table-hover">

                <caption class="visually-hidden">
                    Listado de promociones de la aerolínea
                </caption>

                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Descripción</th>
                        <th scope="col">Descuento</th>
                        <th scope="col">Fecha límite</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>

                <tbody>

                <?php while($fila = mysqli_fetch_assoc($resultado)) {

                    $codPromocionInt = (int) $fila['codPromocion'];
                    $descripcionOut = htmlspecialchars($fila['descripcionPromocion'], ENT_QUOTES, 'UTF-8');
                    $fechaLimiteOut = htmlspecialchars($fila['fechaLimitePromocion'], ENT_QUOTES, 'UTF-8');
                    $estadoOut = htmlspecialchars($fila['estadoPromocion'], ENT_QUOTES, 'UTF-8');

                ?>

                    <tr>
                        <td><?= $codPromocionInt ?></td>
                        <td><?= $descripcionOut ?></td>
                        <td><?= (int) $fila['descuentoPromocion'] ?>%</td>
                        <td><time datetime="<?= $fechaLimiteOut ?>"><?= $fechaLimiteOut ?></time></td>
                        <td><?= $estadoOut ?></td>

                        <td>
                            <a href="editar.php?id=<?= $codPromocionInt ?>" class="btn btn-warning btn-sm">
                                Editar
                                <span class="visually-hidden"> promoción "<?= $descripcionOut ?>"</span>
                            </a>

                            <a href="eliminar.php?id=<?= $codPromocionInt ?>"
                            class="btn btn-danger btn-sm eliminar-promocion"
                            data-descripcion="<?= $descripcionOut ?>">
                                Eliminar
                                <span class="visually-hidden"> promoción "<?= $descripcionOut ?>"</span>
                            </a>
                        </td>
                    </tr>

                <?php } ?>

                </tbody>

            </table>
            <div class="d-flex justify-content-center mt-4">

            <nav aria-label="Paginación de promociones">

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
            <a class="page-link" href="?pagina=<?= $i ?>" <?= $i==$pagina ? 'aria-current="page"' : '' ?>>
            <?= $i ?>
            <?php if ($i == $pagina) { ?><span class="visually-hidden"> (página actual)</span><?php } ?>
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
        </div>

    </div>

    <?php } else { ?>

        <div class="alert alert-info" role="status">
            No hay promociones registradas.
        </div>

    <?php } ?>

</div>

</main>

<?php

$alertas = [
    'error_servidor' => [
        'icon'  => 'error',
        'title' => 'Error',
        'text'  => 'Ocurrió un error inesperado. Intente nuevamente.'
    ],
    'creada' => [
        'icon'  => 'success',
        'title' => '¡Creado!',
        'text'  => 'Se ha creado la promoción.'
    ],
    'eliminado' => [
        'icon'  => 'success',
        'title' => '¡Oculto!',
        'text'  => 'Se ha ocultado el vuelo y sus reservas.'
    ],
    'modificada' => [
        'icon'  => 'success',
        'title' => '¡Modificado!',
        'text'  => 'Se ha actualizado la promoción.'
    ],
    'fecha_invalida' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'La fecha límite debe ser posterior a hoy.'
    ],
    'descuento_invalido' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'El descuento debe estar entre 1% y 100%.'
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
    document.querySelectorAll('.eliminar-promocion').forEach(function (enlace) {
        enlace.addEventListener('click', function (evento) {

            if (typeof Swal === 'undefined') {
                return confirm('¿Eliminar la promoción "' + enlace.dataset.descripcion + '"?');
            }

            evento.preventDefault();

            Swal.fire({
                title:               '¿Eliminar promoción?',
                text:                '¿Desea eliminar la promoción "' + enlace.dataset.descripcion + '"? Esta acción no se puede deshacer.',
                icon:                'warning',
                showCancelButton:    true,
                confirmButtonColor:  '#dc3545',
                cancelButtonColor:   '#6c757d',
                confirmButtonText:   'Sí, eliminar',
                cancelButtonText:    'Cancelar'
            }).then((resultado) => {
                if (resultado.isConfirmed) {
                    window.location.href = enlace.href;
                }
            });
        });
    });

    function ocultarVuelo(event, elemento)
    {
        event.preventDefault();

        Swal.fire({
            title:               '¿Estás seguro?',
            text:                '¿Desea ocultar este vuelo? Al hacerlo también se desactivarán las reservas asociadas.',
            icon:                'warning',
            showCancelButton:    true,
            confirmButtonColor:  '#dc3545',
            cancelButtonColor:   '#6c757d',
            confirmButtonText:   'Sí, ocultar',
            cancelButtonText:    'Cancelar'
        }).then((result) => {
            if (result.isConfirmed){
                window.location.href = elemento.href;
            }
        });
    }

    function activarVuelo(event, elemento)
    {
        event.preventDefault();

        Swal.fire({
            title:               '¿Estás seguro?',
            text:                '¿Desea activar el vuelo? Al hacerlo también se activarán las reservas asociadas',
            icon:                'warning',
            showCancelButton:    true,
            confirmButtonColor:  '#198754',
            cancelButtonColor:   '#6c757d',
            confirmButtonText:   'Sí, activar',
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