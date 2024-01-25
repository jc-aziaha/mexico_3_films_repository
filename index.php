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

    // Générer le token.
    $_SESSION['csrf_token'] = bin2hex(random_bytes(30));
?>
<?php
    $title = "Liste des films";

    $description = "Consultez la liste des films que j'aime bien.";

    $keywords = "dwwm, mexico, php, cinema, films, series";

    $font_awesome = <<<HTML
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
HTML;
?>
<?php include __DIR__ . "/partials/head.php"; ?>

    <?php include __DIR__ . "/partials/nav.php"; ?>

        <main class="container">
            <h1 class="text-center my-3 display-5">Liste des films</h1>

            
            <div class="d-flex justify-content-end align-items-center">
                <a href="create.php" class="btn btn-primary shadow">Nouveau film</a>
            </div>
            

            <?php if(count($films) > 0) : ?>
            <div class="container mb-3">
                <div class="row">
                    <div class="col-md-8 col-lg-5 mx-auto">

                        <?php if( isset($_SESSION['success']) && !empty($_SESSION['success']) ) : ?>
                            <div class="text-center alert alert-success alert-dismissible fade show my-3" role="alert">
                                <?php echo $_SESSION['success']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['success']); ?>
                        <?php endif ?>

                        <?php foreach($films as $film) : ?>
                            <div class="card shadow my-3">
                                <div class="card-body">
                                    <p class="card-text"><strong>Titre</strong>: <?php echo $film['name']; ?></p>
                                    <p class="card-text"><strong>Acteur(s)</strong>: <?php echo $film['actors']; ?></p>
                                    <hr>
                                    <a title="Les détails du film : <?php echo $film['name']; ?>" data-bs-toggle="modal" data-bs-target="#modal_<?php echo $film['id']; ?>" href="" class="text-dark mx-3"><i class="fa-solid fa-eye"></i></a>
                                    <a title="Modifier le film: <?php echo $film['name']; ?>" href="edit.php?film_id=<?php echo $film['id']; ?>" class="text-secondary mx-3"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a onclick="event.preventDefault(); return confirm('Confirmer la suppression?') && document.querySelector('#delete_film_<?php echo $film['id']; ?>').submit();" title="Supprimer le film: <?php echo $film['name']; ?>" href="" class="text-danger mx-3"><i class="fa-solid fa-trash-can"></i></a>
                                    <form action="delete.php" method="POST" id="delete_film_<?php echo $film['id']; ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="honeypot" value="">
                                        <input type="hidden" name="film_id" value="<?php echo $film['id'] ?>">
                                    </form>
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
                                            <p><strong>Acteurs</strong>: <?php echo $film['actors']; ?></p>
                                            <p><strong>Note</strong>: <?php echo $film['review'] != "" ? $film['review'] : "Non renseignée."; ?></p>
                                            <p><strong>Commentaire</strong>: <?php echo $film['comment'] != "" ? nl2br($film['comment']) : "Non renseigné."; ?></p>
                                            
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
            <?php else : ?>
                <p class="text-center mt-5">Aucun film ajouté à la liste pour l'instant.</p>
            <?php endif ?>
        </main>

        <?php include __DIR__ . "/partials/footer.php"; ?>
        
<?php include __DIR__ . "/partials/foot.php"; ?>