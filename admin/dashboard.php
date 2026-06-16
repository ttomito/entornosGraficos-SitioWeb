<?php

include("../includes/verificarSession.php");

include("../includes/header.php");

?>

<div class="container-fluid">

    <div class="row">

        <!-- SIDEBAR -->

        <div class="col-md-3 col-lg-2 sidebar-admin">

            <h4 class="text-center mb-4">

                Administrador

            </h4>

            <a href="../admin/dashboard.php">

                Dashboard

            </a>

            <a href="../admin/aerolineas/listar.php">

                Aerolíneas

            </a>

            <a href="#">

                Promociones

            </a>

            <a href="#">

                Novedades

            </a>

            <a href="#">

                Reportes

            </a>

        </div>

        <!-- CONTENIDO -->

        <div class="col-md-9 col-lg-10 p-4">

            <h2>

                Bienvenido
                <?php echo $_SESSION['nombre']; ?>
            </h2>

            <p class="text-muted">

                Panel de administración del sistema.

            </p>

            <div class="row mt-4">

                <div class="col-md-4 mb-4">

                    <div class="card dashboard-card">

                        <div class="card-body">

                            <h5>

                                Aerolíneas

                            </h5>

                            <h2>

                                0

                            </h2>

                        </div>

                    </div>

                </div>

                <div class="col-md-4 mb-4">

                    <div class="card dashboard-card">

                        <div class="card-body">

                            <h5>

                                Promociones

                            </h5>

                            <h2>

                                0

                            </h2>

                        </div>

                    </div>

                </div>

                <div class="col-md-4 mb-4">

                    <div class="card dashboard-card">

                        <div class="card-body">

                            <h5>

                                Usuarios

                            </h5>

                            <h2>

                                0

                            </h2>

                        </div>

                    </div>

                </div>

            </div>

            <div class="card card-custom mt-3">

                <div class="card-body">

                    <h4>

                        Accesos rápidos

                    </h4>

                    <hr>

                    <a
                    href="../admin/aerolineas/listar.php"
                    class="btn btn-primary">

                        Gestionar Aerolíneas

                    </a>

                    <a
                    href="#"
                    class="btn btn-warning">

                        Aprobar CEOs

                    </a>

                    <a
                    href="#"
                    class="btn btn-success">

                        Novedades

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

<?php

include("../includes/footer.php");

?>