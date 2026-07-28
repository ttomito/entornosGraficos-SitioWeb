<?php

require_once("../../includes/verificarSession.php");

if (($_SESSION['tipo'] ?? '') !== 'CLIENTE') {
    header("Location: ".ruta."/index.php");
    exit();
}

require_once("../../includes/conexion.php");

$codVuelo  = (int) ($_GET['codVuelo'] ?? 0);
$idUsuario = (int) $_SESSION['id'];

$flash = isset($_SESSION['flash']) ? $_SESSION['flash'] : null;
unset($_SESSION['flash']);

$vuelo = null;

if ($codVuelo > 0) {

    $sql = "SELECT * FROM vuelos WHERE codVuelo = ? AND activo = 1 AND fechaVuelo >= CURDATE()";

    $stmt = mysqli_prepare($link, $sql);

    if ($stmt) {

        mysqli_stmt_bind_param($stmt, "i", $codVuelo);
        mysqli_stmt_execute($stmt);

        $resultado = mysqli_stmt_get_result($stmt);

        if ($resultado) {
            $vuelo = mysqli_fetch_assoc($resultado);
        }

        mysqli_stmt_close($stmt);
    } else {
        error_log("Reservar - vuelo: " . mysqli_error($link));
    }
}

$descuentoMaximo = 0.0;

if ($vuelo) {

    $sqlProm = "SELECT COALESCE(MAX(p.descuentoPromocion), 0) AS descuento
                FROM promociones p
                INNER JOIN promociones_destinos pd
                        ON pd.codPromocion = p.codPromocion
                WHERE p.codAerolinea = ?
                  AND p.estadoPromocion = 'APROBADA'
                  AND p.fechaLimitePromocion >= CURDATE()
                  AND LOWER(TRIM(pd.destinoVuelo)) = LOWER(TRIM(?))";

    $stmtProm = mysqli_prepare($link, $sqlProm);

    if ($stmtProm) {

        $codAerolinea = (int) $vuelo['codAerolinea'];
        $destinoVuelo = (string) $vuelo['destinoVuelo'];

        mysqli_stmt_bind_param($stmtProm, "is", $codAerolinea, $destinoVuelo);
        mysqli_stmt_execute($stmtProm);

        $filaProm = mysqli_fetch_assoc(
            mysqli_stmt_get_result($stmtProm)
        );

        $descuentoMaximo = (float) ($filaProm['descuento'] ?? 0);

        mysqli_stmt_close($stmtProm);
    } else {
        error_log("Reservar - promociones: " . mysqli_error($link));
    }
}

if (!$vuelo) {
    http_response_code(404);
}

include_once("../../includes/header.php");


if (!$vuelo) {
?>

    <main id="contenido-principal">

        <div class="container mt-5">

            <div class="row justify-content-center">

                <div class="col-md-8">

                    <div class="card card-custom">

                        <div class="card-body p-5 text-center">

                            <h2 class="text-danger">Vuelo no encontrado</h2>

                            <p>El vuelo que buscás no existe o ya no está disponible.</p>

                            <a href="../vuelos/listar.php" class="btn btn-secondary">
                                Volver al listado
                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </main>

<?php

    include_once("../../includes/footer.php");
    exit();
}

$precio      = (float) $vuelo['precioVuelo'];
$precioFinal = $precio - ($precio * $descuentoMaximo / 100);

$asientosDisponibles = (int) $vuelo['asientosDisponibles'];

$origenOut  = htmlspecialchars($vuelo['origenVuelo'], ENT_QUOTES, 'UTF-8');
$destinoOut = htmlspecialchars($vuelo['destinoVuelo'], ENT_QUOTES, 'UTF-8');

$fechaVuelo    = new DateTime($vuelo['fechaVuelo']);
$horaSalidaOut = htmlspecialchars($vuelo['horaSalida'], ENT_QUOTES, 'UTF-8');

?>

<main id="contenido-principal">

    <div class="container mt-5">

        <div class="row justify-content-center">

            <div class="col-md-8">

                <div class="card card-custom">

                    <div class="card-body p-5">

                        <h2 id="tituloReservar">Reservar Vuelo</h2>

                        <?php if ($flash !== null) { ?>
                            <div
                                id="flashReserva"
                                class="alert alert-<?= $flash['tipo'] === 'success' ? 'success' : 'danger' ?>"
                                data-tipo="<?= $flash['tipo'] === 'success' ? 'success' : 'error' ?>"
                                role="alert">
                                <?= htmlspecialchars($flash['mensaje'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        <?php } ?>

                        <dl class="mb-4">

                            <dt>Fecha de vuelo</dt>
                            <dd>
                                <time datetime="<?= $fechaVuelo->format('Y-m-d') ?>">
                                    <?= $fechaVuelo->format('d/m/Y') ?>
                                </time>
                            </dd>

                            <dt>Horario del vuelo</dt>
                            <dd>
                                <?php if ($horaSalidaOut !== '') { ?>
                                    <time datetime="<?= $horaSalidaOut ?>"><?= $horaSalidaOut ?></time>
                                <?php } else { ?>
                                    No disponible
                                <?php } ?>
                            </dd>

                            <dt>Origen</dt>
                            <dd><?= $origenOut ?></dd>

                            <dt>Destino</dt>
                            <dd><?= $destinoOut ?></dd>

                            <dt>Precio</dt>
                            <dd>$<?= number_format($precio, 0, ',', '.') ?></dd>

                            <?php if ($descuentoMaximo > 0) { ?>
                                <dt>Descuento (se aplica el mayor disponible)</dt>
                                <dd><?= number_format($descuentoMaximo, 1, ',', '.') ?>%</dd>
                            <?php } ?>

                            <dt>Precio final por asiento</dt>
                            <dd>$<?= number_format($precioFinal, 0, ',', '.') ?></dd>

                            <dt>Asientos disponibles</dt>
                            <dd><?= $asientosDisponibles ?></dd>

                            <dt>Total</dt>
                            <dd>
                                <strong id="totalReserva" data-unitario="<?= $precioFinal ?>">—</strong>
                            </dd>

                        </dl>

                        <?php if ($asientosDisponibles <= 0) { ?>

                            <div class="alert alert-warning" role="alert">
                                No quedan asientos disponibles para este vuelo.
                            </div>

                            <a href="../vuelos/listar.php" class="btn btn-secondary">
                                Volver al listado
                            </a>

                        <?php } else { ?>

                            <form action="guardar.php" method="post" aria-labelledby="tituloReservar">

                                <input type="hidden" name="codVuelo" value="<?= $codVuelo ?>">

                                <!-- guardar.php lo compara contra $_SESSION['csrf_token'] -->
                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                                <div class="mb-3">

                                    <label for="cantAsientos" class="form-label">
                                        Cantidad de asientos
                                    </label>

                                    <input
                                        type="number"
                                        id="cantAsientos"
                                        name="cantAsientos"
                                        class="form-control w-50"
                                        min="1"
                                        max="<?= $asientosDisponibles ?>"
                                        step="1"
                                        required
                                        aria-describedby="cantAsientosAyuda">

                                    <small id="cantAsientosAyuda" class="form-text text-muted">
                                        Podés reservar entre 1 y <?= $asientosDisponibles ?> asientos.
                                    </small>

                                </div>

                                <div class="d-flex flex-wrap gap-2">

                                    <button class="btn btn-primary" type="submit">
                                        Reservar
                                    </button>

                                    <a href="../vuelos/listar.php" class="btn btn-outline-secondary">
                                        Volver
                                    </a>

                                </div>

                            </form>

                        <?php } ?>

                    </div>

                </div>

            </div>

        </div>

    </div>

</main>

<script
    src="https://cdn.jsdelivr.net/npm/sweetalert2@11"
    crossorigin="anonymous"></script>

<script>
    (function() {

        // Mensaje que pudo dejar guardar.php
        var flash = document.getElementById('flashReserva');

        if (!flash || typeof Swal === 'undefined') {
            return;
        }

        var tipo = flash.getAttribute('data-tipo') === 'success' ? 'success' : 'error';
        var mensaje = flash.textContent.trim();

        flash.style.display = 'none';

        Swal.fire({
            icon: tipo,
            title: tipo === 'success' ? 'Listo' : 'No se pudo reservar',
            text: mensaje,
            confirmButtonText: 'Aceptar'
        });

    })();
</script>

<script>
    (function() {

        var campo = document.getElementById('cantAsientos');
        var total = document.getElementById('totalReserva');

        if (!campo || !total) {
            return;
        }

        var unitario = parseFloat(total.getAttribute('data-unitario'));

        if (isNaN(unitario)) {
            return;
        }

        function actualizarTotal() {

            var cantidad = parseInt(campo.value, 10);

            if (isNaN(cantidad) || cantidad < 1) {
                total.textContent = '—';
                return;
            }

            total.textContent = '$' + Math.round(unitario * cantidad).toLocaleString('es-AR');
        }

        campo.addEventListener('input', actualizarTotal);
        actualizarTotal();

    })();
</script>

<?php
include_once("../../includes/footer.php");
?>