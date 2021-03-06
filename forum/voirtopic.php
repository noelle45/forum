<?php
session_start();
$titre="Voir un sujet";
include("../includes/identifiants.php");
include("../includes/debut.php");
include("../includes/bbcode.php");
include("../includes/baniere-forum.php");

 
//On récupère la valeur de t
$topic = (int) $_GET['t'];
 
//A partir d'ici, on va compter le nombre de messages pour n'afficher que les 15 premiers
$query=$db->prepare('SELECT topic_titre, topic_post, forum_topic.forum_id, topic_last_post,
forum_name, auth_view, auth_topic, auth_post 
FROM forum_topic 
LEFT JOIN forum_forum ON forum_topic.forum_id = forum_forum.forum_id 
WHERE topic_id = :topic');
$query->bindValue(':topic',$topic,PDO::PARAM_INT);
$query->execute();
$data=$query->fetch();
$forum=$data['forum_id']; 
$totalDesMessages = $data['topic_post'] + 1;
$nombreDeMessagesParPage = 15;
$nombreDePages = ceil($totalDesMessages / $nombreDeMessagesParPage);
?>

<?php
echo '<p>&nbsp; &nbsp; <i>Vous êtes ici</i> : &nbsp; &nbsp; <a href="../membres/accueil.php">Index du forum</a>  &nbsp; &nbsp;  <a href="./voirforum.php?f='.$forum.'">'.stripslashes(htmlspecialchars($data['forum_name'])).'</a>  &nbsp; &nbsp;  <a href="./voirtopic.php?t='.$topic.'">'.stripslashes(htmlspecialchars($data['topic_titre'])).'</a>';
?>

<?php
//Nombre de pages
$page = (isset($_GET['page']))?intval($_GET['page']):1;
 
$premierMessageAafficher = ($page - 1) * $nombreDeMessagesParPage;

$query->CloseCursor(); 

$query=$db->prepare('SELECT post_id , post_createur , post_texte , post_time ,
membre_id, membre_pseudo, membre_inscrit, membre_avatar, membre_localisation, membre_post, membre_signature, membre_rang
FROM forum_post
LEFT JOIN forum_membres ON forum_membres.membre_id = forum_post.post_createur
WHERE topic_id =:topic
ORDER BY post_id
LIMIT :premier, :nombre');
$query->bindValue(':topic',$topic,PDO::PARAM_INT);
$query->bindValue(':premier',(int) $premierMessageAafficher,PDO::PARAM_INT);
$query->bindValue(':nombre',(int) $nombreDeMessagesParPage,PDO::PARAM_INT);
$query->execute();
 
//On vérifie que la requête a bien retourné des messages
if ($query->rowCount()<1)
{
        echo'<p>Il n y a aucun post sur ce topic, vérifiez l\'url et réessayez</p>';
}
else
{
        //Si tout roule on affiche notre tableau puis on remplit avec une boucle
        ?>
		<br/>
		  <table>
        <tr>
        <th class="vt_auteur"><strong>Auteurs</strong></th>             
        <th class="vt_mess"><strong>Messages</strong></th>       
        </tr>
		<br/>
        <?php
        while ($data = $query->fetch())
        {

				//On vérifie les droits du membre
				//(partie du code commentée plus tard)
				echo'<tr><td class="td_titre"><strong>
				<a href="../membres/voirprofil.php?m='.$data['membre_id'].'&amp;action=consulter">
				'.stripslashes(htmlspecialchars($data['membre_pseudo'])).'</a></strong></td>';


				if($id == $data['post_createur'] || $_SESSION['id'] == 1)
         {
				echo'<td class="td_titre" id=p_'.$data['post_id'].'>Posté à '.date('H\hi \l\e d M y',$data['post_time']).'
				<a href="poster.php?p='.$data['post_id'].'&amp;action=delete">
				<img src="../img/icones/delete.gif" alt="Supprimer" title="Supprimer ce message" /></a>
				
				<a href="poster.php?p='.$data['post_id'].'&amp;action=edit">
				<img src="../img/icones/editer.gif" alt="Editer" title="Editer ce message" /></a></td></tr>';
         }
         else
         {
				echo'<td class="td_titre">
				Posté à '.date('H\hi \l\e d M y',$data['post_time']).'
				</td></tr>';
         }
       
         //Détails sur le membre qui a posté
         echo'<tr><td>';
        
            if(!empty($data['membre_avatar'])){
                echo '<img src="../membres/avatars/'.$data['membre_avatar'].'" alt="Avatar" height="60px" />';}
            else{
                echo '<img src="../membres/avatars/compte100.png" alt="Avatar" height="60px"/>';}
         
         echo '<br />Membre inscrit le '.date('d/m/Y',$data['membre_inscrit']).'
         <br />Messages : '.$data['membre_post'].'<br />
         Localisation : '.stripslashes(htmlspecialchars($data['membre_localisation'])).'</td>';
               
         //Message
         echo'<td>'.code(nl2br(stripslashes(htmlspecialchars($data['post_texte'])))).'
         <br /><hr />'.code(nl2br(stripslashes(htmlspecialchars($data['membre_signature'])))).'</td></tr>';
         }
         $query->CloseCursor();

         ?>
</table>
<?php
	
if(isset($_SESSION['pseudo'])){
	$query=$db->prepare('SELECT topic_locked FROM forum_topic WHERE topic_id = :topic');
	$query->bindValue(':topic',$topic,PDO::PARAM_INT);
	$query->execute(); 
	$data=$query->fetch();
	echo '<h2 style="text-align:center">Ce topic est cloturé</h2>';
	echo '<p style="text-align:center"><a href="../membres/accueil.php">Retour</a></p>,';
	if($data['topic_locked'] != 0){
	echo '<h1>';
	echo'<br/><a href="poster.php?action=repondre&amp;t='.$topic.'"><img src="../img/icones/repondre.gif" alt="Répondre" title="Répondre à ce topic"/></a>&nbsp;&nbsp;<a href="poster.php?action=nouveautopic&amp;f='.$forum.'"><img src="../img/icones/nouveau.gif" alt="Nouveau topic" title="Poster un nouveau topic" /></a></h1>';
	echo '</h1>';
	}
}
	
        echo '<p style="text-align:center">Page : ';
        for ($i = 1 ; $i <= $nombreDePages ; $i++)
        {
                if ($i == $page) //On affiche pas la page actuelle en lien
                {
                echo $i;
                }
                else
                {
                echo '<a href="voirtopic.php?t='.$topic.'&amp;page='.$i.'">
                ' . $i . '</a> ';
                }
        }
        echo'</p>';
       
        //On ajoute 1 au nombre de visites de ce topic
        $query=$db->prepare('UPDATE forum_topic
        SET topic_vu = topic_vu + 1 WHERE topic_id = :topic');
        $query->bindValue(':topic',$topic,PDO::PARAM_INT);
        $query->execute();
        $query->CloseCursor();

} //Fin du if qui vérifiait si le topic contenait au moins un message
$query=$db->prepare('SELECT membre_rang
FROM forum_membres WHERE membre_id=:id');
$query->bindValue(':id',$id,PDO::PARAM_INT);
$query->execute();
$data=$query->fetch();
$query=$db->prepare('SELECT forum_id, forum_name FROM forum_forum WHERE forum_id <> :forum');
$query->bindValue(':forum',$forum,PDO::PARAM_INT);
$query->execute();

//****************************************PARTI ADMIN***********************************************

if($data['membre_rang']>=3)
?> <hr> <?php
echo'<br/><h2 style="text-align:center">Admistration du Topic</h2>';
{
echo'<table>
<tr>
<th> <p>Déplacer vers :</p> </th>
<th> <p>Etat du forum :</p> </th>
<th> <p>Réponse automatique :</p> </th>
</tr>
<td>
<form method="post" action=postok.php?action=deplacer&amp;t='.$topic.'>
<select name="dest">';               
while($data=$query->fetch())
{
     echo'<option value='.$data['forum_id'].' id='.$data['forum_id'].'>'.$data['forum_name'].'</option>';
}
$query->CloseCursor();
echo'
</select>
<input type="hidden" name="from" value='.$forum.'>
<input type="submit" name="submit" value="Envoyer" />
</form>';
echo'</td>';
 //***************************************************************
echo'<td>';
$query = $db->prepare('SELECT topic_locked FROM forum_topic WHERE topic_id = :topic');
$query->bindValue(':topic',$topic,PDO::PARAM_INT);
$query->execute();
$data=$query->fetch();

if ($data['topic_locked'] == 1) // Topic verrouillé !
{
    echo'<h3 style="text-align:center;color:black"><a href="./postok.php?action=unlock&t='.$topic.'">
    <img src="img/lock.png" alt="deverrouiller" title="Déverrouiller ce sujet" /><br/>Clôturé</a></h3><br/>';
}
else //Sinon le topic est déverrouillé !
{
    echo'<h3 style="text-align:center;color:black"><a href="./postok.php?action=lock&amp;t='.$topic.'"><br/>
    <img src="img/unlock.png" alt="verrouiller" title="Verrouiller ce sujet" /><br/>Ouvert</a></h3>';
}
}
$query->CloseCursor();
echo'</td>';
//**********************************************************************
echo'<td>
<form method="post" action=postok.php?action=autorep&amp;t='.$topic.'>
<select name="rep">';
$query=$db->query('SELECT automess_id, automess_titre
FROM forum_automess');
while ($data = $query->fetch())
{
     echo '<option value="'.$data['automess_id'].'">
     '.$data['automess_titre'].'</option>';
}
echo '</select>  
<input type="submit" name="submit" value="Envoyer" /></form>';
$query->CloseCursor();
echo'</td>
</tr>
</table>';

include("../includes/footer_membres.php");
?>
       
</div>
</body>
</html>

