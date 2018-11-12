<div class="include_fixed">

<?php
    
    if(isset($_SESSION['pseudo'])){
        echo '<p class="p_baniere"> <a href="voirprofil.php?m='.$_SESSION['id'].'&amp;action=consulter"> Mon compte '
        .htmlspecialchars($_SESSION['pseudo']).'</a>  &nbsp; &nbsp; &nbsp;  <a href="deconnexion.php" title="vous avez terminé votre visite ?"> Me deconnecter</a><p>';
        
        if(!empty($_SESSION['avatar'])){
            echo '<img src="../membres/avatars/'.$_SESSION['avatar'].'" alt="Avatar" />';}
        else{
           echo '<img src="../membres/avatars/compte100.png" alt="Avatar" />';}
       }
    else{
        echo '<h1>Bonjour et Bienvenue !<br/> vous n\'êtes pas connecté(e)</h1>
	         <p class="parag_centre"><a href="../index.php" title="Me connecter"> Me connecter</a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href="register.php" title="M\'inscrire"> M\'inscrire</a></p>';
    }
?>
</div>