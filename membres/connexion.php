<?php

$titre="Connexion";
include("../includes/identifiants.php");
include("../includes/debut.php");

if(isset($_POST['pseudo'])){
	include('../includes/baniere-non-connecte.php');
}

echo'&nbsp; &nbsp; <i><p class="p_baniere">Vous êtes ici : &nbsp; &nbsp; </i><a href ="accueil.php">Accueil forum</a>  &nbsp; &nbsp;  Connexion<br/><br/>';
echo '<h1 class="p_message>Connexion</h1>';


if ($id!=0) erreur(ERR_IS_CO);


//On reprend la suite du code
else
{
    $message='';
    if (empty($_POST['pseudo']) || empty($_POST['password']) ) //Oublie d'un champ
    {
        $message = '<p class="p_message">une erreur s\'est produite pendant votre identification.
	Vous devez remplir tous les champs</p>
	<p class="p_message">Cliquez <a href="connexion.php">ici</a> pour revenir</p>';
    }
	
    else //On check le mot de passe
    {
        $query=$db->prepare('SELECT membre_mdp, membre_id, membre_rang, membre_pseudo, membre_avatar
        FROM forum_membres WHERE membre_pseudo = :pseudo');
        $query->bindValue(':pseudo',$_POST['pseudo'], PDO::PARAM_STR);
        $query->execute();
        $data=$query->fetch();
		 
			if ($data['membre_mdp'] == ($_POST['password'])) // Acces OK !
			{
				 $_SESSION['pseudo'] = $data['membre_pseudo'];
				 $_SESSION['level'] = $data['membre_rang'];
				 $_SESSION['id'] = $data['membre_id'];
				 $_SESSION['avatar'] = $data['membre_avatar'];
				 header('Location: accueil.php');
			}
			else // Acces pas OK !
			{
				 $message = '<p class="p_message">Une erreur s\'est produite 
				 pendant votre identification.<br /> Le mot de passe ou le pseudo 
				 entré n\'est pas correcte.</p>
				 <br />Cliquez <a href="../index.php">ici</a> 
				 pour revenir à la page d accueil</p>';
			}
    $query->CloseCursor();
    }
    echo $message.'</div></body></html>';

}
?>

