

<div class="include_fixed">

<?php

if(isset($_SESSION['pseudo']))
	{
		if(empty($_SESSION['avatar']))
		{
			echo'<p class="p_baniere"><img src="avatars/compte100.png" "alt="Ce membre n\'a pas d\'avatar" /></p>';
		}
		else
		{
			echo'<p class="p_baniere"><img src="../membres/avatars/'.$_SESSION['avatar'].'"alt="Ce membre n\'a pas d\'avatar" /></p>';
		}
	
		 echo '<p class="p_baniere"> <a href="../membres/voirprofil.php?m='.$_SESSION['id'].'&amp;action=consulter"> Compte de '.htmlspecialchars($_SESSION['pseudo']).'</a>  &nbsp; &nbsp; &nbsp;  <a href="deconnexion.php" title="vous avez terminé votre visite ?"> Me deconnecter</a><p>';
    ?>
<hr>
<?php
		 
		 echo'<p class="parag_centre"><a href="../membres/voirprofil.php?m='.$_SESSION['id'].'&amp;action=consulter">Mon profil</a>&nbsp;&nbsp;  &nbsp;&nbsp; &nbsp;  
		 <a href="amis.php"> Mes amis</a>&nbsp;&nbsp;  &nbsp;&nbsp; &nbsp;  
		 <a href="messagerie.php" title="Messagerie"> Ma messagerie</a>&nbsp; &nbsp;  &nbsp; &nbsp; &nbsp;  
		 <a href="minichat/index.php">Ouvrir le MiniChat</a>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
		 <a href="../membres/memberlist.php" title="Voir la liste des membres"> Liste des membres connectés</a>
		 </p>
		 ';
	}


	
else
{
	echo '<h1>Bonjour et Bienvenue !<br/> vous n\'êtes pas connecté(e)</h1><br/>
	<p class="p_baniere"><a href="../index.php" title="Me connecter"> Me connecter</a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href="register.php" title="M\'inscrire"> M\'inscrire</a></p>';
}
?>
</div>