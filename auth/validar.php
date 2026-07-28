<?php

require_once __DIR__ . '/../includes/conexion.php';

$token   = trim($_GET['token'] ?? '');
$usuario = null;

if ($token !== '' && preg_match('/^[a-f0-9]{64}$/', $token)) {

    $stmt = mysqli_prepare(
        $link,
        'SELECT codUsuario, tipoUsuario, estadoCuenta
         FROM usuarios
         WHERE tokenValidacion = ?
         LIMIT 1'
    );
    mysqli_stmt_bind_param($stmt, 's', $token);
    mysqli_stmt_execute($stmt);
    $resultadoUsuario = mysqli_stmt_get_result($stmt);
    $usuario          = mysqli_fetch_assoc($resultadoUsuario);
    mysqli_stmt_close($stmt);
}

include("../includes/header.php");

if (!$usuario) {
?>

    <div class="container mt-5">

        <div class="row justify-content-center">

            <div class="col-md-6">

                <div class="card card-custom">

                    <div class="card-body text-center p-5" role="alert">

                        <h2 class="text-danger">

                            Link inválido

                        </h2>

                        <p>

                            Este enlace ya fue utilizado o no existe.

                        </p>

                        <a href="login.php" class="btn btn-primary">

                            Ir al Login

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

<?php

    include("../includes/footer.php");
    exit();
}

$stmt = mysqli_prepare(
    $link,
    "UPDATE usuarios
     SET estadoCuenta = 'ACTIVA', tokenValidacion = NULL
     WHERE tokenValidacion = ?"
);
mysqli_stmt_bind_param($stmt, 's', $token);
$ejecutado = mysqli_stmt_execute($stmt);
$filas     = mysqli_stmt_affected_rows($stmt);
mysqli_stmt_close($stmt);

$resultado = $ejecutado && $filas === 1;

if (!$resultado) {
    error_log('Fallo al validar la cuenta con token ' . substr($token, 0, 8) . '...');
}

?>

<div class="container my-5">

    <div class="row justify-content-center">

        <div class="col-md-6">

            <div class="card card-custom shadow-lg">

                <div class="card-body text-center p-5">

                    <?php if ($resultado) { ?>

                        <div role="status">

                            <h2 class="text-success mb-4">

                                Cuenta Validada

                            </h2>

                            <?php if ($usuario['tipoUsuario'] == 'CEO') { ?>

                                <p class="lead">

                                    Tu correo fue validado correctamente.

                                </p>

                                <p class="text-muted">

                                    Ahora un administrador debe aprobar tu solicitud.
                                    Te avisaremos cuando puedas ingresar.

                                </p>

                            <?php } else { ?>

                                <p class="lead">

                                    Tu cuenta fue activada correctamente.

                                </p>

                                <p class="text-muted">

                                    Ya podés iniciar sesión.

                                </p>

                            <?php } ?>

                        </div>

                    <?php } else { ?>

                        <div role="alert">

                            <h2 class="text-danger mb-4">

                                Ocurrió un error

                            </h2>

                            <p>

                                No pudimos procesar la validación. Intentá nuevamente
                                más tarde o contactanos si el problema persiste.

                            </p>

                        </div>

                    <?php } ?>

                    <a href="login.php" class="btn btn-primary btn-lg mt-3">

                        Ir al Login

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

<?php

include("../includes/footer.php");

?>