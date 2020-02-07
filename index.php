<?php

// Récupération de l'URL du formulaire
if (@$_POST['url'])
    $url = $_POST['url'];
else
    $url = "";

// Si l'URL est valide
if(!empty($url) && !filter_var($url, FILTER_VALIDATE_URL) === false){

    // Chargement de la class pour récupérer le titre de la vidéo
    include_once 'YouTubeDownloader.class.php';
    $handler = new YouTubeDownloader();
    
    // Récupération de l'objet de l'URL
    $downloader = $handler->getDownloader($url);

    // Si l'URL est bien celle d'une vidéo YouTube
    if($downloader->hasVideo()){

        // Récupération des infos
        $videoTitle = $downloader->getVideoInfos()[0]['title']; // Titre
        $id = $downloader->getVideoId();

        // Formatage du nom de fichier
        $fileName = str_replace(' ', '_', $videoTitle);
        $fileName = mb_strtolower($fileName, 'UTF-8');
        $fileName = preg_replace('/[^A-Za-z0-9.\_\-]/', '', basename($fileName));

        // Si aucun fichier du dossier mp3 ne correspond à la vidéo
        if (shell_exec("ls mp3/*-$id.mp3 | wc -l") == 0) {

            // Création d'un dossier de travail unique
            $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
            $folderName = substr(str_shuffle($permitted_chars), 0, 16);
            shell_exec("mkdir $folderName");

            // Mise à jour de youtube-dl
            shell_exec("./youtube-dl -U");

            // Téléchargement et conversion de la vidéo
            shell_exec("cd $folderName; ./../youtube-dl " . $downloader->getVideoUrl());
            shell_exec("cd $folderName; mv *.mp4 $fileName.mp4");
            shell_exec("./ffmpeg/ffmpeg -i $folderName/$fileName.mp4 mp3/$fileName-$id.mp3 -q:a 2");

            // Nettoyage du dossier de travail
            shell_exec("rm -r $folderName");
        }
        
        // Lancement du téléchargement
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $videoTitle . '.mp3"');
        header("Content-Type: audio/mpeg");
        header("Content-Transfer-Encoding: binary");
        readfile("mp3/$fileName-$id.mp3");
    }
}

?>

<html lang="fr">
    <head>
        <!-- Site -->
        <meta charset="utf-8">
        <meta name="author" content="Julien Giraud">
        <meta name="copyright" content="https://www.son.julien-giraud.fr">
        <meta name="language" content="fr">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" href="https://www.son.julien-giraud.fr/images/favicon.png">

        <!-- CSS -->
        <link rel="stylesheet" type="text/css" href="https://www.son.julien-giraud.fr/css/normalize.css">
        <link rel="stylesheet" type="text/css" href="https://www.son.julien-giraud.fr/css/icons.css">
        <link rel="stylesheet" type="text/css" href="https://www.son.julien-giraud.fr/css/fonts.css">
        <link rel="stylesheet" type="text/css" href="https://www.son.julien-giraud.fr/css/variables.css">
        <link rel="stylesheet" type="text/css" href="https://www.son.julien-giraud.fr/css/global.css">
        <link rel="stylesheet" type="text/css" href="https://www.son.julien-giraud.fr/css/styles.css">

        <!-- Page -->
        <title>Convertisseur YouTube MP3</title>
        <meta name="description" content="Convertisseur YouTube MP3, téléchargez votre musique depuis YouTube !">
        <meta name="keywords" content="convertisseur, musique, son, MP3, MP4, YouTube">
        <link rel="canonical" href="https://www.son.julien-giraud.fr">
    </head>

    <body>
        <header>
            <nav id="navbar" class="navbar up">
                <div class="navbar-container">
                    <a class="brand" href="." title="Convertisseur YouTube MP3">
                        <img src="https://www.son.julien-giraud.fr/images/favicon.png" alt="icone accueil" class="icon mr-4">
                        Accueil
                    </a>
                </div>
            </nav>
        </header>

        <section>
            <div class="container">
                <h1>Convertisseur YouTube MP3</h1>

                <p>Entrer l'URL de la vidéo</p>
                <form action="." method="post">
                    <p class="m-0"><input autocomplete="off" class="input-text" type="text" name="url" /></p>
                    <p><input class="button" type="submit" value="Télécharger"></p>
                </form>

                <a href="." title="Nouvelle conversion">Convertir une autre vidéo</a>

                <p class="mt-10" style="font-size:12px">*Ce site permet de convertir en mp3 des vidéos accessibles librement depuis YouTube, et ne souhaite pas porter atteinte aux auteurs ou aux ayants droit. Si vous souhaitez supprimer un contenu de ce site, merci d'envoyez un email à l'adresse <a href="mailto:contact@julien-giraud.fr" title="Envoyer un email à contact@julien-giraud.fr">contact@julien-giraud.fr</a></p>
            </div>
        </section>

        <footer>
            <div class="footer-container">
                <div>
                    <span>Julien Giraud | Étudiant développeur lyonnais</span>
                </div>
                <div>
                    <a href="https://www.julien-giraud.fr" title="Accueil - Julien Giraud Développeur">
                        <img src="https://www.son.julien-giraud.fr/images/julien-giraud-developpeur-rubiks-cube.svg" alt="Logo Rubik's Cube Développeur" class="logo-footer">
                    </a>
                </div>
                <div class="footer-copyright">
                    <span>© Tous droits réservés -</span>
                    <span title="Dernière mise à jour : 02/02/2020">2019 / 2020</span>
                </div>
            </div>
        </footer>

        <!-- footer JS -->
        <script src="https://www.son.julien-giraud.fr/javascript/main.js"></script>

    </body>
</html>
