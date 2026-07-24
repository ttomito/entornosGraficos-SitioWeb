<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

$id    = (int) ($_GET['id'] ?? 0);
$idCEO = (int) ($_SESSION['id'] ?? 0);

$sql = "SELECT p.* FROM promociones p
INNER JOIN usuarios u
ON p.codAerolinea = u.codAerolinea
WHERE p.codPromocion = $id
AND u.codUsuario = $idCEO";

$resultado = mysqli_query($link, $sql);
$promocion = mysqli_fetch_assoc($resultado);

if (!$promocion) {
    die("Acceso denegado");
}

$destinosExistentes = [];
$sqlDestinos = "SELECT destinoVuelo FROM promocionesDestinos WHERE codPromocion = $id ORDER BY destinoVuelo";
$resultadoDestinos = mysqli_query($link, $sqlDestinos);

if ($resultadoDestinos) {
    while ($filaDestino = mysqli_fetch_assoc($resultadoDestinos)) {
        $destinosExistentes[] = $filaDestino['destinoVuelo'];
    }
}

$descripcionOut  = htmlspecialchars($promocion['descripcionPromocion'], ENT_QUOTES, 'UTF-8');
$fechaLimiteOut  = htmlspecialchars($promocion['fechaLimitePromocion'], ENT_QUOTES, 'UTF-8');

?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<main id="contenido-principal">

    <div class="container mt-5">

        <div class="row justify-content-center">

            <div class="col-md-8">

                <div class="card card-custom">

                    <div class="card-body p-5">

                        <h2>Editar Promoción</h2>

                        <p class="text-body-secondary">
                            Los campos marcados con
                            <span aria-hidden="true">*</span>
                            <span class="visually-hidden">(obligatorio)</span>
                            son obligatorios.
                        </p>

                        <form action="actualizar.php" method="post">

                            <input type="hidden" name="id" value="<?= (int) $promocion['codPromocion'] ?>">

                            <div class="mb-3">

                                <label for="descripcion">
                                    Descripción
                                    <span aria-hidden="true">*</span>
                                </label>

                                <textarea
                                    id="descripcion"
                                    name="descripcion"
                                    class="form-control"
                                    maxlength="200"
                                    aria-describedby="ayudaDescripcion"
                                    required
                                    aria-required="true"><?= $descripcionOut ?></textarea>

                                <div id="ayudaDescripcion" class="form-text">
                                    200 caracteres de máximo.
                                </div>

                            </div>

                            <div class="mb-3">

                                <label for="destinoInput">
                                    Destino(s) del vuelo
                                    <span aria-hidden="true">*</span>
                                </label>

                                <div class="d-flex gap-2">

                                    <input type="text"
                                        id="destinoInput"
                                        class="form-control"
                                        maxlength="100"
                                        aria-describedby="ayudaDestino">

                                    <button type="button" id="btnAgregarDestino" class="btn btn-outline-primary text-nowrap">
                                        Agregar destino
                                    </button>

                                </div>

                                <div id="ayudaDestino" class="form-text">
                                    Escribí un destino y presioná "Agregar destino" (o Enter). Podés sumar varios: la promoción se aplicará a los vuelos de tu aerolínea que tengan cualquiera de estos destinos.
                                </div>

                                <ul id="listaDestinos" class="list-group mt-2" aria-live="polite"></ul>

                                <div id="destinosOcultos"></div>

                                <div id="errorDestinos" class="text-danger form-text mt-1" role="alert" hidden>
                                    Agregá al menos un destino antes de guardar.
                                </div>

                            </div>

                            <div class="mb-3">

                                <label for="descuento">
                                    Descuento %
                                    <span aria-hidden="true">*</span>
                                </label>

                                <input type="number"
                                    id="descuento"
                                    name="descuento"
                                    class="form-control"
                                    value="<?= (int) $promocion['descuentoPromocion'] ?>"
                                    min="1"
                                    max="100"
                                    aria-describedby="ayudaDescuento"
                                    required
                                    aria-required="true">

                                <div id="ayudaDescuento" class="form-text">
                                    Ingresá un valor entero entre 1 y 100.
                                </div>

                            </div>

                            <div class="mb-3">

                                <label for="fechaLimite">
                                    Fecha límite
                                    <span aria-hidden="true">*</span>
                                </label>

                                <input type="date"
                                    id="fechaLimite"
                                    name="fechaLimite"
                                    class="form-control"
                                    value="<?= $fechaLimiteOut ?>"
                                    min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                                    aria-describedby="ayudaFecha"
                                    required
                                    aria-required="true">

                                <div id="ayudaFecha" class="form-text">
                                    Debe ser una fecha posterior a hoy.
                                </div>

                            </div>

                            <button class="btn btn-primary" onclick="modificarPromocion(event)">Actualizar</button>
                            <a href="listar.php" class="btn btn-secondary">Cancelar</a>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</main>

<script>
    (function() {

        const input = document.getElementById('destinoInput');
        const btnAgregar = document.getElementById('btnAgregarDestino');
        const lista = document.getElementById('listaDestinos');
        const destinosOcultos = document.getElementById('destinosOcultos');
        const errorDestinos = document.getElementById('errorDestinos');
        const form = input.closest('form');

        const destinos = <?= json_encode($destinosExistentes, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;

        function normalizar(texto) {
            return texto.trim().replace(/\s+/g, ' ');
        }

        function renderizarDestinos() {

            lista.innerHTML = '';
            destinosOcultos.innerHTML = '';

            destinos.forEach(function(destino, indice) {

                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.textContent = destino;

                const btnQuitar = document.createElement('button');
                btnQuitar.type = 'button';
                btnQuitar.className = 'btn btn-sm btn-outline-danger';
                btnQuitar.textContent = 'Quitar';
                btnQuitar.setAttribute('aria-label', 'Quitar destino ' + destino);

                btnQuitar.addEventListener('click', function() {
                    destinos.splice(indice, 1);
                    renderizarDestinos();
                    input.focus();
                });

                li.appendChild(btnQuitar);
                lista.appendChild(li);

                const oculto = document.createElement('input');
                oculto.type = 'hidden';
                oculto.name = 'destinos[]';
                oculto.value = destino;
                destinosOcultos.appendChild(oculto);

            });

        }

        function agregarDestino() {

            const valor = normalizar(input.value);

            if (valor === '') {
                return;
            }

            const yaExiste = destinos.some(function(d) {
                return d.toLowerCase() === valor.toLowerCase();
            });

            if (!yaExiste) {
                destinos.push(valor);
                renderizarDestinos();
                errorDestinos.hidden = true;
            }

            input.value = '';
            input.focus();

        }

        btnAgregar.addEventListener('click', agregarDestino);

        input.addEventListener('keydown', function(evento) {
            if (evento.key === 'Enter') {
                evento.preventDefault();
                agregarDestino();
            }
        });

        form.addEventListener('submit', function(evento) {
            if (destinos.length === 0) {
                evento.preventDefault();
                errorDestinos.hidden = false;
                input.focus();
            }
        });

        renderizarDestinos();

    })();

    function modificarPromocion(event) {
        event.preventDefault();

        const formulario = event.target.closest('form');

        Swal.fire({
            title: '¿Estás seguro?',
            text: '¿Desea modificar esta promoción?',
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
include("../../includes/footer.php");
?>