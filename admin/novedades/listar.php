<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

$sql = "
SELECT *
FROM novedades
ORDER BY codNovedad DESC
";

$resultado = mysqli_query($link,$sql);

?>

<div class="container mt-4">

    <div class="d-flex justify-content-between mb-4">

        <h2>Gestión de Novedades</h2>

        <a
        href="crear.php"
        class="btn btn-success">

            Nueva Novedad

        </a>

    </div>

    <div class="card card-custom">

        <div class="card-body">

            <table class="table table-hover">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Novedad</th>
                        <th>Publicación</th>
                        <th>Expiración</th>
                        <th>Acciones</th>

                    </tr>

                </thead>

                <tbody>

                <?php while($fila = mysqli_fetch_assoc($resultado)){ ?>

                    <tr>

                        <td><?= $fila['codNovedad'] ?></td>

                        <td><?= $fila['textoNovedad'] ?></td>

                        <td><?= $fila['fechaPublicacion'] ?></td>

                        <td><?= $fila['fechaExpiracion'] ?></td>

                        <td>

                            <a
                            href="editar.php?id=<?= $fila['codNovedad'] ?>"
                            class="btn btn-warning btn-sm">

                                Editar

                            </a>

                            <a
                            href="eliminar.php?id=<?= $fila['codNovedad'] ?>"
                            class="btn btn-danger btn-sm"
                            onclick="return confirm('¿Eliminar novedad?');">

                                Eliminar

                            </a>

                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php
include("../../includes/footer.php");
?>