<?php
session_start();
$titre="Connexion";
include("../includes/identifiants.php");
include("../includes/debut.php");

echo '<p><i>Vous êtes ici</i> : <a href="accueil.php">Index du forum</a> --> Connexion';


echo '<h1>Connexion</h1>';


if ($id!=0) erreur(ERR_IS_CO);


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
        $query=$db->prepare('SELECT membre_mdp, membre_id, membre_rang, membre_pseudo
        FROM forum_membres WHERE membre_pseudo = :pseudo');
        $query->bindValue(':pseudo',$_POST['pseudo'], PDO::PARAM_STR);
        $query->execute();
        $data=$query->fetch();
		 
			if ($data['membre_mdp'] == ($_POST['password'])) // Acces OK !
			{
				 $_SESSION['pseudo'] = $data['membre_pseudo'];
				 $_SESSION['level'] = $data['membre_rang'];
				 $_SESSION['id'] = $data['membre_id'];
				 $message = '<p>Bienvenue '.$data['membre_pseudo'].', 
					vous êtes maintenant connecté!</p>
					<p>Cliquez <a href="accueil.php">ici</a> 
					pour revenir à la page d accueil</p>';  
			}
			else // Acces pas OK !
			{
				 $message = '<p>Une erreur s\'est produite 
				 pendant votre identification.<br /> Le mot de passe ou le pseudo 
				 entré n\'est pas correcte.</p>
				 <br />Cliquez <a href="accueil.php">ici</a> 
				 pour revenir à la page d accueil</p>';
			}
    $query->CloseCursor();
    }
    echo $message.'</div></body></html>';

}
?>

