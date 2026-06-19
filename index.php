<?php

include("includes/conexion.php");
/*
    Estadísticas
*/


$vuelosHome = mysqli_query(

    $link,

    "

    SELECT *

    FROM vuelos

    ORDER BY fechaVuelo ASC

    LIMIT 6

    "

);
$totalAerolineas = mysqli_fetch_assoc(
    mysqli_query(
        $link,
        "SELECT COUNT(*) total FROM aerolineas"
    )
);

$totalVuelos = mysqli_fetch_assoc(
    mysqli_query(
        $link,
        "SELECT COUNT(*) total FROM vuelos"
    )
);

$totalUsuarios = mysqli_fetch_assoc(
    mysqli_query(
        $link,
        "SELECT COUNT(*) total FROM usuarios"
    )
);

/*
    Promociones
*/

$promociones = mysqli_query(
    $link,
    "
    SELECT *
    FROM promociones
    WHERE estadoPromocion='APROBADA'
    LIMIT 3
    "
);

/*
    Novedades
*/

$novedades = mysqli_query(
    $link,
    "
    SELECT *
    FROM novedades
    WHERE CURDATE()
    BETWEEN fechaPublicacion
    AND fechaExpiracion
    LIMIT 3
    "
);
?>


<?php

include("includes/header.php");

?>

<section class="py-5 text-center text-white"
style="
background:
linear-gradient(rgba(0,0,0,.55),rgba(0,0,0,.55)),
url('https://images.unsplash.com/photo-1436491865332-7a61a109cc05');
background-size:cover;
background-position:center;
min-height:500px;
display:flex;
align-items:center;
">

    <div class="container">

        <h1 class="display-3 fw-bold">

            Encontrá tu próximo destino

        </h1>

        <p class="lead mt-3">

            Reservá vuelos nacionales e internacionales
            de forma rápida, segura y sencilla.

        </p>

    </div>

</section>
<section class="container my-5">

    <div class="row text-center">

        <div class="col-md-4 mb-3">

<div class="card shadow-sm card-hover estadistica-card">
                <div class="card-body">

                    <h2 class="text-primary">

                        <?= $totalAerolineas['total'] ?>

                    </h2>

                    <h5>Aerolíneas</h5>

                </div>

            </div>

        </div>

        <div class="col-md-4 mb-3">

<div class="card shadow-sm card-hover estadistica-card">
                <div class="card-body">

                    <h2 class="text-success">

                        <?= $totalVuelos['total'] ?>

                    </h2>

                    <h5>Vuelos</h5>

                </div>

            </div>

        </div>

        <div class="col-md-4 mb-3">

<div class="card shadow-sm card-hover estadistica-card">
                <div class="card-body">

                    <h2 class="text-warning">

                        <?= $totalUsuarios['total'] ?>

                    </h2>

                    <h5>Usuarios</h5>

                </div>

            </div>

        </div>

    </div>

</section>
    <section class="container my-5">

    <h2 class="text-center mb-4">

        Promociones Destacadas

    </h2>

    <div class="row">

        <?php while($promo = mysqli_fetch_assoc($promociones)){ ?>

        <div class="col-md-4 mb-4">

<div class="card h-100 shadow-lg card-hover border-0">
                <div class="card-body">

                    <span class="badge bg-success">

                        Promoción

                    </span>

                    <h5 class="mt-3">

                        <?= $promo['descripcionPromocion'] ?>

                    </h5>

                    <h3 class="text-danger">

                        <?= $promo['descuentoPromocion'] ?>% OFF

                    </h3>

                </div>

            </div>

        </div>

        <?php } ?>

    </div>

</section>


<section class="container my-5">

    <h2 class="text-center mb-5 fw-bold">

        Últimas Novedades

    </h2>

    <div class="row">

        <?php

        while($novedad = mysqli_fetch_assoc($novedades))
        {
        ?>

        <div class="col-md-4 mb-4">

           <div
class="card border-0 shadow-lg h-100 card-hover">

                <div class="card-body p-4">

                    <span
                    class="badge bg-primary mb-3">

                        Novedad

                    </span>

                    <p class="lead">

                        <?= $novedad['textoNovedad'] ?>

                    </p>

                    <hr>

                    <small class="text-muted">

                        Vigente hasta:

                        <?= $novedad['fechaExpiracion'] ?>

                    </small>

                </div>

            </div>

        </div>

        <?php
        }
        ?>

    </div>

</section>
<section
class="py-4 bg-dark text-white">

<div class="container text-center">

<h3>

✈ Más de 50 destinos disponibles

</h3>

<p>

Promociones exclusivas,
vuelos nacionales e internacionales.

</p>

</div>

</section>
<section class="py-5 bg-light">

    <div class="container">

        <h2
        class="text-center fw-bold mb-5">

            ¿Cómo funciona?

        </h2>

        <div class="row">

            <div class="col-md-4 mb-4">

              <div
class="card shadow-lg border-0 h-100 card-hover">

                    <div
                    class="card-body text-center p-5">

                        <div class="display-1">

                            🔎

                        </div>

                        <h3
                        class="text-primary mt-3">

                            Buscar

                        </h3>

                        <p>

                            Encontrá vuelos según
                            origen, destino y fecha.

                        </p>

                    </div>

                </div>

            </div>

            <div class="col-md-4 mb-4">

               <div
class="card shadow-lg border-0 h-100 card-hover">

                    <div
                    class="card-body text-center p-5">

                        <div class="display-1">

                            ✈️

                        </div>

                        <h3
                        class="text-success mt-3">

                            Reservar

                        </h3>

                        <p>

                            Elegí el vuelo ideal
                            para tu viaje.

                        </p>

                    </div>

                </div>

            </div>

            <div class="col-md-4 mb-4">

                 <div
class="card shadow-lg border-0 h-100 card-hover">

                    <div
                    class="card-body text-center p-5">

                        <div class="display-1">

                            ✅

                        </div>

                        <h3
                        class="text-warning mt-3">

                            Confirmar

                        </h3>

                        <p>

                            Gestioná tus compras
                            y reservas fácilmente.

                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>
<section
class="py-5"
style="
background:#f8f9fa;
">

<div class="container-fluid">

    <h2
    class="text-center fw-bold mb-5">

        Próximos Vuelos

    </h2>

    <div class="row">

<?php

$i = 1;

while(
$vuelo =
mysqli_fetch_assoc($vuelosHome)
)
{
?>

<div
class="col-lg-2 col-md-4 col-sm-6 mb-4">

<div
class="card card-hover shadow border-0 h-100">

<img
src="<?= $vuelo['imagenVuelo'] ?>"
class="card-img-top"
style="
height:200px;
object-fit:cover;
">

<div class="card-body">

<h5>

<?= $vuelo['origenVuelo'] ?>

→

<?= $vuelo['destinoVuelo'] ?>

</h5>

<h4 class="text-success">

$

<?= number_format(
$vuelo['precioVuelo'],
0,
',',
'.'
) ?>

</h4>

<button
class="btn btn-outline-primary"
data-bs-toggle="collapse"
data-bs-target="#v<?= $i ?>">

Ver detalle

</button>

<div
id="v<?= $i ?>"
class="collapse mt-3">

<p>

Fecha:
<?= $vuelo['fechaVuelo'] ?>

</p>

<p>

Hora:
<?= $vuelo['horaSalida'] ?>

</p>

<p>

Asientos:
<?= $vuelo['asientosDisponibles'] ?>

</p>

<a
href="cliente/vuelos/listar.php"
class="btn btn-warning">

Reservar

</a>

</div>

</div>

</div>

</div>

<?php

$i++;

}

?>

</div>

</div>

</section>

<?php
if(!isset($_SESSION['id']))
{
?>

<section class="container my-5">

 <div
class="card border-0 shadow-lg card-hover"
style="
background:
linear-gradient(
135deg,
#0d6efd,
#0a3d91
);
color:white;
border-radius:20px;
">

        <div
        class="card-body p-5 text-center 
        ">

            <h2 class="fw-bold">

                ¿Listo para comenzar tu viaje?

            </h2>

           <p class="lead text-white">

                Registrate gratis y comenzá a reservar
                vuelos nacionales e internacionales.

            </p>

            <a
            href="auth/registro.php"
            class="btn btn-warning btn-lg ">

                Crear Cuenta

            </a>

        </div>

    </div>

</section>



<?php
}
?>

<?php

include("includes/footer.php");

?>