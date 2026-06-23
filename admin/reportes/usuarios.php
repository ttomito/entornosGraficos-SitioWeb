<?php

include("../../includes/verificarSession.php");
include("../../includes/conexion.php");
include("../../includes/header.php");

$sql = "SELECT codUsuario, nombreUsuario, emailUsuario, tipoUsuario, estadoCuenta FROM usuarios ORDER BY nombreUsuario ";

$resultado = mysqli_query($link, $sql);

if (!$resultado) {
    die("Error en la consulta: " . mysqli_error($link));
}

?>

<div class="container mt-5">

    <h2>Reporte de Usuarios</h2>

    <div class="card card-custom mt-4">

        <div class="card-body">

            <table class="table table-hover">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Tipo</th>
                        <th>Estado</th>

                    </tr>

                </thead>

                <tbody>

                <?php while($fila = mysqli_fetch_assoc($resultado)){ ?>

                    <tr>

                        <td><?= $fila['codUsuario'] ?></td>
                        <td><?= $fila['nombreUsuario'] ?></td>
                        <td><?= $fila['emailUsuario'] ?></td>
                        <td><?= $fila['tipoUsuario'] ?></td>
                        <td><?= $fila['estadoCuenta'] ?></td>

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