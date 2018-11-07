<?php
//Cette fonction doit être appelée avant tout code html
session_start();

$titre = "Accueil du forum";
include("includes/identifiants.php");
include("includes/debut.php");


if (!isset($_POST['pseudo'])) //On est dans la page de formulaire
{
	echo '<form method="post" action="membres/connexion.php">
	<fieldset class="field_constants">
	<legend>Connexion</legend>
	<p><label for="pseudo">Pseudo :</label><br/><input name="pseudo" type="text" id="pseudo" /><br />
	<label for="password">Mot de Passe :</label><br/><input type="password" name="password" id="password" />
	<input type="submit" value="Connexion" /></p></form>
	<p><a href="membres/register.php">Pas encore inscrit(e) ?</a></p>
	</fieldset>
	

	</div>
	</body>
	</html>';
}



if(isset($_SESSION['pseudo']))
{
echo'<i><p>Vous êtes ici : </i><a href ="./index.php">Accueil forum</a></p><br/>
<h1>Bonjour et Bienvenue '.htmlspecialchars($_SESSION['pseudo']).'!</h1>

<h4><a href="./voirprofil.php?m='.$_SESSION['id'].'&amp;action=consulter">Mon profil</a> - 
<a href="amis.php"> Mes amis</a> - 
<a href="messagesprives.php" title="Messagerie"> Ma messagerie</a> - 
<a href="deconnexion.php" title="vous avez terminé votre visite ?"> Me deconnecter</a><br/>
<a href="memberlist.php" title="Voir la liste des membres"> Liste des membres connectés</a><br/></h4>
';
}
else
{
	echo '<h1>Bonjour et Bienvenue !<br/> vous n\'êtes pas connecté(e)</h1><br/>';
}	
$query = $db->query('SELECT membre_pseudo, membre_id FROM forum_membres');
$data = $query->fetch();
$query->CloseCursor();
?>

<?php
//Initialisation de deux variables
$totaldesmessages = 0;
$categorie = NULL;
?>

<?php
//Cette requête permet d'obtenir tout sur le forum
$query=$db->prepare('SELECT cat_id, cat_nom, 
forum_forum.forum_id, forum_name, forum_desc, forum_post, forum_topic, auth_view, forum_topic.topic_id,  forum_topic.topic_post, post_id, post_time, post_createur, membre_pseudo, 
membre_id 
FROM forum_categorie
LEFT JOIN forum_forum ON forum_categorie.cat_id = forum_forum.forum_cat_id
LEFT JOIN forum_post ON forum_post.post_id = forum_forum.forum_last_post_id
LEFT JOIN forum_topic ON forum_topic.topic_id = forum_post.topic_id
LEFT JOIN forum_membres ON forum_membres.membre_id = forum_post.post_createur
WHERE auth_view <= :lvl 
ORDER BY cat_ordre, forum_ordre DESC');
$query->bindValue(':lvl',$lvl,PDO::PARAM_INT);
$query->execute();
?>

<table>

<?php
//Début de la boucle
while($data = $query->fetch())
{
    //On affiche chaque catégorie
    if( $categorie != $data['cat_id'] )
    {
        //Si c'est une nouvelle catégorie on l'affiche
       
        $categorie = $data['cat_id'];
?>
        <tr>
        <th></th>
        <th class="titre"><strong><?php echo stripslashes(htmlspecialchars($data['cat_nom'])); ?>
        </strong></th>             
        <th class="nombremessages"><strong>Sujets</strong></th>       
        <th class="nombresujets"><strong>Messages</strong></th>       
        <th class="derniermessage"><strong>Dernier message</strong></th>   
        </tr>
<?php
               
    }

if (verif_auth($data['auth_view']))
{
    // les forums en détail : description, nombre de réponses etc...

    echo'<tr><td><img src="img/icones/a_lire.gif" alt="message" /></td>
    <td class="titre"><strong>
    <a href="./voirforum.php?f='.$data['forum_id'].'">
    '.stripslashes(htmlspecialchars($data['forum_name'])).'</a></strong>
    <br />'.nl2br(stripslashes(htmlspecialchars($data['forum_desc']))).'</td>
    <td class="nombresujets">'.$data['forum_topic'].'</td>
    <td class="nombremessages">'.$data['forum_post'].'</td>';

    // Deux cas possibles :
    // Soit il y a un nouveau message, soit le forum est vide
    if (!empty($data['forum_post']))
    {
         //Selection dernier message
		 $nombreDeMessagesParPage = 15;
			 $nbr_post = $data['topic_post'] +1;
		 $page = ceil($nbr_post / $nombreDeMessagesParPage);
			 
         echo'<td class="derniermessage">
         '.date('H\hi \l\e d/M/Y',$data['post_time']).'<br />
         <a href="./voirprofil.php?m='.stripslashes(htmlspecialchars($data['membre_id'])).'&amp;action=consulter">'.$data['membre_pseudo'].'
         <a href="./voirtopic.php?t='.$data['topic_id'].'&amp;page='.$page.'#p_'.$data['post_id'].'">
         <br/><br/><img src="img/icones/fleche_suivante.png" alt="go" /> Voir les messages </a></td></tr>';

     }
     else
     {
         echo'<td class="nombremessages">Pas de message</td></tr>';
     }

     //Cette variable stock le nombre de messages, on la met à jour
     $totaldesmessages += $data['forum_post'];

}	
else
	{
		echo'<td class="nombremessages">Pas de message</td></tr>';
	}
	 //Fin de la vérification d'autorisation


} //fin de la boucle
	$query->CloseCursor();
	echo '</table></div>';
	?>
<?php
include("includes/footer_simple.php");
?>
