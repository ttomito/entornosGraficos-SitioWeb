<?php

include("../../includes/verificarSession.php");
include("../../includes/header.php");

?>

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-8">

<div class="card card-custom">

<div class="card-body p-5">

<h2>Nueva Promoción</h2>

<form
action="guardar.php"
method="post">

<div class="mb-3">

<label>

Descripción

</label>

<textarea
name="descripcion"
class="form-control"
required>

</textarea>

</div>

<div class="mb-3">

<label>

Descuento %

</label>

<input
type="number"
name="descuento"
class="form-control"
required>

</div>

<label>

Fecha limite

</label>

<input
type="date"
name="fechaLimite"
class="form-control"
required>

<button
class="btn btn-primary mt-3">

Guardar

</button>

</form>

</div>

</div>

</div>

</div>

</div>

<?php
include("../../includes/footer.php");
?>