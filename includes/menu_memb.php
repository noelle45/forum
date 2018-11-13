<?php
if(isset($_SESSION["pseudo"])){
	echo ' <p class="p_menu">
	<a href="../membres/friends.php"> Mes amis</a>&nbsp;&nbsp;  &nbsp;&nbsp; &nbsp;  	
	<a href="../messagerie/messagerie.php" title="Messagerie"> Ma messagerie</a>&nbsp; &nbsp;  &nbsp; &nbsp; &nbsp;  	
	<a href="../messagerie/minichat/index.php">Ouvrir le MiniChat</a>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 	
	<a href="members-list.php" title="Voir la liste des membres"> Liste des membres connectés</a> &nbsp; &nbsp; &nbsp; &nbsp;';

	$query=$db->prepare('SELECT membre_pseudo, membre_email,
		        membre_siteweb, membre_signature, membre_msn, membre_localisation,
		        membre_avatar, membre_rang, membre_inscrit, membre_post
		        FROM forum_membres WHERE membre_id=:id');
		        $query->bindValue(':id',$id,PDO::PARAM_INT);
		        $query->execute();
		        $data=$query->fetch(); 

	if ($data["membre_rang"]>=3){
	echo'<a href="../admin/accueil-admin.php" title="Administartion du forum"> Administartion du forum</a>
	</p>';}
	}
?>
<hr>
