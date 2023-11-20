<?php
session_start();

    // 1- Si les données n'arrivent pas au serveur via la méthode "POST",
    if ( $_SERVER['REQUEST_METHOD'] !== "POST" ) 
    {
        // Rediriger l'utilisateur vers la page d'accueil puis arrêter l'exécution du script.
        return header("Location: index.php");
    }
    
    
    // 2- Dans le cas contraire, 
    // Si la méthode HTTP "DELETE" n'a pas été précisée dans le formalaire,
    if ( !isset($_POST['_method']) || $_POST['_method'] !== "DELETE" ) 
    {
        // Rediriger l'utilisateur vers la page d'accueil puis arrêter l'exécution du script.
        return header("Location: index.php");
    }

    
    $postClean = [];

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
        return header("Location: index.php");
    }
    unset($_SESSION['csrf_token']);


    // 4- Protéger le serveur contre les robots spameurs
    if ( !isset($postClean['honeypot']) || !empty($postClean['honeypot']) ) 
    {
        // Effectuer une redirection vers la page de laquelle proviennent les données puis arrêter l'exécution du script
        return header("Location: index.php");
    }

    // 6- Etablir une connexion avec la base de données
    require __DIR__ . "/db/connexion.php";

    // 7- Vérifier si l'identifiant provenant du formulaire correspond à celui 
    // d'un film qui existe dans la table des films.
    $req = $db->prepare("SELECT * FROM film WHERE id=:id");
    $req->bindValue(":id", $postClean['film_id']);
    $req->execute();

    // 8- Si le nombre total de films récupéré en fonction de la requête n'est pas égal à 1,
    if ( $req->rowCount() != 1 ) 
    {
        // Rediriger l'utilisateur vers la page d'accueil puis arrêter l'exécution du script.
        return header("Location: index.php");
    }

    // 9- Dans le cas contraire,
    // Récupérer le film à supprimer.
    $film = $req->fetch();

    // 10- Effectuer une seconde requête afin de supprimer le film.
    $deleteRequest = $db->prepare("DELETE FROM film WHERE id=:id");
    $deleteRequest->bindValue(":id", $film['id']);
    $deleteRequest->execute();

    // 11- Générer le message flash
    $_SESSION['success'] = "<strong><em>$film[name]</em></strong> a été retiré de la liste.";

    // 12- Effectuer une redirection vers la page d'accueil puis arrêter l'exécution du script.
    return header("Location: index.php");

