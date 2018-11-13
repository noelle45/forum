<div class="include_fixed">
<?php
    
    if(isset($_SESSION['pseudo'])){
        echo '<p class="p_baniere_petit_pseudo"> <a href="../membres/voirprofil.php?m='.$_SESSION['id'].'&amp;action=consulter">'
        .htmlspecialchars($_SESSION['pseudo']).'</p>';
        
        if(!empty($_SESSION['avatar'])){
        echo '<p class="p_baniere_petit"><img class="roundedImage" src="../membres/avatars/'.$_SESSION['avatar'].'" alt="Avatar" />';}
	else{
	echo '<p class="p_baniere_petit"><img class="roundedImage" src="../membres/avatars/compte100.png" alt="Avatar" />';}
        
        echo '<br/><p class="p_baniere_petit"><a href="../membres/deconnexion.php" title="vous avez terminé votre visite ?"> Me deconnecter</a></p>';
       }
    else{
        echo '<h1>Bonjour et Bienvenue !<br/> vous n\'êtes pas connecté(e)</h1>
	         <p class="parag_centre"><a href="../index.php" title="Me connecter"> Me connecter</a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href="../membres/register.php" title="M\'inscrire"> M\'inscrire</a></p>';
    }
?>
</div>
<?php
include("../includes/menu_mess.php");
?>
