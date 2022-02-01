<?php session_start(); // on démarre une nouvelle session ou reprend une session existante

require('src/connect.php');
// Nécessaire pour se connecter à la base de données

if(!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['password_two'])) {
    // Les 3 champs doivent être renseignés pour faire une inscription d'utilisateur
    // Si les champs du formulaire ne sont pas vides alors on peut traiter la demande de connexion

    // déclaration des variables
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);
    $password_two = htmlspecialchars($_POST['password_two']);
    // htmlspecialchars() est une fonction php qui permet de convertir les caractères spéciaux en entités html
    // les filtres sont utilisés pour valider les données et se prémunir des failles XSS
    
    // on hash le mot de passe
    $hashedpassword = password_hash($password, PASSWORD_DEFAULT);

    // on vérifie que l'adresse indiquée est au format e-mail
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        header('location:inscription.php?error=1&message=Adresse e-mail non-valide');
        exit();

    }

    // on vérifie que les deux mots de passe soient les mêmes
    if($password != $password_two) {
        
        header('location:inscription.php?error=1&message=Les mots de passe ne sont pas identiques.');
        exit();

    }

    // on vérifie que l'adresse e-mail n'aie pas déjà été utilisée
    $requete = $pdo->prepare("SELECT COUNT(*) as nbMail FROM user WHERE email = ?");
    $requete->execute(array($email));

    while($email_verif = $requete->fetch()) {
        
        // si le nombre d'e-mail que la requête trouve est différent de 0, alors on affiche un message

        if($email_verif['nbMail'] !=0) {
            
            header('location:inscription.php?error=1&message=Cette adresse e-mail a déjà été utilisée. Veuillez vous inscrire avec une autre.');
            exit();

        }

    }

    // on envoie les infos en base de données
    $requete = $pdo->prepare("INSERT INTO user(email, password) VALUES(?,?)");
    $requete->execute(array($email, $hashedpassword));

    header('location:inscription.php?success=1&message=Vous êtes à présent inscrit !');
	exit();

} 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
    <title>Disney+ subscribe</title>
</head>
<body>

    <?php include('src/header.php'); ?>

    <section>
		<div id="login-body">
			
            <h2>Inscrivez-vous avec votre adresse e-mail</h2>

            <?php 
            if(isset($_GET['success'])) {

                echo '<div class="alert success">'.htmlspecialchars($_GET['message']).'</div>';

            } 
            else if(isset($_GET['error'])) {
                
                echo '<div class="alert error">'.htmlspecialchars($_GET['message']).'</div>';
            
            }
            ?>

			<form method="post" action="inscription.php">
				<input type="email" name="email" placeholder="Votre adresse e-mail" required />
				<input type="password" name="password" placeholder="Créez votre mot de passe" required />
				<input type="password" name="password_two" placeholder="Retapez votre mot de passe" required />
				<input type="submit" value ="S'inscrire" class="button" name="Submit">
			</form>

			<p class="grey">Vous êtes déjà inscrit sur Disney+ ? <a href="index.php">Connectez-vous</a>.</p>
		
        </div>
	</section>

	<?php include('src/footer.php'); ?>
</body>
</html>