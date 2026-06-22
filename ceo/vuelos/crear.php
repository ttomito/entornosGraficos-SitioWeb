<?php

include("../../includes/verificarSession.php");
include("../../includes/header.php");

?>

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-8">

<div class="card card-custom">

<div class="card-body p-5">

<h2>Nuevo Vuelo</h2>

<form action="guardar.php" method="post">

<div class="mb-3">

<label>Origen</label>

<input
type="text"
name="origen"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Destino</label>

<input
type="text"
name="destino"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Fecha</label>

<input
type="date"
name="fecha"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Hora</label>

<input
type="time"
name="hora"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Precio</label>

<input
type="number"
step="0.01"
name="precio"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Asientos Disponibles</label>

<input
type="number"
name="asientos"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Imagen de referencia</label>

<input
type="text"
name="imagen"
class="form-control"
required>

</div>

<button class="btn btn-primary">

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