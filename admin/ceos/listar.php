<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

$sql = "

SELECT *

FROM usuarios

WHERE tipoUsuario = 'CEO'

ORDER BY estadoCuenta,
         nombreUsuario

";

$resultado = mysqli_query(
    $link,
    $sql
);

?>

<div class="container mt-4">

    <div class="card card-custom">

        <div class="card-body">

            <h2 class="mb-4">

                Gestión de CEOs

            </h2>

            <table class="table table-hover">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Estado</th>
                        <th>Acciones</th>

                    </tr>

                </thead>

                <tbody>

                <?php while($fila = mysqli_fetch_assoc($resultado)){ ?>

                    <tr>

                        <td>

                            <?= $fila['codUsuario'] ?>

                        </td>

                        <td>

                            <?= $fila['nombreUsuario'] ?>

                        </td>

                        <td>

                            <?= $fila['emailUsuario'] ?>

                        </td>

                        <td>

                            <?= $fila['estadoCuenta'] ?>

                        </td>

                        <td>

                        <?php
                        if($fila['estadoCuenta'] == 'PENDIENTE')
                        {
                        ?>

                            <a
                            href="aprobar.php?id=<?= $fila['codUsuario'] ?>"
                            class="btn btn-success btn-sm">

                                Aprobar

                            </a>

                            <a
                            href="rechazar.php?id=<?= $fila['codUsuario'] ?>"
                            class="btn btn-danger btn-sm">

                                Rechazar

                            </a>

                        <?php
                        }
                        ?>

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