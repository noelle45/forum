<div class="include_fixed">
<h1>essai </h1>
<?php

if(isset($_SESSION['pseudo']))
	{
		
		echo'<p class="p_baniere"><img src="avatars/'.$data['membre_avatar'].'"alt="Ce membre n\'a pas d\'avatar" /></p>';
		
		 echo '<p class="p_baniere"> <a href="./voirprofil.php?m='.$_SESSION['id'].'&amp;action=consulter"> Mon compte '.htmlspecialchars($_SESSION['pseudo']).'</a>  &nbsp; &nbsp; &nbsp;  <a href="deconnexion.php" title="vous avez terminé votre visite ?"> Me deconnecter</a><p>';	
		 
?>
<hr>
<?php

		 echo'<p class="parag_centre"><a href="./voirprofil.php?m='.$_SESSION['id'].'&amp;action=consulter">Mon profil</a>&nbsp;&nbsp;  &nbsp;&nbsp; &nbsp;  
		 <a href="../messagerie/amis.php"> Mes amis</a>&nbsp;&nbsp;  &nbsp;&nbsp; &nbsp;  
		 <a href="../messagerie/messagerie.php" title="Messagerie"> Ma messagerie</a>&nbsp; &nbsp;  &nbsp; &nbsp; &nbsp;  
		 <a href="../messagerie/minichat/index.php">Ouvrir le MiniChat</a>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
		 <a href="../membres/memberlist.php" title="Voir la liste des membres"> Liste des membres connectés</a>
		 </p>
		 ';
	}
	
else
{
	echo '<h1>Bonjour et Bienvenue !<br/> vous n\'êtes pas connecté(e)</h1>
	<p class="p_baniere"><a href="../index.php" title="Me connecter"> Me connecter</a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href="register.php" title="M\'inscrire"> M\'inscrire</a></p>';
}
?>

</div>