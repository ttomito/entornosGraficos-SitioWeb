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

                <h2>Nueva Aerolínea</h2>

                <form action="guardar.php" method="post">

                    <div class="mb-3">

                        <label>Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>

                    </div>

                    <div class="mb-3">

                        <label>Descripción</label>
                        <textarea name="descripcion" class="form-control"></textarea>

                    </div>

                    <div class="mb-3">

                        <label>País</label>
                        <input type="text" name="pais" class="form-control">

                    </div>

                    <button class="btn btn-primary" onclick="confirmarCreacion(event)">Guardar</button>

                </form>

            </div>

        </div>

    </div>

</div>

</div>

<script>
    function confirmarCreacion(event)
    {
        event.preventDefault();

        const formulario = event.target.closest('form');

        Swal.fire({
            title:               'Crear aerolínea',
            text:                '¿Desea crear la aerolínea?',
            icon:                'warning',
            showCancelButton:    true,
            confirmButtonColor:  'rgb(5, 153, 0)',
            cancelButtonColor:   '#6c757d',
            confirmButtonText:   'Sí, crear',
            cancelButtonText:    'Cancelar'
        }).then((result) => {
        if (result.isConfirmed)
        {
            formulario.submit();
        }
    });
    }
</script>

<?php
include("../../includes/footer.php");
?>
