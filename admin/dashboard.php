<?php

include("../includes/verificarSession.php");

include("../includes/header.php");

?>

<div class="container my-5">

    <div class="text-center mb-5">

        <h1 class="fw-bold">

            Panel Administrador

        </h1>

        <p class="text-muted">

            Bienvenido <?php echo $_SESSION['nombre']; ?>

        </p>

    </div>

    <div class="row g-4">

        <div class="col-md-6">

            <div class="card dashboard-card">

                <div class="card-body">

                    <h3>✈ Aerolíneas</h3>

                    <p>
                        Gestionar aerolíneas del sistema.
                    </p>

                    <a
                    href="#"
                    class="btn btn-primary">

                        Administrar

                    </a>

                </div>

            </div>

        </div>

        <div class="col-md-6">

            <div class="card dashboard-card">

                <div class="card-body">

                    <h3>🎯 Promociones</h3>

                    <p>
                        Aprobar o denegar promociones.
                    </p>

                    <a
                    href="#"
                    class="btn btn-primary">

                        Ver promociones

                    </a>

                </div>

            </div>

        </div>

        <div class="col-md-6">

            <div class="card dashboard-card">

                <div class="card-body">

                    <h3>📢 Novedades</h3>

                    <p>
                        Crear y administrar novedades.
                    </p>

                    <a
                    href="#"
                    class="btn btn-primary">

                        Gestionar

                    </a>

                </div>

            </div>

        </div>

        <div class="col-md-6">

            <div class="card dashboard-card">

                <div class="card-body">

                    <h3>📊 Reportes</h3>

                    <p>
                        Consultar estadísticas del sistema.
                    </p>

                    <a
                    href="#"
                    class="btn btn-primary">

                        Ver reportes

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

<?php

include("../includes/footer.php");

?>