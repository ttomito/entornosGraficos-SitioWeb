<?php

include("includes/header.php");

?>

<section class="hero">

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-lg-10 text-center">

                <h1 class="hero-title">

                    Encontrá tu próximo vuelo

                </h1>

                <p class="hero-subtitle">

                    Reservá vuelos nacionales e internacionales
                    al mejor precio.

                </p>

              <div class="search-box mt-4">

    <div class="row g-3">

        <div class="col-md-3">

            <input
                type="text"
                class="form-control"
                placeholder="Origen">

        </div>

        <div class="col-md-3">

            <input
                type="text"
                class="form-control"
                placeholder="Destino">

        </div>

        <div class="col-md-3">

            <input
                type="date"
                class="form-control">

        </div>

        <div class="col-md-3">

            <button
                class="btn btn-primary w-100">

                Buscar vuelo

            </button>

        </div>

    </div>

</div>

            </div>

        </div>

    </div>

</section>

<section class="container my-5">

    <h2 class="text-center mb-4">

        Destinos destacados

    </h2>

    <div class="row">

        <div class="col-md-4">

            <div class="card card-custom">

                <img
                    src="https://picsum.photos/400/200?1"
                    class="card-img-top">

                <div class="card-body">

                    <h4>Madrid</h4>

                    <h5>$850.000</h5>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card card-custom">

                <img
                    src="https://picsum.photos/400/200?2"
                    class="card-img-top">

                <div class="card-body">

                    <h4>Miami</h4>

                    <h5>$1.200.000</h5>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card card-custom">

                <img
                    src="https://picsum.photos/400/200?3"
                    class="card-img-top">

                <div class="card-body">

                    <h4>Bariloche</h4>

                    <h5>$180.000</h5>

                </div>

            </div>

        </div>

    </div>

</section>

<?php

include("includes/footer.php");

?>