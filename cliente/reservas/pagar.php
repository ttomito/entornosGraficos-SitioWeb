<?php

include("../../includes/verificarSession.php");
include("../../includes/header.php");
include("../../includes/conexion.php");

$codReserva = (int) ($_GET['codReserva'] ?? 0);
$idUsuario = (int) $_SESSION['id'];

$reserva = null;

if ($codReserva > 0) {

    $sql = "

    SELECT *

    FROM reservas

    WHERE codReserva = $codReserva

    AND codUsuario = $idUsuario

    ";

    $resultado = mysqli_query($link, $sql);

    if ($resultado) {
        $reserva = mysqli_fetch_assoc($resultado);
    }
}

if (!$reserva) {
?>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card card-custom">

                <div class="card-body p-5 text-center" role="alert">

                    <h2 class="text-danger">

                        Reserva no encontrada

                    </h2>

                    <p>

                        La reserva que buscás no existe o no te pertenece.

                    </p>

                    <a href="listar.php" class="btn btn-secondary">

                        <span aria-hidden="true">←</span> Volver

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

<?php

    include("../../includes/footer.php");
    exit();
}

if ($reserva['estadoReserva'] !== 'PENDIENTE') {
?>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card card-custom">

                <div class="card-body p-5 text-center" role="alert">

                    <h2 class="text-danger">

                        No se puede pagar esta reserva

                    </h2>

                    <p>

                        Su estado actual es "<?= htmlspecialchars($reserva['estadoReserva'], ENT_QUOTES, 'UTF-8') ?>".

                    </p>

                    <a href="verReserva.php?codReserva=<?= $codReserva ?>" class="btn btn-secondary">

                        <span aria-hidden="true">←</span> Volver a la reserva

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

<?php

    include("../../includes/footer.php");
    exit();
}

$codVuelo = (int) $reserva['codVuelo'];

$vuelo = null;

if ($codVuelo > 0) {

    $sqlvuelos = "

    SELECT *

    FROM vuelos

    WHERE codVuelo = $codVuelo

    ";

    $resultadoVuelo = mysqli_query($link, $sqlvuelos);

    if ($resultadoVuelo) {
        $vuelo = mysqli_fetch_assoc($resultadoVuelo);
    }
}

if (!$vuelo) {
?>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card card-custom">

                <div class="card-body p-5 text-center" role="alert">

                    <h2 class="text-danger">

                        No pudimos cargar el vuelo

                    </h2>

                    <p>

                        No encontramos el vuelo asociado a esta reserva.

                    </p>

                    <a href="verReserva.php?codReserva=<?= $codReserva ?>" class="btn btn-secondary">

                        <span aria-hidden="true">←</span> Volver a la reserva

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

<?php

    include("../../includes/footer.php");
    exit();
}

$origenOut = htmlspecialchars($vuelo['origenVuelo'], ENT_QUOTES, 'UTF-8');
$destinoOut = htmlspecialchars($vuelo['destinoVuelo'], ENT_QUOTES, 'UTF-8');
$fechaVueloOut = htmlspecialchars($vuelo['fechaVuelo'], ENT_QUOTES, 'UTF-8');

$error = isset($_GET['error']) ? htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') : null;

?>


<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-8">

<div class="card card-custom">

<div class="card-body p-5">

<h2 id="tituloPagar">Pagar reserva</h2>

<?php if ($error !== null) { ?>

<div class="alert alert-danger" role="alert">

    <?= $error ?>

</div>

<?php } ?>

<div class="mb-3">
    Origen: <?= $origenOut ?>
</div>

<div class="mb-3">
    Destino: <?= $destinoOut ?>
</div>

<div class="mb-3">
    Fecha: <time datetime="<?= $fechaVueloOut ?>"><?= $fechaVueloOut ?></time>
</div>

<div class="mb-3">
    Cantidad de asientos: <?= (int) $reserva['cantAsientos'] ?>
</div>

<div class="mb-3">
    Precio total: $<?= number_format($reserva['precioFinal'], 0, ',', '.') ?>
</div>

<form action="confirmarPago.php" method="post" aria-labelledby="tituloPagar">

    <input
        type="hidden"
        name="codReserva"
        value="<?= $codReserva ?>"
    >

    <div class="d-flex gap-2">

        <button
            type="submit"
            class="btn btn-success"
        >
            Confirmar pago
        </button>

        <a href="verReserva.php?codReserva=<?= $codReserva ?>" class="btn btn-outline-secondary">

            Cancelar

        </a>

    </div>

</form>

</div>

</div>

</div>

</div>

</div>

<?php include("../../includes/footer.php"); ?>