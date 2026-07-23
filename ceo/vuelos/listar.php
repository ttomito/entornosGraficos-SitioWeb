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

if(!$codAerolinea){ ?>

<div class="container mt-4">

    <div class="alert alert-warning" role="alert">

        <h4>Aerolínea no asignada</h4>
        <p>Un administrador todavía no le asignó una aerolínea. No puede gestionar vuelos hasta que eso ocurra.</p>

    </div>

</div>

<?php

include("../../includes/footer.php");

exit();

}
$sqlConteo = " 
SELECT COUNT(*) AS total 
FROM vuelos
WHERE codAerolinea =$codAerolinea
"; 

$resultadoConteo = mysqli_query($link,$sqlConteo); 
$filaConteo = mysqli_fetch_assoc($resultadoConteo); 
$totalRegistros = $filaConteo['total']; 
$totalPaginas = ceil( $totalRegistros / $registrosPorPagina);

$sql = "
SELECT * 
FROM vuelos 
WHERE codAerolinea = $codAerolinea 
ORDER BY fechaVuelo 
LIMIT $inicio, $registrosPorPagina";

$resultado = mysqli_query($link, $sql);
if (!$resultado) {
    die(mysqli_error($link));
}
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* Contraste corregido para SC 1.4.3 (AA) */
    .texto-secundario-accesible {
        color: #495057;
    }
    /* Utilidad estándar de Bootstrap por si el proyecto no la incluye */
    .visually-hidden {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        padding: 0 !important;
        margin: -1px !important;
        overflow: hidden !important;
        clip: rect(0, 0, 0, 0) !important;
        white-space: nowrap !important;
        border: 0 !important;
    }
    /* Foco visible reforzado para SC 2.4.7 */
    a.btn:focus-visible,
    button:focus-visible {
        outline: 3px solid #0d6efd;
        outline-offset: 2px;
    }
</style>

<div class="container mt-4">

    <div class="d-flex justify-content-between mb-4">

        <h2 id="main-heading" tabindex="-1">Gestión de Vuelos</h2>
        <a href="crear.php" class="btn btn-success">Nuevo Vuelo</a>

    </div>

    <div class="card card-custom">

    <div class="card-body">

        <?php if (mysqli_num_rows($resultado) === 0){ ?>

            <p class="texto-secundario-accesible">No hay vuelos registrados para esta aerolínea.</p>

        <?php } else { ?>

            <table class="table table-hover">

                <caption class="visually-hidden">Listado de vuelos de la aerolínea, ordenados por fecha</caption>

                <thead>

                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Origen</th>
                        <th scope="col">Destino</th>
                        <th scope="col">Fecha</th>
                        <th scope="col">Hora</th>
                        <th scope="col">Precio</th>
                        <th scope="col">Asientos</th>
                        <th scope="col">Acciones</th>
                    </tr>

                </thead>

                <tbody>

                <?php while($fila = mysqli_fetch_assoc($resultado)){

                ?>

                    <tr>
                        <td><?= $fila['codVuelo'] ?></td>
                        <td><?= $fila['origenVuelo'] ?></td>
                        <td><?= $fila['destinoVuelo'] ?></td>
                        <td><?= $fila['fechaVuelo'] ?></td>
                        <td><?= $fila['horaSalida'] ?></td>
                        <td>$<?= $fila['precioVuelo'] ?></td>
                        <td><?= $fila['asientosDisponibles'] ?></td>
                        <td>
                            <a href="editar.php?id=<?= $fila['codVuelo'] ?>"
                               class="btn btn-warning btn-sm"
                               role="button">
                                Editar
                            </a>

                            <?php if($fila['activo'] == 1) {?>
                            <a href="eliminar.php?id=<?= $fila['codVuelo'] ?>&activo=<?= $fila["activo"] ?>"
                               class="btn btn-danger btn-sm"
                               role="button"
                               onclick="ocultarVuelo(event, this)">
                                Ocultar
                            </a>
                            <?php } else { ?>
                            <a href="eliminar.php?id=<?= $fila['codVuelo'] ?>&activo=<?= $fila["activo"] ?>"
                               class="btn btn-success btn-sm"
                               role="button"
                               onclick="activarVuelo(event, this)">
                                Activar
                            </a>
                            <?php } ?>

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

        <?php } ?>

    </div>

</div>

</div>

<?php

$alertas = [
    'error_servidor' => [
        'icon'  => 'error',
        'title' => 'Error',
        'text'  => 'Ocurrió un error inesperado. Intente nuevamente.'
    ],
    'creado' => [
        'icon'  => 'success',
        'title' => '¡Creado!',
        'text'  => 'Se ha creado el vuelo.'
    ],
    'eliminado' => [
        'icon'  => 'success',
        'title' => '¡Oculto!',
        'text'  => 'Se ha ocultado el vuelo y sus reservas.'
    ],
    'campos_vacios' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'No pueden haber campos vacíos.'
    ],
    'actualizado' => [
        'icon'  => 'success',
        'title' => '¡Modificado!',
        'text'  => 'Se ha actualizado el vuelo.'
    ],
    'fecha_invalida' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'La fecha del vuelo debe ser mayor a hoy.'
    ],
    'hora_invalida' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'La hora no tiene un formato válido.'
    ],
    'precio_invalido' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'El precio debe estar entre 50 y 5.000.000.'
    ],
    'asientos_invalidos' => [
        'icon'  => 'error',
        'title' => '¡Error!',
        'text'  => 'Los asientos deben ser un número de 0 a 500.'
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
            cancelButtonText:    'Cancelar',
            focusCancel:         true
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
            cancelButtonText:    'Cancelar',
            focusCancel:         true
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