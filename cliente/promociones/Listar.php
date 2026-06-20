<?php

include("../../includes/header.php");
include("../../includes/conexion.php");

$sql = "

SELECT
p.*,
a.nombreAerolinea

FROM promociones p

INNER JOIN aerolineas a
ON p.codAerolinea = a.codAerolinea

ORDER BY p.codPromocion DESC

";

$resultado = mysqli_query(
    $link,
    $sql
);
?>

<div class="container mt-4">

    <div class="d-flex justify-content-between mb-4">

        <h2>

            promociones disponibles

        </h2>

    </div>

    <div class="card card-custom">

        <div class="card-body">

            <table class="table table-hover">

                <thead>

                    <tr>
                        <th>Promocion</th>
                        <th>Descripcion</th>
                        <th>Fecha limite</th>
                        <th>Aerolinea</th>
                    </tr>

                </thead>

                <tbody>
                <?php $hoy = new DateTime() ?>
                <?php while($fila = mysqli_fetch_assoc($resultado)){ ?>
                    <?php 
                    $fechaLimite = new DateTime($fila['fechaLimitePromocion']);
                    if ($fila['estadoPromocion'] =="APROBADA" && $fechaLimite >= $hoy){

                    ?>
                    
                        
                        <tr>

                            <td>

                                <?= $fila['descuentoPromocion']?>%

                            </td>

                            <td>

                                <?= $fila['descripcionPromocion'] ?>

                            </td>

                            <td>

                                <?= $fila['fechaLimitePromocion'] ?>
                                
                            </td>
                            
                            <td>

                                <?= $fila['nombreAerolinea'] ?>

                            </td>

                        </tr>
                        
                    <?php } ?>
                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>
<?php include("../../includes/footer.php"); ?>