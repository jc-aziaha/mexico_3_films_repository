<!DOCTYPE html>
<html lang="fr">
    <head>

        <!-- Encodage des caractères -->
        <meta charset="UTF-8">

        <!-- Minimum de responsive design à toujours mettre en place -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- Titre de la page affiché dans l'onglet -->
        <?php if( isset($title) && !empty($title) ) : ?>
            <title><?php echo $title; ?> - Cinema</title>
        <?php endif ?>

        <!-- Favicon -->
        <link rel="apple-touch-icon" sizes="180x180" href="assets/images/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="assets/images/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon/favicon-16x16.png">
        <link rel="manifest" href="assets/images/favicon/site.webmanifest">
        <link rel="mask-icon" href="assets/images/favicon/safari-pinned-tab.svg" color="#5bbad5">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="theme-color" content="#ffffff">

        <!-- Google font -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins&display=swap" rel="stylesheet">

        <!-- Seo (Référencement naturel) -->
        <?php if( isset($keywords) && !empty($keywords) ) : ?>
            <meta name="keywords" content="<?php echo $keywords; ?>">
        <?php endif ?>

        <?php if( isset($description) && !empty($description) ) : ?>
            <meta name="description" content="<?php echo $description; ?>">
        <?php endif ?>

        <meta name="robots" content="index, follow">
        <meta name="author" content="dwwm-mexico">
        <meta name="publisher" content="dwwm-mexico">

        <?php if( isset($font_awesome) && !empty($font_awesome) ) : ?>
            <!-- Font awesome -->
            <?php echo $font_awesome; ?>
        <?php endif ?>

        <!-- Bootstrap 5 Links -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <link rel="stylesheet" href="assets/styles/app.css">
    </head>
    <body class="bg-light">