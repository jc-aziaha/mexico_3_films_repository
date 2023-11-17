<?php
    // Partie Controller 
    // Traitement des données par le serveur

    if ( $_SERVER['REQUEST_METHOD'] === "POST" ) 
    {
        var_dump($_POST);
        exit();
    }
?>
<!-- Partie Vue -->
<?php include __DIR__ . "/partials/head.php"; ?>

    <?php include __DIR__ . "/partials/nav.php"; ?>

        <main class="container">
            <h1 class="text-center my-3 display-5">Nouveau film</h1>

            <div class="container">
                <div class="row">
                    <div class="col-md-5 mx-auto">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="name">Nom du film <span class="text-danger fs-4">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" autofocus>
                            </div>
                            <div class="mb-3">
                                <label for="actors">Nom du/des acteurs <span class="text-danger fs-4">*</span></label>
                                <input type="text" name="actors" id="actors" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="review">La note / 5</label>
                                <input type="number" name="review" id="review" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="comment">Laissez un commentaire</label>
                                <textarea name="comment" id="comment" class="form-control" rows="4"></textarea>
                            </div>
                            <div>
                                <input type="submit" value="Créer" class="btn btn-primary shadow">
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </main>

        <?php include __DIR__ . "/partials/footer.php"; ?>
        
<?php include __DIR__ . "/partials/foot.php"; ?>