<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$idCEO = (int)$_SESSION['id'];

if ($id <= 0) {
    header("Location: listar.php");
    exit();
}

$sql = "SELECT v.*
FROM vuelos v
INNER JOIN usuarios u
ON v.codAerolinea = u.codAerolinea
WHERE v.codVuelo = ?
AND u.codUsuario = ?";

$stmt = mysqli_prepare($link, $sql);

if (!$stmt) {
    error_log("Error al preparar la consulta: " . mysqli_error($link));
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

mysqli_stmt_bind_param($stmt, "ii", $id, $idCEO);
mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);
$vuelo = $resultado ? mysqli_fetch_assoc($resultado) : null;
mysqli_stmt_close($stmt);

if (!$vuelo) {
    header("Location: listar.php?alerta=acceso_denegado");
    exit();
}

$origenEscapado = htmlspecialchars($vuelo['origenVuelo'], ENT_QUOTES, 'UTF-8');
$destinoEscapado = htmlspecialchars($vuelo['destinoVuelo'], ENT_QUOTES, 'UTF-8');
$fechaEscapada = htmlspecialchars($vuelo['fechaVuelo'], ENT_QUOTES, 'UTF-8');
$horaEscapada = htmlspecialchars($vuelo['horaSalida'], ENT_QUOTES, 'UTF-8');
$precioEscapado = htmlspecialchars($vuelo['precioVuelo'], ENT_QUOTES, 'UTF-8');
$asientosEscapados = htmlspecialchars($vuelo['asientosDisponibles'], ENT_QUOTES, 'UTF-8');
$imagenEscapada = htmlspecialchars($vuelo['imagenVuelo'] ?? '', ENT_QUOTES, 'UTF-8');

$fechaMinima = date('Y-m-d', strtotime('+1 day'));

?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card card-custom">

                <div class="card-body p-5">

                    <h2>Editar Vuelo</h2>
                    <p class="text-muted" style="font-size: 0.9rem;">Los campos marcados con <span aria-hidden="true">*</span> son obligatorios.</p>

                    <form action="actualizar.php" method="post" enctype="multipart/form-data">

                        <input type="hidden" name="id" value="<?= (int)$vuelo['codVuelo'] ?>">
                        <input type="hidden" name="imagenActual" value="<?= $imagenEscapada ?>">

                        <div class="mb-3">
                            <label for="origen">Origen <span aria-hidden="true">*</span></label>
                            <input type="text" id="origen" name="origen" class="form-control" maxlength="50" minlength="3" pattern="[A-Za-zÀ-ÿ\s]+" value="<?= $origenEscapado ?>" required aria-required="true" aria-describedby="origenAyuda">
                            <small id="origenAyuda" class="form-text text-muted">Solo letras y espacios, entre 3 y 50 caracteres.</small>
                        </div>

                        <div class="mb-3">
                            <label for="destino">Destino <span aria-hidden="true">*</span></label>
                            <input type="text" id="destino" name="destino" class="form-control" maxlength="50" minlength="3" pattern="[A-Za-zÀ-ÿ\s]+" value="<?= $destinoEscapado ?>" required aria-required="true" aria-describedby="destinoAyuda">
                            <small id="destinoAyuda" class="form-text text-muted">Solo letras y espacios, entre 3 y 50 caracteres.</small>
                        </div>

                        <div class="mb-3">
                            <label for="fecha">Fecha <span aria-hidden="true">*</span></label>
                            <input type="date" id="fecha" name="fecha" class="form-control" min="<?= $fechaMinima ?>" value="<?= $fechaEscapada ?>" required aria-required="true" aria-describedby="fechaAyuda">
                            <small id="fechaAyuda" class="form-text text-muted">Debe ser posterior a hoy.</small>
                        </div>

                        <div class="mb-3">
                            <label for="hora">Hora <span aria-hidden="true">*</span></label>
                            <input type="time" id="hora" name="hora" class="form-control" value="<?= $horaEscapada ?>" required aria-required="true">
                        </div>

                        <div class="mb-3">
                            <label for="precio">Precio <span aria-hidden="true">*</span></label>
                            <input type="number" id="precio" step="0.01" min="50" max="5000000" name="precio" class="form-control" value="<?= $precioEscapado ?>" required aria-required="true" aria-describedby="precioAyuda">
                            <small id="precioAyuda" class="form-text text-muted">Valor entre 50 y 5.000.000.</small>
                        </div>

                        <div class="mb-3">
                            <label for="asientos">Asientos Disponibles <span aria-hidden="true">*</span></label>
                            <input type="number" id="asientos" name="asientos" class="form-control" min="0" max="500" step="1" value="<?= $asientosEscapados ?>" required aria-required="true" aria-describedby="asientosAyuda">
                            <small id="asientosAyuda" class="form-text text-muted">Número entero entre 0 y 500.</small>
                        </div>

                        <div class="mb-3">

                            <label for="imagen">Imagen de referencia</label>

                            <?php if (!empty($imagenEscapada)): ?>
                                <div class="mb-2" id="contenedorImagenActual">
                                    <p class="text-muted mb-1" style="font-size: 0.85rem;">Imagen actual:</p>
                                    <img src="../../uploads/vuelos/<?= $imagenEscapada ?>" alt="Imagen actual del vuelo cargada previamente" style="max-height: 150px; border-radius: 6px;">
                                </div>
                            <?php endif; ?>

                            <input type="file" id="imagen" name="imagen" class="form-control" accept="image/png, image/jpeg, image/webp" aria-describedby="imagenAyuda">
                            <small id="imagenAyuda" class="form-text text-muted">Formatos permitidos: PNG, JPG, JPEG o WEBP. Seleccioná una nueva para reemplazar la actual.</small>

                            <div class="mt-2 d-none" id="contenedorVistaPrevia">
                                <p class="text-muted mb-1" style="font-size: 0.85rem;">Nueva imagen seleccionada (se guardará al actualizar):</p>
                                <img id="vistaPreviaImagen" src="" alt="Vista previa de la nueva imagen seleccionada" style="max-height: 150px; border-radius: 6px;">
                            </div>

                        </div>

                        <button class="btn btn-primary" onclick="modificarVuelo(event)">Actualizar</button>
                        <a href="listar.php" class="btn btn-secondary">Cancelar</a>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<script>
    const inputImagen = document.getElementById('imagen');
    const contenedorVistaPrevia = document.getElementById('contenedorVistaPrevia');
    const vistaPreviaImagen = document.getElementById('vistaPreviaImagen');

    inputImagen.addEventListener('change', function() {
        const archivo = this.files && this.files[0];

        if (!archivo) {
            contenedorVistaPrevia.classList.add('d-none');
            vistaPreviaImagen.src = '';
            return;
        }

        const lector = new FileReader();

        lector.onload = function(evento) {
            vistaPreviaImagen.src = evento.target.result;
            contenedorVistaPrevia.classList.remove('d-none');
        };

        lector.readAsDataURL(archivo);
    });

    function modificarVuelo(event) {
        event.preventDefault();

        const formulario = event.target.closest('form');

        Swal.fire({
            title: '¿Estás seguro?',
            text: '¿Desea modificar este vuelo?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, modificar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                formulario.submit();
            }
        });
    }
</script>

<?php
$alertasEditarVuelo = [
    'campos_vacios' => [
        'icon'  => 'error',
        'title' => 'Campos incompletos',
        'text'  => 'Todos los campos son obligatorios.'
    ],
    'origen_invalido' => [
        'icon'  => 'error',
        'title' => 'Origen inválido',
        'text'  => 'El origen debe tener entre 3 y 50 caracteres, solo letras y espacios.'
    ],
    'destino_invalido' => [
        'icon'  => 'error',
        'title' => 'Destino inválido',
        'text'  => 'El destino debe tener entre 3 y 50 caracteres, solo letras y espacios.'
    ],
    'fecha_invalida' => [
        'icon'  => 'error',
        'title' => 'Fecha inválida',
        'text'  => 'La fecha debe ser posterior a hoy.'
    ],
    'hora_invalida' => [
        'icon'  => 'error',
        'title' => 'Hora inválida',
        'text'  => 'Ingresá una hora válida.'
    ],
    'precio_invalido' => [
        'icon'  => 'error',
        'title' => 'Precio inválido',
        'text'  => 'El precio debe estar entre 50 y 5.000.000.'
    ],
    'asientos_invalidos' => [
        'icon'  => 'error',
        'title' => 'Asientos inválidos',
        'text'  => 'La cantidad de asientos debe ser un número entero entre 0 y 500.'
    ],
    'imagen_muy_grande' => [
        'icon'  => 'error',
        'title' => 'Imagen muy grande',
        'text'  => 'La imagen no puede superar los 3MB.'
    ],
    'imagen_invalida' => [
        'icon'  => 'error',
        'title' => 'Imagen inválida',
        'text'  => 'El archivo debe ser una imagen PNG, JPG, JPEG o WEBP.'
    ],
    'error_imagen' => [
        'icon'  => 'error',
        'title' => 'Error',
        'text'  => 'No se pudo guardar la imagen. Intente nuevamente.'
    ]
];

if (isset($_GET['alerta']) && array_key_exists($_GET['alerta'], $alertasEditarVuelo)) {
    $alertaEditarVuelo = $alertasEditarVuelo[$_GET['alerta']];
?>

    <script>
        Swal.fire({
            icon: '<?= $alertaEditarVuelo['icon'] ?>',
            title: '<?= $alertaEditarVuelo['title'] ?>',
            text: '<?= $alertaEditarVuelo['text'] ?>',
            confirmButtonText: 'Aceptar'
        });
    </script>
<?php } ?>

<?php
include("../../includes/footer.php");
?>