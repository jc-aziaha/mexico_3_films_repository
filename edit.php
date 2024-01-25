<?php
session_start();

    // 1- Si l'identifiant du film à modifier ne se trouve pas dans $_GET,
    if ( !isset($_GET['film_id']) || empty($_GET['film_id']) ) 
    {
        // Effectuer une redirection vers la page d'accueil puis arrêter l'exécution du script.
        return header('Location: index.php');
    }

    // var_dump("cool"); die();

    // Dans le cas contraire, 
    // 2- Récupérer cet identifiant en s'assurant de se protéger contre les failles de type XSS.

    $filmId = (int) htmlspecialchars($_GET['film_id']);
    // $filmId = intval(htmlspecialchars($_GET['film_id']));

    // 3- Etablir une connexion avec la base de données.
    require __DIR__ . "/db/connexion.php";

    // 4- Vérifier si l'identifiant récupéré de la barre d'url correspond à celui 
    // d'un film qui existe dans la table des films.
    $req = $db->prepare("SELECT * FROM film WHERE id=:id"); 

    $req->bindValue(":id", $filmId);

    $req->execute();

    // 5- Si le nombre total de films récupéré en fonction de la requête n'est pas égal à 1,
    if ( $req->rowCount() != 1 ) 
    {
        // Effectuer une redirection vers la page d'accueil puis arrêter l'exécution du script.
        return header("Location: index.php");
    }

    // 6- Dans le cas contraire,
    // Récupérer le film à modifier
    $film = $req->fetch();

    // 7- Si la méthode d'envoi des données est "POST",
    if ( $_SERVER['REQUEST_METHOD'] === "POST" ) 
    {

        // 7-7 Si la méthode "PUT" n'est pas précisée dans le formulaire,
        if ( !isset($_POST['_method']) || $_POST['_method'] !== "PUT" ) 
        {
            // Effectuer une redirection vers la page de laquelle proviennent les données puis arrêter l'exécution du script
            return header("Location: " . $_SERVER['HTTP_REFERER']);
        }

        $postClean = [];
        $errors    = [];

        // 8- Protéger le serveur contre les failles de type XSS
        foreach ($_POST as $key => $value) 
        {
            $postClean[$key] = htmlspecialchars($value);
        }

        // 9- Protéger le serveur contre les failles de type CSRF
        if ( !isset($_SESSION['csrf_token'])  || !isset($postClean['csrf_token']) 
            || empty($_SESSION['csrf_token']) || empty($postClean['csrf_token'])
            || $_SESSION['csrf_token'] !== $postClean['csrf_token']
        ) 
        {
            // Effectuer une redirection vers la page de laquelle proviennent les données puis arrêter l'exécution du script
            unset($_SESSION['csrf_token']);
            return header("Location: " . $_SERVER['HTTP_REFERER']);
        }
        unset($_SESSION['csrf_token']);


        // 10- Protéger le serveur contre les robots spameurs
        if ( !isset($postClean['honeypot']) || !empty($postClean['honeypot']) ) 
        {
            // Effectuer une redirection vers la page de laquelle proviennent les données puis arrêter l'exécution du script
            return header("Location: " . $_SERVER['HTTP_REFERER']);
        }


        // 11- Définir les contraintes de validation de chaque donnée provenant du formulaire

        // 11-a) Pour le nom du film
        if ( isset($postClean['name']) )
        {
            if ( empty($postClean['name']) ) 
            {
                $errors['name'] = "Le nom du film est obligatoire.";
            }
            else if( mb_strlen($postClean['name']) > 255 )
            {
                $errors['name'] = "Le nom du film ne doit pas dépasser 255 caractères.";
            }
        }

        // 11-b) Pour le nom du/des acteurs
        if ( isset($postClean['actors']) )
        {
            if ( empty($postClean['actors']) ) 
            {
                $errors['actors'] = "Le nom du ou des acteurs est obligatoire.";
            }
            else if( mb_strlen($postClean['actors']) > 255 )
            {
                $errors['actors'] = "Le nom du ou des acteurs ne doit pas dépasser 255 caractères.";
            }
        }


        // 11-c) Pour la note
        if ( isset($postClean['review']) )
        {
            if ( $postClean['review'] !== "" ) 
            {
                if ( !is_numeric($postClean['review']) )
                {
                    $errors['review'] = "La note doit être un nombre";
                }
                else if( $postClean['review'] < "0" || $postClean['review'] > "5")
                {
                    $errors['review'] = "La note doit être comprise entre 0 et 5.";
                }
            }
        }


        // 11-d) Pour les commentaires
        if ( isset($postClean['comment']) ) 
        {
            if ( $postClean['comment'] !== "" ) 
            {
                if ( mb_strlen($postClean['comment']) > 1000 ) 
                {
                    $errors['comment'] = "Le commentaire ne doit pas dépasser 1000 caractères.";
                }
            }
        }


        // 12- Si le tableau d'erreurs contient au moins une erreur,
        if ( count($errors) > 0 ) 
        {
            // Sauvegarder ces messages d'erreur en session
            $_SESSION['form_errors'] = $errors;
            
            // Sauvegarder les anciennes données provenant du formulaire en session
            $_SESSION['old'] = $postClean;

            // Effectuer une redirection vers la page de la laquelle proviennent les informations puis arrêter l'exécution du script
            return header("Location: " . $_SERVER['HTTP_REFERER']);
        }

        // 13- Dans le cas contraire,
        // Arrondir la note à un chiffre après la virgule
        if ( isset($postClean['review']) && $postClean['review'] !== "" ) 
        {
            $reviewRounded = round($postClean['review'], 1);
        }

        // 14- Etablir une connexion avec la base de données
        require __DIR__ . "/db/connexion.php";

        // 15- Effectuer la requête d'insertion du nouveau film dans la table des films de la base de données.

            // 15-a) On prepare la requête
        $req = $db->prepare("UPDATE film SET name=:name, actors=:actors, review=:review, comment=:comment, updated_at=now() WHERE id=:id");

            // 15-b) On initialise les valeurs
        $req->bindValue(":name", $postClean['name']);
        $req->bindValue(":actors", $postClean['actors']);
        $req->bindValue(":review", isset($reviewRounded) ? $reviewRounded : "");
        $req->bindValue(":comment", $postClean['comment']);
        $req->bindValue(":id", $film['id']);

            // 15-c) On execute la requête
        $req->execute();

            // 15-d) On ferme le curseur (Non obligatoire.)
        $req->closeCursor();


        // 16- Générer un message flash de succès
        $_SESSION['success'] = "Le film a été modifié.";

        // 12- Effectuer une redirection vers la page d'accueil puis arrêter l'exécution du script.
        return header("Location: index.php");
    }

    // Générons le token
    $_SESSION['csrf_token'] = bin2hex(random_bytes(30));
?>
<?php
    $title = "Modifier ce film";

    $description = "Modifier ce film.";

    $keywords = "dwwm, mexico, php, cinema, films, series, modifier";
?>
<?php include __DIR__ . "/partials/head.php"; ?>

    <?php include __DIR__ . "/partials/nav.php"; ?>

        <main class="container">
            <h1 class="text-center my-3 display-5">Modifier ce film</h1>
            
            <div class="container">
                <div class="row">
                    <div class="col-md-8 col-lg-5 mx-auto p-4 shadow bg-white">

                        <?php if(isset($_SESSION['form_errors']) && !empty($_SESSION['form_errors'])) : ?>
                            <div class="alert alert-danger" role="alert">
                                <ul>
                                    <?php foreach($_SESSION['form_errors'] as $error) : ?>
                                        <li><?php echo $error; ?></li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                            <?php unset($_SESSION['form_errors']); ?>
                        <?php endif ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="name">Nom du film <span class="text-danger fs-4">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" autofocus value="<?php echo isset($_SESSION['old']['name']) ? $_SESSION['old']['name'] : $film['name']; unset($_SESSION['old']['name']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="actors">Nom du/des acteurs <span class="text-danger fs-4">*</span></label>
                                <input type="text" name="actors" id="actors" class="form-control" value="<?php echo isset($_SESSION['old']['actors']) ? $_SESSION['old']['actors'] : $film['actors']; unset($_SESSION['old']['actors']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="review">La note / 5</label>
                                <input type="number" step="0.1" min="0" max="5" name="review" id="review" class="form-control" value="<?php echo isset($_SESSION['old']['review']) ? $_SESSION['old']['review'] : $film['review']; unset($_SESSION['old']['review']); ?>">
                                <small><em>La note doit être comprise entre 0 et 5.</em></small>
                            </div>
                            <div class="mb-3">
                                <label for="comment">Laissez un commentaire</label>
                                <textarea name="comment" id="comment" class="form-control" rows="4"><?php echo isset($_SESSION['old']['comment']) ? $_SESSION['old']['comment'] : $film['comment']; unset($_SESSION['old']['comment']); ?></textarea>
                                <small><em>Le commentaire ne doit pas dépasser 1000 caractères.</em></small>
                            </div>
                            <div>
                                <input formnovalidate type="submit" value="Modifier" class="btn btn-primary shadow">
                            </div>
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="honeypot" value="">
                            <input type="hidden" name="_method" value="PUT">
                        </form>
                    </div>
                </div>
            </div>

        </main>

        <?php include __DIR__ . "/partials/footer.php"; ?>
        
<?php include __DIR__ . "/partials/foot.php"; ?>

