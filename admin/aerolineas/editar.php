<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: listar.php");
    exit();
}

$sql = "SELECT * FROM aerolineas WHERE codAerolinea = ?";
$stmt = mysqli_prepare($link, $sql);

if (!$stmt) {
    error_log("Error al preparar la consulta: " . mysqli_error($link));
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);

if (!$resultado) {
    error_log("Error al obtener aerolínea: " . mysqli_error($link));
    mysqli_stmt_close($stmt);
    header("Location: listar.php?alerta=error_servidor");
    exit();
}

$aerolinea = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);

if (!$aerolinea) {
    header("Location: listar.php?alerta=no_encontrada");
    exit();
}

$nombreEscapado = htmlspecialchars($aerolinea['nombreAerolinea'], ENT_QUOTES, 'UTF-8');
$descripcionEscapada = htmlspecialchars($aerolinea['descripcionAerolinea'], ENT_QUOTES, 'UTF-8');
$paisEscapado = htmlspecialchars($aerolinea['codPais'], ENT_QUOTES, 'UTF-8');

?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container mt-4">

    <div class="card card-custom">

        <div class="card-body">

            <h2 class="mb-4" id="titulo-form">Editar Aerolínea</h2>

            <p class="text-muted">
                Los campos marcados con <span aria-hidden="true">*</span><span class="visually-hidden">(obligatorio)</span> son obligatorios.
            </p>

            <form action="actualizar.php" method="post" aria-labelledby="titulo-form">

                <input type="hidden" name="id" value="<?= (int)$aerolinea['codAerolinea'] ?>">

                <div class="mb-3">

                    <label for="nombre">
                        Nombre
                        <span aria-hidden="true">*</span>
                        <span class="visually-hidden">(obligatorio)</span>
                    </label>
                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        class="form-control"
                        value="<?= $nombreEscapado ?>"
                        required
                        minlength="2"
                        maxlength="100"
                        aria-describedby="nombre-ayuda nombre-error">
                    <div id="nombre-ayuda" class="form-text">Entre 2 y 100 caracteres.</div>
                    <div id="nombre-error" class="invalid-feedback" role="alert"></div>

                </div>

                <div class="mb-3">

                    <label for="descripcion">Descripción</label>
                    <textarea
                        id="descripcion"
                        name="descripcion"
                        class="form-control"
                        maxlength="500"
                        aria-describedby="descripcion-ayuda descripcion-error"><?= $descripcionEscapada ?></textarea>
                    <div id="descripcion-ayuda" class="form-text">Opcional, hasta 500 caracteres.</div>
                    <div id="descripcion-error" class="invalid-feedback" role="alert"></div>

                </div>

                <div class="mb-3">

                    <label for="pais">País</label>
                    <input
                        type="text"
                        id="pais"
                        name="pais"
                        class="form-control"
                        value="<?= $paisEscapado ?>"
                        maxlength="100"
                        aria-describedby="pais-ayuda pais-error">
                    <div id="pais-ayuda" class="form-text">Opcional. Solo letras, espacios y guiones.</div>
                    <div id="pais-error" class="invalid-feedback" role="alert"></div>

                </div>

                <button type="submit" class="btn btn-primary" onclick="confirmarActualizacion(event)">Actualizar</button>

                <a href="listar.php" class="btn btn-secondary">Cancelar</a>

            </form>

        </div>

    </div>

</div>

<script>
    const REGLAS_VALIDACION = [{
            id: 'nombre',
            requerido: true,
            minLength: 2,
            maxLength: 150,
            mensajeVacio: 'Ingresá el nombre de la aerolínea.',
            mensajeCorto: 'El nombre debe tener al menos 2 caracteres.',
            mensajeLargo: 'El nombre no puede superar los 150 caracteres.'
        },
        {
            id: 'descripcion',
            requerido: false,
            maxLength: 500,
            mensajeLargo: 'La descripción no puede superar los 500 caracteres.'
        },
        {
            id: 'pais',
            requerido: false,
            maxLength: 100,
            patron: /^[A-Za-zÀ-ÖØ-öø-ÿ\s'-]+$/,
            mensajePatron: 'El país solo puede contener letras, espacios y guiones.',
            mensajeLargo: 'El país no puede superar los 100 caracteres.'
        }
    ];

    function mostrarError(campo, mensaje) {
        campo.classList.add('is-invalid');
        campo.setAttribute('aria-invalid', 'true');

        const elementoError = document.getElementById(campo.id + '-error');
        if (elementoError) {
            elementoError.textContent = mensaje;
        }
    }

    function ocultarError(campo) {
        campo.classList.remove('is-invalid');
        campo.removeAttribute('aria-invalid');

        const elementoError = document.getElementById(campo.id + '-error');
        if (elementoError) {
            elementoError.textContent = '';
        }
    }

    function validarCampo(regla) {
        const campo = document.getElementById(regla.id);
        const valor = campo.value.trim();

        if (regla.requerido && valor === '') {
            mostrarError(campo, regla.mensajeVacio);
            return false;
        }

        if (valor !== '' && regla.minLength && valor.length < regla.minLength) {
            mostrarError(campo, regla.mensajeCorto);
            return false;
        }

        if (regla.maxLength && valor.length > regla.maxLength) {
            mostrarError(campo, regla.mensajeLargo);
            return false;
        }

        if (valor !== '' && regla.patron && !regla.patron.test(valor)) {
            mostrarError(campo, regla.mensajePatron);
            return false;
        }

        ocultarError(campo);
        return true;
    }

    function validarFormulario() {
        let esValido = true;
        let primerCampoInvalido = null;

        REGLAS_VALIDACION.forEach((regla) => {
            const campoValido = validarCampo(regla);
            if (!campoValido) {
                esValido = false;
                if (!primerCampoInvalido) {
                    primerCampoInvalido = document.getElementById(regla.id);
                }
            }
        });

        if (primerCampoInvalido) {
            primerCampoInvalido.focus();
        }

        return esValido;
    }

    REGLAS_VALIDACION.forEach((regla) => {
        const campo = document.getElementById(regla.id);
        campo.addEventListener('blur', () => validarCampo(regla));
    });

    function confirmarActualizacion(event) {
        event.preventDefault();

        const formulario = event.target.closest('form');

        if (!validarFormulario()) {
            return;
        }

        const nombreActual = document.getElementById('nombre').value.trim();

        Swal.fire({
            title: '¿Estás seguro?',
            text: `¿Desea modificar la aerolínea "${nombreActual}"?`,
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