<?php

include("includes/conexion.php");
/*
    Estadísticas
*/


/*Destinos populares*/ 
$destinosPopulares = mysqli_query(
$link,
"

SELECT
v.destinoVuelo,
COUNT(r.codReserva) AS cantidad,
MAX(v.imagenVuelo) AS imagenVuelo

FROM reservas r

INNER JOIN vuelos v
ON r.codVuelo = v.codVuelo

WHERE r.fechaReserva >= DATE_SUB(CURDATE(), INTERVAL 3 YEAR)

GROUP BY v.destinoVuelo

ORDER BY cantidad DESC

LIMIT 8

"
);





$vuelosHome = mysqli_query(
$link,
"

SELECT *

FROM vuelos

WHERE fechaVuelo >= CURDATE()

ORDER BY fechaVuelo ASC

LIMIT 15

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
        <div class="mt-4">

    <a
    href="cliente/vuelos/listar.php"
    class="btn btn-warning btn-lg me-2">

        Buscar vuelos

    </a>

    <a
    href="cliente/promociones/listar.php"
    class="btn btn-outline-light btn-lg">

        Ver promociones

    </a>

</div>

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

<h2 class="fw-bold mb-4">

Destinos Más Populares

</h2>

<div class="netflix-wrapper">

<button
class="btn btn-dark netflix-prev"
onclick="moverDestinos(-1)">

❮

</button>

<div
id="destinosScroll"
class="d-flex netflix-scroll pb-3">


<?php

while(
$destino =
mysqli_fetch_assoc(
$destinosPopulares
))
{
?>

<div
class="card  netflix-card shadow border-0 card-hover"
>

<img
src="<?= $destino['imagenVuelo'] ?>"
style="
height:180px;
object-fit:cover;
">

<div class="card-body">

<h5>

<?= $destino['destinoVuelo'] ?>

</h5>

<p class="text-muted">

<?= $destino['cantidad'] ?>

reservas

</p>

<a
href="cliente/vuelos/listar.php?destino=<?= urlencode($destino['destinoVuelo']) ?>"
class="btn btn-primary btn-sm">

Ver vuelos

</a>

</div>

</div>

<?php
}
?>

</div>
<button
class="btn btn-dark netflix-next"
onclick="moverDestinos(1)">

❯

</button>
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
                    <a
href="cliente/vuelos/listar.php?promo=<?= $promo['codPromocion'] ?>"
class="btn btn-success mt-3">

    Ver vuelos con esta promoción

</a>

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
                    <br><br>

<a
href="cliente/novedades/listar.php"
class="btn btn-primary">

    Más información

</a>

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



<section class="container my-5">

<h2 class="fw-bold mb-4">

✈ Próximos Vuelos

</h2>

<div class="netflix-wrapper">

<button
class="btn btn-dark netflix-prev"
onclick="moverVuelos(-1)">
❮
</button>

<div
id="vuelosScroll"
class="netflix-scroll">


<?php

while(
$vuelo =
mysqli_fetch_assoc(
$vuelosHome
))
{
?>

<div
class="card  netflix-card  shadow border-0 card-hover"
>

<img
src="<?= $vuelo['imagenVuelo'] ?>"
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

<p>

📅 <?= $vuelo['fechaVuelo'] ?>

</p>

<p class="fw-bold text-success">

$

<?= number_format(
$vuelo['precioVuelo'],
0,
',',
'.'
) ?>

</p>

<?php

if(isset($_SESSION['id']))
{
?>

<a
href="cliente/reservas/reservar.php?codVuelo=<?= $vuelo['codVuelo'] ?>"
class="btn btn-warning">

Ver disponibilidad

</a>

<?php
}
else
{
?>

<a
href="auth/login.php"
class="btn btn-primary">

Iniciar sesión

</a>

<?php
}
?>

</div>

</div>

<?php
}
?>

</div>
<button
class="btn btn-dark netflix-next"
onclick="moverVuelos(1)">
❯
</button>
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

<script>

function moverDestinos(direccion)
{
document.getElementById(
'destinosScroll'
).scrollBy({
left: direccion * 300,
behavior: 'smooth'
});
}

function moverVuelos(direccion)
{
document.getElementById(
'vuelosScroll'
).scrollBy({
left: direccion * 300,
behavior: 'smooth'
});
}

</script>



<?php

include("includes/footer.php");

?>