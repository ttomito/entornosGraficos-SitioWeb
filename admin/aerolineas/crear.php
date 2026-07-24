<?php

include("../../includes/verificarSession.php");
include("../../includes/header.php");

?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<div class="container mt-5">


    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card card-custom">

                <div class="card-body p-5">

                    <h2 id="titulo-form">Nueva Aerolínea</h2>

                    <p class="text-muted">
                        Los campos marcados con <span aria-hidden="true">*</span><span class="visually-hidden">(obligatorio)</span> son obligatorios.
                    </p>

                    <form action="guardar.php" method="post" aria-labelledby="titulo-form">

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
                                required
                                minlength="2"
                                maxlength="150"
                                aria-describedby="nombre-ayuda nombre-error">
                            <div id="nombre-ayuda" class="form-text">Entre 2 y 150 caracteres.</div>
                            <div id="nombre-error" class="invalid-feedback" role="alert"></div>

                        </div>

                        <div class="mb-3">

                            <label for="descripcion">Descripción</label>
                            <textarea
                                id="descripcion"
                                name="descripcion"
                                class="form-control"
                                maxlength="500"
                                aria-describedby="descripcion-ayuda descripcion-error"></textarea>
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
                                maxlength="100"
                                aria-describedby="pais-ayuda pais-error">
                            <div id="pais-ayuda" class="form-text">Opcional. Solo letras, espacios y guiones.</div>
                            <div id="pais-error" class="invalid-feedback" role="alert"></div>

                        </div>

                        <button type="submit" class="btn btn-primary" onclick="confirmarCreacion(event)">Guardar</button>

                    </form>

                </div>

            </div>

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

    // Valida cada campo apenas el usuario sale de él, para dar feedback temprano
    REGLAS_VALIDACION.forEach((regla) => {
        const campo = document.getElementById(regla.id);
        campo.addEventListener('blur', () => validarCampo(regla));
    });

    function confirmarCreacion(event) {
        event.preventDefault();

        const formulario = event.target.closest('form');

        if (!validarFormulario()) {
            return;
        }

        Swal.fire({
            title: 'Crear aerolínea',
            text: '¿Desea crear la aerolínea?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, crear',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                formulario.submit();
            }
        });
    }
</script>

<?php

$alertasCrear = [
    'campos_vacios' => [
        'icon'  => 'error',
        'title' => 'Campo obligatorio',
        'text'  => 'El nombre de la aerolínea es obligatorio.'
    ],
    'nombre_corto' => [
        'icon'  => 'error',
        'title' => 'Nombre muy corto',
        'text'  => 'El nombre debe tener al menos 2 caracteres.'
    ],
    'nombre_largo' => [
        'icon'  => 'error',
        'title' => 'Nombre muy largo',
        'text'  => 'El nombre no puede superar los 150 caracteres.'
    ],
    'descripcion_larga' => [
        'icon'  => 'error',
        'title' => 'Descripción muy larga',
        'text'  => 'La descripción no puede superar los 500 caracteres.'
    ],
    'pais_largo' => [
        'icon'  => 'error',
        'title' => 'País muy largo',
        'text'  => 'El país no puede superar los 100 caracteres.'
    ],
    'pais_invalido' => [
        'icon'  => 'error',
        'title' => 'País inválido',
        'text'  => 'El país solo puede contener letras, espacios y guiones.'
    ],
    'error_servidor' => [
        'icon'  => 'error',
        'title' => 'Error',
        'text'  => 'Ocurrió un error inesperado. Intente nuevamente.'
    ]
];

if (isset($_GET['alerta']) && array_key_exists($_GET['alerta'], $alertasCrear)) {
    $alertaCrear = $alertasCrear[$_GET['alerta']];
?>

    <script>
        Swal.fire({
            icon: '<?= $alertaCrear['icon'] ?>',
            title: '<?= $alertaCrear['title'] ?>',
            text: '<?= $alertaCrear['text'] ?>',
            confirmButtonText: 'Aceptar'
        });
    </script>
<?php } ?>

<?php
include("../../includes/footer.php");
?>