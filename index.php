<?php
//Cette fonction doit être appelée avant tout code html
session_start();

$titre = "Accueil du forum";
include("includes/identifiants.php");
include("includes/debut.php");


if(isset($_SESSION['pseudo']))
{
	header("Location: membres/accueil.php");
}
else
{
	echo '<fieldset class="field_constants">
	
	     <h1> Bonjour et bienvenu sur le forum </h1>
		  <p> Pour accéder à cette section, merci de vous identifier</p>
		  <p><a href="membres/register.php"> Pas encore inscrit ? </a></p>
		  ';
	
	echo '<form method="post" action="membres/connexion.php">
	
	<legend>Connexion</legend>
	<p><label for="pseudo">Pseudo :</label><br/><input name="pseudo" type="text" id="pseudo" /><br />
	<label for="password">Mot de Passe :</label><br/><input type="password" name="password" id="password" />
	<br/><br/><input type="submit" value="Connexion" /></p></form>
	
	</form>
	<a href="membres/accueil.php" >Entrer en tant que visiteur</a><br/>
	(Vous n\'aurrez pas accés aux profils des membres)
	</fieldset>';
	
}	

include("includes/footer_simple.php");
?>
