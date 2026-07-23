<?php

include("../includes/conexion.php");
include("../includes/header.php");

$sql="SELECT * FROM sobre_nosotros LIMIT 1";

$resultado=mysqli_query($link,$sql);

$sobre=mysqli_fetch_assoc($resultado);

?>

<div class="container mt-5">

<div class="card shadow">

<div class="card-body">

<h2>Editar Sobre Nosotros</h2>

<form
action="guardar.php"
method="post">

<input
type="hidden"
name="codSobre"
value="<?= $sobre['codSobre'] ?>">

<div class="mb-3">

<label>Título</label>

<input
type="text"
name="titulo"
class="form-control"
value="<?= $sobre['titulo'] ?>">

</div>

<div class="mb-3">

<label>Descripción</label>

<textarea
name="descripcion"
class="form-control"
rows="4"><?= $sobre['descripcion'] ?></textarea>

</div>

<div class="mb-3">

<label>Misión</label>

<textarea
name="mision"
class="form-control"
rows="4"><?= $sobre['mision'] ?></textarea>

</div>

<div class="mb-3">

<label>Visión</label>

<textarea
name="vision"
class="form-control"
rows="4"><?= $sobre['vision'] ?></textarea>

</div>

<button
class="btn btn-success">

Guardar cambios

</button>

</form>

</div>

</div>

</div>