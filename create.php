<?php
session_start();

    /*
     * ------------------------------------------------------
     * Partie Controller 
     * Traitement des données par le serveur
     * ------------------------------------------------------
    */

    // 1- Si la méthode d'envoi des données est "POST",
    if ( $_SERVER['REQUEST_METHOD'] === "POST" ) 
    {

        $postClean = [];
        $errors    = [];

        // 2- Protéger le serveur contre les failles de type XSS
        foreach ($_POST as $key => $value) 
        {
            $postClean[$key] = htmlspecialchars($value);
        }

        // 3- Protéger le serveur contre les failles de type CSRF
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


        // 4- Protéger le serveur contre les robots spameurs
        if ( !isset($postClean['honeypot']) || !empty($postClean['honeypot']) ) 
        {
            // Effectuer une redirection vers la page de laquelle proviennent les données puis arrêter l'exécution du script
            return header("Location: " . $_SERVER['HTTP_REFERER']);
        }


        // 5- Définir les contraintes de validation de chaque donnée provenant du formulaire

        // 5-a) Pour le nom du film
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

        // 5-b) Pour le nom du/des acteurs
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


        // 5-c) Pour la note
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


        // 5-d) Pour les commentaires
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


        // 6- Si le tableau d'erreur contient au moins une erreur,
        if ( count($errors) > 0 ) 
        {
            // Sauvegarder ces messages d'erreur en session
            // Sauvegarder les anciennes données provenant du formulaire en session

            // Effectuer une redirection vers la page de la laquelle proviennent les informations puis arrêter l'exécution du script
            return header("Location: " . $_SERVER['HTTP_REFERER']);
        }

        var_dump("cool"); die();
        // 7- Dans le cas contraire,
            
        // 8- Arrondir la note à un chiffre après la virgule

        // 9- Etablir une connexion avec la base de données

        // 10- Effectuer la requête d'insertion du nouveau film dans la table des films de la base de données.
        
        // 11- Générer un message flash de succès

        // 12- Effectuer une redirection vers la page d'accueil puis arrêter l'exécution du script.

    }

    // Générons le token
    $_SESSION['csrf_token'] = bin2hex(random_bytes(30));
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
                                <input type="number" step="0.1" min="0" max="5" name="review" id="review" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="comment">Laissez un commentaire</label>
                                <textarea name="comment" id="comment" class="form-control" rows="4"></textarea>
                            </div>
                            <div>
                                <input type="submit" value="Créer" class="btn btn-primary shadow">
                            </div>
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="honeypot" value="">
                        </form>
                    </div>
                </div>
            </div>

        </main>

        <?php include __DIR__ . "/partials/footer.php"; ?>
        
<?php include __DIR__ . "/partials/foot.php"; ?>