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


$idCEO = $_SESSION['id'];

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

$codAerolinea = $ceo['codAerolinea'];

$sqlConteo = " 
SELECT COUNT(*) AS total 
FROM promociones 
WHERE codAerolinea =$codAerolinea
"; 

$resultadoConteo = mysqli_query($link,$sqlConteo); 
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

                <?php while($fila = mysqli_fetch_assoc($resultado)) { ?>

                    <tr>
                        <td><?= $fila['codPromocion'] ?></td>
                        <td><?= $fila['descripcionPromocion'] ?></td>
                        <td><?= $fila['descuentoPromocion'] ?>%</td>
                        <td><?= $fila['fechaLimitePromocion'] ?></td>
                        <td><?= $fila['estadoPromocion'] ?></td>

                        <td>
                            <a href="editar.php?id=<?= $fila['codPromocion'] ?>" class="btn btn-warning btn-sm">
                                Editar
                                <span class="visually-hidden"> promoción "<?= $fila['descripcionPromocion'] ?>"</span>
                            </a>

                            <a href="eliminar.php?id=<?= $fila['codPromocion'] ?>"
                            class="btn btn-danger btn-sm"
                            onclick="return confirm('¿Eliminar la promoción &quot;<?= $fila['descripcionPromocion'] ?>&quot;?')">
                                Eliminar
                                <span class="visually-hidden"> promoción "<?= $fila['descripcionPromocion'] ?>"</span>
                            </a>
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
        </div>

    </div>

    <?php } else { ?>

        <div class="alert alert-info">
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