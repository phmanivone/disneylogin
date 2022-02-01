<?php session_start(); // Démarre une nouvelle session ou reprend une session existante

require ('src/connect.php');
// comme include, require inclut et exécute le fichier spécifié en argument
// sauf que lorsqu'il y aura une erreur le script s'arrêtera

if(!empty($_POST['email']) && !empty($_POST['password']) ){
    // on vérifie que les champs du formulaire ne sont pas vides pour pouvoir traiter la demande de connexion


	// Déclaration des variables

	$email = htmlspecialchars($_POST['email']);
	$password = htmlspecialchars($_POST['password']);
    // htmlspecialchars convertit les caractères spéciaux en entités HTML

	if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    // on filtre pour vérifier que ce soit bien une adresse email qui a été entrée
        
        header('location:index.php?error=1&message=Format e-mail invalide');
        exit();

	}

	// Connexion au site

    $requete = $pdo->prepare("SELECT * FROM user WHERE email = ?");
	$requete->execute(array($email));
    // prepare prépare une requête à l'exécution et retourne un objet
    // Lorsque la requête est préparée, la base de données va analyser, compiler et optimiser son plan pour exécuter la requête.
	// execute exécute une requête préparée

    
    while($user = $requete->fetch()) {
    // fetch récupère une ligne depuis un jeu de résultats associé à l'objet PDO.
        
        if(password_verify($password, $user['password'])) {
        // if($password == $user['password']) {
        // on vérifie que le mot de passe donné dans le formulaire correspond au mot de passe de l'utilisateur dans la base de donnée
                
            $_SESSION['connect'] = 1;
            $_SESSION['user'] = $user['email'];

            header('location:index.php?success=1&message=Vous êtes connecté.');
            exit();

        } else {

            header('location:index.php?error=1&message=La connexion a échoué. Réessayez.');
            exit();

        }
        
    }

} ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
    <title>Disney+ login</title>
</head>
<body>

<?php include("src/header.php"); ?>

<section> 
    <div id='login-body'>
     
    
        <?php
        if(isset($_SESSION['user'])) { 
        // $_SESSION est une superglobale, c'est une variable interne à php
        // elle est disponible dans tous les contextes du script, globale ou locale
        // $_SESSION prend comme toutes les superglobales la forme d'un tableau associatif
        ?>
    
        <h1>Bonjour !</h1>

        <p>Qu'allez-vous regarder aujourd'hui ?</p>

        <small><a href="logout.php">Se déconnecter</a></small>
        
        <?php } else { ?>

            <h2>Identifiez-vous avec votre adresse e-mail</h2>             

        <?php } ?>
        
        <?php 
            if(isset($_GET['success'])) {

                echo '<div class="alert success">'.htmlspecialchars($_GET['message']).'</div>';

            } 
            else if(isset($_GET['error'])) {
                
                echo '<div class="alert error">'.htmlspecialchars($_GET['message']).'</div>';
            
            }
        ?>

            <form action="index.php" method="post" name="Login_form">
            
            <!-- la méthode POST est utilisée pour transmettre des données 
            via un formulaire passé en caché et on ne voit pas les données des variables
                
            la méthode GET passe les variables via l'URL en une seule requête HTTP
            les données seront visibles dans l'URL -->

                <input type="email" placeholder="Adresse e-mail" class="Input" name="email">
                <input type="password" placeholder="Mot de passe" class="Input" name="password">
                <input type="submit" value ="Se connecter" class="button" name="submit">
            </form>

            <p class="grey">Nouveau sur Disney+ ? <a href="inscription.php">S'inscrire</a></p>

    </div>
</section>

<?php include("src/footer.php"); ?>
    
</body>
</html>