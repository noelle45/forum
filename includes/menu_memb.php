<?php
echo ' <p class="p_menu">
<a href="../messagerie/amis.php"> Mes amis</a>&nbsp;&nbsp;  &nbsp;&nbsp; &nbsp;  	
<a href="../messagerie/messagerie.php" title="Messagerie"> Ma messagerie</a>&nbsp; &nbsp;  &nbsp; &nbsp; &nbsp;  	
<a href="../messagerie/minichat/index.php">Ouvrir le MiniChat</a>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 	
<a href="memberlist.php" title="Voir la liste des membres"> Liste des membres connectés</a> &nbsp; &nbsp; &nbsp; &nbsp;'; 

if ($data["membre_rang"]>=3){
echo'<a href="../admin/accueil-admin.php" title="Administartion du forum"> Administartion du forum</a>
</p>';}
?>
<hr>
