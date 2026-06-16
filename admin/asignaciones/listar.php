<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

$sql = "

SELECT
u.*,
a.nombreAerolinea

FROM usuarios u

LEFT JOIN aerolineas a
ON u.codAerolinea = a.codAerolinea

WHERE u.tipoUsuario = 'CEO'

AND u.aprobadoAdmin = 'SI'

ORDER BY u.nombreUsuario

";

$resultado = mysqli_query($link,$sql);

?>

<div class="container mt-4">

    <div class="card card-custom">

        <div class="card-body">

            <h2>

                Asignación de Aerolíneas

            </h2>

            <table class="table table-hover">

                <thead>

                    <tr>

                        <th>CEO</th>
                        <th>Email</th>
                        <th>Aerolínea</th>
                        <th>Acción</th>

                    </tr>

                </thead>

                <tbody>

                <?php while($fila = mysqli_fetch_assoc($resultado)){ ?>

                    <tr>

                        <td><?= $fila['nombreUsuario'] ?></td>

                        <td><?= $fila['emailUsuario'] ?></td>

                        <td>

                            <?= $fila['nombreAerolinea'] ?? 'Sin asignar' ?>

                        </td>

                        <td>

                            <a
                            href="asignar.php?id=<?= $fila['codUsuario'] ?>"
                            class="btn btn-primary btn-sm">

                                Asignar

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