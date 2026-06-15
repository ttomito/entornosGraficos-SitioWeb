<?php

include("../includes/verificarSession.php");

include("../includes/header.php");

?>

<div class="container mt-5">

    <h1>

        Panel Cliente

    </h1>

    <h4>

        Bienvenido
        <?php echo $_SESSION['nombre']; ?>

    </h4>

</div>

<?php

include("../includes/footer.php");

?>