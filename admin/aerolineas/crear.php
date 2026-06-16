<?php

include("../../includes/verificarSession.php");
include("../../includes/header.php");

?>

<div class="container mt-5">

```
<div class="row justify-content-center">

    <div class="col-md-8">

        <div class="card card-custom">

            <div class="card-body p-5">

                <h2>Nueva Aerolínea</h2>

                <form
                action="guardar.php"
                method="post">

                    <div class="mb-3">

                        <label>Nombre</label>

                        <input
                        type="text"
                        name="nombre"
                        class="form-control"
                        required>

                    </div>

                    <div class="mb-3">

                        <label>Descripción</label>

                        <textarea
                        name="descripcion"
                        class="form-control"></textarea>

                    </div>

                    <div class="mb-3">

                        <label>País</label>

                        <input
                        type="text"
                        name="pais"
                        class="form-control">

                    </div>

                    <button
                    class="btn btn-primary">

                        Guardar

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>
```

</div>

<?php
include("../../includes/footer.php");
?>
