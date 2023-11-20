<?php
session_start();

    // 1- Etablir une connexion avec la base de données.
    require __DIR__ . "/db/connexion.php";

    // 2- Effectuer la requête de récupération de tous les films de la table des films.

        // 2-a) Préparer la requête de sélection de toutes les colonnes de tous les enregistrements de la table "film". 
    $req = $db->prepare("SELECT * FROM film ORDER BY created_at DESC");

        // 2-b) Executer la requête.
    $req->execute();

        // 2-c) Récupérer tous les films.
    $films = $req->fetchAll();

?>
<?php include __DIR__ . "/partials/head.php"; ?>

    <?php include __DIR__ . "/partials/nav.php"; ?>

        <main class="container">
            <h1 class="text-center my-3 display-5">Liste des films</h1>

            <?php if( isset($_SESSION['success']) && !empty($_SESSION['success']) ) : ?>
                <div class="text-center alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['success']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif ?>

            <div class="d-flex justify-content-end align-items-center">
                <a href="create.php" class="btn btn-primary shadow">Nouveau film</a>
            </div>

            <div class="container">
                <div class="row">
                    <div class="col-md-8 col-lg-5 mx-auto">
                        <?php foreach($films as $film) : ?>
                            <div class="card shadow my-3">
                                <div class="card-body">
                                    <p class="card-text"><strong>Titre</strong>: <?php echo $film['name']; ?></p>
                                    <p class="card-text"><strong>Acteur(s)</strong>: <?php echo $film['actors']; ?></p>
                                    <hr>
                                    <a title="Les détails du film : <?php echo $film['name']; ?>" data-bs-toggle="modal" data-bs-target="#modal_<?php echo $film['id']; ?>" href="" class="text-dark mx-3"><i class="fa-solid fa-eye"></i></a>

                                    
                                    <a href="" class="text-secondary mx-3"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a href="" class="text-danger mx-3"><i class="fa-solid fa-trash-can"></i></a>
                                </div>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="modal_<?php echo $film['id']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <p class="modal-title fs-5" id="exampleModalLabel"><strong><?php echo $film['name']; ?></strong></p>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>Acteurs</strong>: <?php echo $film['name']; ?></p>
                                            <p><strong>Note</strong>: <?php echo $film['review'] != "" ? $film['review'] : "Non renseignée."; ?></p>
                                            <p><strong>Commentaire</strong>: <?php echo $film['comment'] != "" ? $film['comment'] : "Non renseigné."; ?></p>
                                            
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>
        </main>

        <?php include __DIR__ . "/partials/footer.php"; ?>
        
<?php include __DIR__ . "/partials/foot.php"; ?>