<?php
session_start();
$titre="Connexion";
include("includes/identifiants.php");
include("includes/debut.php");
include('includes/baniere-non-connecte.php');

if(!isset($_SESSION['pseudo'])){
?>
<!--**************************FORMULAIRE DE CONNEXION**************************************-->
<fieldset>

<h1> Bonjour et bienvenu </h1>
<p> Pour accéder à cette section, merci de vous identifier</p>
<p><a href="membres/register.php"> Pas encore inscrit ? </a></p><br/>
  
<form method="post" action="index.php">
<p style="color:gray;text-align:left">

<label for="pseudo">Pseudo :</label> 
<input name="pseudo" type="text" id="pseudo" /><br /><br/>

<label for="password">Mot de Passe :</label>
<input type="password" name="password" id="password" />

</p>
	
<p><input type="submit" value="Connexion" /></p>

<input type="hidden" name="page" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" />

</form>
<p><a href="membres/register.php">Pas encore inscrit ?</a><br/>
</fieldset>
<?php
//*************************VERIFICATION IDENTIFIANTS*****************************************

    $message='';
    if (empty($_POST['pseudo']) || empty($_POST['password']) ) //Oublie d'un champ
    {
        $message = '<p style="text-align:center">Merci de remplir tous les champs</p>';
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
	    $message = '<p style="text-align:center;font-size:20px;">Bienvenu '.$data['membre_pseudo'].' ! 
			Tu es maintenant connecté!<br/>Redirection automatique</p>';
?>
        <meta http-equiv="refresh" content="2 ; url=membres/accueil.php">
<?php
	}
	else // Acces pas OK !
	{
	    $message = '<p>Une erreur s\'est produite pendant votre identification.<br /> Le mot de passe ou le pseudo entré n\'est pas correcte.</p><p>Cliquez <a href="index.php">ici</a> pour revenir à la page précédente</p>';
	}
    $query->CloseCursor();
}
    echo $message;
}
echo'</div></body></html>';



