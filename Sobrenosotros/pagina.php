<?php

include("../includes/conexion.php");
include("../includes/header.php");

$sql = "

SELECT *

FROM sobre_nosotros

LIMIT 1

";

$resultado =
mysqli_query(
$link,
$sql
);

$sobre =
mysqli_fetch_assoc(
$resultado
);

?>

<div class="container my-5">

<div class="text-center mb-5">

<h1 class="display-4 fw-bold">

✈ <?= $sobre['titulo'] ?>

</h1>

<p class="lead mt-3">

<?= $sobre['descripcion'] ?>

</p>

</div>

<div class="row">

<div class="col-md-6 mb-4">

<div class="card shadow-lg border-0 h-100 card-hover">

<div class="card-body p-4">

<h3 class="text-primary">

Nuestra Misión

</h3>

<p>

<?= $sobre['mision'] ?>

</p>

</div>

</div>

</div>

<div class="col-md-6 mb-4">

<div class="card shadow-lg border-0 h-100 card-hover">

<div class="card-body p-4">

<h3 class="text-success">

Nuestra Visión

</h3>

<p>

<?= $sobre['vision'] ?>

</p>

</div>

</div>

</div>

<div class="row mt-5">

<div class="col-md-4">

<div class="card shadow border-0 card-hover">

<div class="card-body text-center">

<h1>✈</h1>

<h4>Vuelos Internacionales</h4>

<p>

Conectamos destinos de todo el mundo.

</p>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card shadow border-0 card-hover">

<div class="card-body text-center">

<h1>🔒</h1>

<h4>Reservas Seguras</h4>

<p>

Protegemos toda la información de nuestros usuarios.

</p>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card shadow border-0 card-hover">

<div class="card-body text-center">

<h1>⭐</h1>

<h4>Experiencia Simple</h4>

<p>

Diseñamos una plataforma rápida e intuitiva.

</p>

</div>

</div>

</div>

</div>

</div>



<?php if(
isset($_SESSION['tipo'])
&&
$_SESSION['tipo']=='ADMIN'
){ ?>

<div class="text-center mt-4">

<a
href="editar.php"
class="btn btn-warning">

Editar contenido

</a>

</div>

<?php } ?>

</div>

<?php include("../includes/footer.php"); ?>