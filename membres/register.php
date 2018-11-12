<?php
session_start();
$titre="Enregistrement";
include("../includes/identifiants.php");
include("../includes/debut.php");



echo '<p>&nbsp; &nbsp;<i>Vous êtes ici</i> : &nbsp; &nbsp; <a href="accueil.php">Index du forum</a>  &nbsp; &nbsp;  Enregistrement';

?>

<?php
if (empty($_POST['pseudo'])) // Si la variable est vide, on peut considérer qu'on est sur la page de formulaire
{
	echo '<h1>Inscription</h1>';
	echo '<form method="post" action="register.php" enctype="multipart/form-data">
	
	<fieldset><legend>Identifiants</legend><br/>
	<label for="pseudo">* Pseudo </label> <br/> <input name="pseudo" type="text" id="pseudo" /> <br/>
	(le pseudo doit contenir entre 3 et 15 caractères)<br /><br/>
	
	<label for="password">* Mot de Passe </label><br/><input type="password" name="password" id="password" /><br /><br/>
	
	<label for="confirm">* Confirmer le mot de passe </label><br/><br/><input type="password" name="confirm" id="confirm" /><br/>
	</fieldset><br/>
	
	<fieldset><legend>Contacts</legend><br/>
	<label class="email" for="email">* Votre adresse Mail </label><br/><br/><input type="text" name="email" id="email" /><br /><br/>
	
	<label for="msn">Votre adresse MSN </label><br/><input type="text" name="msn" id="msn" /><br /><br/>
	
	<label for="website">Votre site web </label><br/><input type="text" name="website" id="website" /><br/>
	</fieldset><br/>
	
	<fieldset><legend>Informations supplémentaires</legend><br/>
	<label for="localisation">Localisation </label><br/><input type="text" name="localisation" id="localisation" /><br/>
	</fieldset><br/>
	
	<fieldset><legend>Profil sur le forum</legend><br/>
	<label for="avatar">Choisissez votre avatar :</label><br/><input value="INSERT:avatars/compte100.png" type="file" name="avatar" id="avatar" /><br/>(Taille max : 30Ko)<br />';
    
    echo '<img src="avatars/compte100.png"/>';
    
	
	echo '<label for="signature">Signature </label><br/><textarea cols="40" rows="4" name="signature" id="signature">La signature est limitée à 200 caractères</textarea><br/><br/>
	
	<p>Les champs précédés d\'un * sont obligatoires</p>
	
	<label>Se souvenir de moi ?</label><br/><input type="checkbox" name="souvenir" /><br />

	<p><input type="submit" value="S\'inscrire" /></p></form>
	</fieldset><br/>
	
	</div>
	</body>
	</html>';
	
	
} //Fin de la partie formulaire
else 
{
    $pseudo_erreur1 = NULL;
    $pseudo_erreur2 = NULL;
    $mdp_erreur = NULL;
    $email_erreur1 = NULL;
    $email_erreur2 = NULL;
    $msn_erreur = NULL;
    $signature_erreur = NULL;
    $avatar_erreur = NULL;
    $avatar_erreur1 = NULL;
    $avatar_erreur2 = NULL;
    $avatar_erreur3 = NULL;

    //On récupère les variables
    $i = 0;
    $temps = time(); 
    $pseudo=$_POST['pseudo'];
    $signature = $_POST['signature'];
    $email = $_POST['email'];
    $msn = $_POST['msn'];
    $website = $_POST['website'];
    $localisation = $_POST['localisation'];
	 //$avatar = $_POST['avatar'];
    $pass = ($_POST['password']);
    $confirm = ($_POST['confirm']);
	
    //Vérification du pseudo
    $query=$db->prepare('SELECT COUNT(*) AS nbr FROM forum_membres WHERE membre_pseudo =:pseudo');
    $query->bindValue(':pseudo',$pseudo, PDO::PARAM_STR);
    $query->execute();
    $pseudo_free=($query->fetchColumn()==0)?1:0;
    $query->CloseCursor();
	
    if(!$pseudo_free)
    {
        $pseudo_erreur1 = "Votre pseudo est déjà utilisé par un membre";
        $i++;
    }

    if (strlen($pseudo) < 3 || strlen($pseudo) > 15)
    {
        $pseudo_erreur2 = "Votre pseudo est soit trop grand, soit trop petit";
        $i++;
    }

    //Vérification du mdp
    if ($pass != $confirm || empty($confirm) || empty($pass))
    {
        $mdp_erreur = "Votre mot de passe et votre confirmation diffèrent, ou sont vides";
        $i++;
    }

    //Vérification de l'adresse email

    //Il faut que l'adresse email n'ait jamais été utilisée
    $query=$db->prepare('SELECT COUNT(*) AS nbr FROM forum_membres WHERE membre_email =:mail');
    $query->bindValue(':mail',$email, PDO::PARAM_STR);
    $query->execute();
    $mail_free=($query->fetchColumn()==0)?1:0;
    $query->CloseCursor();
    
    if(!$mail_free)
    {
        $email_erreur1 = "Votre adresse email est déjà utilisée par un membre";
        $i++;
    }
    //On vérifie la forme maintenant
    if (!preg_match("#^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]{2,}\.[a-z]{2,4}$#", $email) || empty($email))
    {
        $email_erreur2 = "Votre adresse E-Mail n'a pas un format valide";
        $i++;
    }
    //Vérification de l'adresse MSN
    if (!preg_match("#^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]{2,}\.[a-z]{2,4}$#", $msn) && !empty($msn))
    {
        $msn_erreur = "Votre adresse MSN n'a pas un format valide";
        $i++;
    }
    //Vérification de la signature
    if (strlen($signature) > 200)
    {
        $signature_erreur = "Votre signature est trop longue";
        $i++;
    }

    //Vérification de l'avatar :
    if (!empty($_FILES['avatar']['size']))
    {
        //On définit les variables :
        $maxsize = 30072; //Poid de l'image
        $maxwidth = 400; //Largeur de l'image
        $maxheight = 400; //Longueur de l'image
        $extensions_valides = array( 'jpg' , 'jpeg' , 'gif' , 'png', 'bmp' ); 
		//Liste des extensions valides
        
        if ($_FILES['avatar']['error'] > 0)
        {
                $avatar_erreur = "Erreur lors du transfert de l'avatar : ";
        }
        if ($_FILES['avatar']['size'] > $maxsize)
        {
                $i++;
                $avatar_erreur1 = "Le fichier est trop gros : (<strong>".$_FILES['avatar']['size']." Octets</strong> contre <strong>".$maxsize." Octets</strong>)";
        }

        $image_sizes = getimagesize($_FILES['avatar']['tmp_name']);
        if ($image_sizes[0] > $maxwidth OR $image_sizes[1] > $maxheight)
        {
                $i++;
                $avatar_erreur2 = "Image trop large ou trop longue : 
                (<strong>".$image_sizes[0]."x".$image_sizes[1]."</strong> contre <strong>".$maxwidth."x".$maxheight."</strong>)";
        }
        
        $extension_upload = strtolower(substr(  strrchr($_FILES['avatar']['name'], '.')  ,1));
        if (!in_array($extension_upload,$extensions_valides) )
        {
                $i++;
                $avatar_erreur3 = "Extension de l'avatar incorrecte";
        
		}
			
	}

   if ($i==0)
   {
	echo'<h1>Inscription terminée</h1>';
        echo'<p class="p_baniere">Bienvenue '.stripslashes(htmlspecialchars($_POST['pseudo'])).' vous êtes maintenant inscrit sur le forum</br/>
			Cliquez <a href="accueil.php">ici</a> pour entrer sur le forum</p>';
	
	$nomavatar=(!empty($_FILES['avatar']['size']))?move_avatar($_FILES['avatar']):'';
   
        $query=$db->prepare('INSERT INTO forum_membres 
        (membre_pseudo, membre_mdp2, membre_email,             
        membre_msn, membre_siteweb, membre_avatar,
        membre_signature, membre_localisation, membre_inscrit,   
        membre_derniere_visite)
        VALUES (:pseudo, :pass, :email, :msn, :website, :nomavatar, :signature, :localisation, :temps, :temps)');
	$query->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
	$query->bindValue(':pass', $pass, PDO::PARAM_STR);
	$query->bindValue(':email', $email, PDO::PARAM_STR);
	$query->bindValue(':msn', $msn, PDO::PARAM_STR);
	$query->bindValue(':website', $website, PDO::PARAM_STR);
	$query->bindValue(':nomavatar', $nomavatar, PDO::PARAM_STR);
	$query->bindValue(':signature', $signature, PDO::PARAM_STR);
	$query->bindValue(':localisation', $localisation, PDO::PARAM_STR);
	$query->bindValue(':temps', $temps, PDO::PARAM_INT);
        $query->execute();

	//Et on définit les variables de sessions
        $_SESSION['pseudo'] = $pseudo;
        $_SESSION['id'] = $db->lastInsertId(); ;
        $_SESSION['level'] = 2;
		  //$_SESSION['avatar'] = $avatar;
        $query->CloseCursor();
    }
    else
    {
        echo'<h1>Inscription interrompue</h1>';
        echo'<p class="p_baniere">Une ou plusieurs erreurs se sont produites pendant l incription</p>';
        echo'<p>'.$i.' erreur(s)</p>';
        echo'<p>'.$pseudo_erreur1.'</p>';
        echo'<p>'.$pseudo_erreur2.'</p>';
        echo'<p>'.$mdp_erreur.'</p>';
        echo'<p>'.$email_erreur1.'</p>';
        echo'<p>'.$email_erreur2.'</p>';
        echo'<p>'.$msn_erreur.'</p>';
        echo'<p>'.$signature_erreur.'</p>';
        echo'<p>'.$avatar_erreur.'</p>';
        echo'<p>'.$avatar_erreur1.'</p>';
        echo'<p>'.$avatar_erreur2.'</p>';
        echo'<p>'.$avatar_erreur3.'</p>';
       
        echo'<p>Cliquez <a href="register.php">ici</a> pour recommencer</p>';
    }

}

?>
</div>
</body>
</html>
