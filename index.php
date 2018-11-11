<?php
session_start();
$titre="Connexion";
include("includes/identifiants.php");
include("includes/debut.php");

if (!isset($_POST['pseudo']))
{
     header('Location: connexion.php');
  exit();
}

if ($id!=0) erreur(ERR_IS_CO);


if (!isset($_POST['pseudo'])) //On est dans la page de formulaire
{
    
    echo '<fieldset class="field_constants"><legend>Connexion</legend>
	
	     <h1> Bonjour et bienvenu sur le forum </h1>
		  <p> Pour accéder à cette section, merci de vous identifier</p>
		  <p><a href="membres/register.php"> Pas encore inscrit ? </a></p><br/>
		  ';
    
	echo '<form method="post" action="index.php">
	<p>
	<label for="pseudo">Pseudo :</label><input name="pseudo" type="text" id="pseudo" /><br />
	<label for="password">Mot de Passe :</label><input type="password" name="password" id="password" />
	</p>
	
	<p><input type="submit" value="Connexion" /></p>';
?>
<input type="hidden" name="page" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" />

<?php
    echo '</form>
	<a href="membres/register.php">Pas encore inscrit ?</a><br/>
    </fieldset>';
    

}

//On reprend la suite du code
else
{
    $message='';
    if (empty($_POST['pseudo']) || empty($_POST['password']) ) //Oublie d'un champ
    {
        $message = '<p>une erreur s\'est produite pendant votre identification.
	Vous devez remplir tous les champs</p>
	<p>Cliquez <a href="connexion.php">ici</a> pour revenir</p>';
    }
    else //On check le mot de passe
    {
        $query=$db->prepare('SELECT membre_mdp2, membre_id, membre_avatar, membre_rang, membre_pseudo
        FROM forum_membres WHERE membre_pseudo = :pseudo');
        $query->bindValue(':pseudo',$_POST['pseudo'], PDO::PARAM_STR);
        $query->execute();
        $data=$query->fetch();
	if ($data['membre_mdp2'] == ($_POST['password'])) // Acces OK !
	{
	    $_SESSION['pseudo'] = $data['membre_pseudo'];
	    $_SESSION['level'] = $data['membre_rang'];
	    $_SESSION['id'] = $data['membre_id'];
        $_SESSION['avatar'] = $data['membre_avatar'];
	    $message = '<p>Bienvenue '.$data['membre_pseudo'].', 
			vous êtes maintenant connecté!</p>
			<p>Cliquez <a href="membres/accueil.php">ici</a> 
			pour entrer sur le forum</p>';  
	}
	else // Acces pas OK !
	{
	    $message = '<p>Une erreur s\'est produite pendant votre identification.<br /> Le mot de passe ou le pseudo entré n\'est pas correcte.</p><p>Cliquez <a href="connexion.php">ici</a> pour revenir à la page précédente</p>';
	}
    $query->CloseCursor();
    }
    echo $message.'</div></body></html>';
    include("includes/footer_simple.php");

}

