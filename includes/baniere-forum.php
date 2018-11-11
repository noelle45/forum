<div class="include_fixed">
<?php

if(isset($_SESSION['pseudo']))
	{
         echo'<p class="p_baniere"><img src="avatars/'.$_SESSION['avatar'].'"alt="Ce membre n\'a pas d\'avatar" /></p>';
		 echo '<p class="p_baniere"> <a href="../membres/voirprofil.php?m='.$_SESSION['id'].'&amp;action=consulter"> Mon compte '.htmlspecialchars($_SESSION['pseudo']).'</a>  &nbsp; &nbsp; &nbsp;  <a href="../membres/deconnexion.php" title="vous avez terminé votre visite ?"> Me deconnecter</a><p>';	
		 
        ?>
        <hr>
        <?php
	}
	
else
{
	echo '<h1>Bonjour et Bienvenue !<br/> vous n\'êtes pas connecté(e)</h1>
	<p class="parag_centre"><a href="index.php" title="Me connecter"> Me connecter</a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href="../membres/register.php" title="M\'inscrire"> M\'inscrire</a></p>';
}
?>

</div>