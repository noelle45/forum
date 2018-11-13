<?php
session_start();
$titre="Poster";
$balises = true;
include("../includes/identifiants.php");
include("../includes/debut.php");
include("../includes/bbcode.php");
include("../includes/baniere-forum.php");

?> <div style="text-align:center"><?php

$action = (isset($_GET['action']))?htmlspecialchars($_GET['action']):'';
if ($id==0) erreur(ERR_IS_CO);
if (isset($_GET['f']))
{
    $forum = (int) $_GET['f'];
    $query= $db->prepare('SELECT forum_name, forum_id, auth_view, auth_post, auth_topic, auth_annonce, auth_modo
    FROM forum_forum WHERE forum_id =:forum');
    $query->bindValue(':forum',$forum,PDO::PARAM_INT);
    $query->execute();
    $data=$query->fetch();
    echo '<p>&nbsp; &nbsp;<i>Vous êtes ici</i> : &nbsp; &nbsp; <a href="../membres/accueil.php">Index du forum</a>  &nbsp; &nbsp;  
    <a href="voirforum.php?f='.$data['forum_id'].'">'.stripslashes(htmlspecialchars($data['forum_name'])).'</a>  &nbsp; &nbsp; Nouveau topic</p>';
}

elseif (isset($_GET['t']))
{
    $topic = (int) $_GET['t'];
    $query=$db->prepare('SELECT topic_titre, forum_topic.forum_id,
    forum_name, auth_view, auth_post, auth_topic, auth_annonce, auth_modo
    FROM forum_topic
    LEFT JOIN forum_forum ON forum_forum.forum_id = forum_topic.forum_id
    WHERE topic_id =:topic');
    $query->bindValue(':topic',$topic,PDO::PARAM_INT);
    $query->execute();
    $data=$query->fetch();
    $forum = $data['forum_id'];  

    echo '<p><i>Vous êtes ici</i> : <a href="../membres/accueil.php">Index du forum</a>  &nbsp; &nbsp; 
    <a href="voirforum.php?f='.$data['forum_id'].'">'.stripslashes(htmlspecialchars($data['forum_name'])).'</a>
    <a href="voirtopic.php?t='.$topic.'">'.stripslashes(htmlspecialchars($data['topic_titre'])).'</a>  &nbsp; &nbsp; Répondre</p>';
}

elseif (isset ($_GET['p']))
{
    $post = (int) $_GET['p'];
    $query=$db->prepare('SELECT post_createur, forum_post.topic_id, topic_titre, forum_topic.forum_id,
    forum_name, auth_view, auth_post, auth_topic, auth_annonce, auth_modo
    FROM forum_post
    LEFT JOIN forum_topic ON forum_topic.topic_id = forum_post.topic_id
    LEFT JOIN forum_forum ON forum_forum.forum_id = forum_topic.forum_id
    WHERE forum_post.post_id =:post');
    $query->bindValue(':post',$post,PDO::PARAM_INT);
    $query->execute();
    $data=$query->fetch();

    $topic = $data['topic_id'];
    $forum = $data['forum_id'];
 
    echo '<p><i>Vous êtes ici</i> : <a href="../membres/accueil.php">Index du forum</a>  &nbsp; &nbsp; <a href="voirforum.php?f='.$data['forum_id'].'">'.stripslashes(htmlspecialchars($data['forum_name'])).'</a>  &nbsp; &nbsp; <a href="voirtopic.php?t='.$topic.'">'.stripslashes(htmlspecialchars($data['topic_titre'])).'</a>  &nbsp; &nbsp; Modérer un message</p>';
}
$query->CloseCursor();  



switch($action)
{
	case "repondre":
	?>
	
	<form method="post" action="postok.php?action=repondre&amp;t=<?php echo $topic ?>" name="formulaire">

	<fieldset><p style="color:black;text-align:left">
	<input type="button" id="gras" name="gras" value="Gras" onClick="javascript:bbcode('[g]', '[/g]');return(false)" />
	<input type="button" id="italic" name="italic" value="Italic" onClick="javascript:bbcode('[i]', '[/i]');return(false)" />
	<input type="button" id="souligné" name="souligné" value="Souligné" onClick="javascript:bbcode('[s]', '[/s]');return(false)" />
	<input type="button" id="lien" name="lien" value="Lien" onClick="javascript:bbcode('[url]', '[/url]');return(false)" />
	 &nbsp; &nbsp; 
	<img src="img/heureux.png" title="heureux" alt="heureux" onClick="javascript:smilies(' :D ');return(false)" />
	<img src="img/smile.png" title="lol" alt="lol" onClick="javascript:smilies(' :lol: ');return(false)" />
	<img src="img/triste.png" title="triste" alt="triste" onClick="javascript:smilies(' :triste: ');return(false)" />
	<img src="img/cool.png" title="cool" alt="cool" onClick="javascript:smilies(' :frime: ');return(false)" />
	<img src="img/langue.png" title="rire" alt="rire" onClick="javascript:smilies(' XD ');return(false)" />
	<img src="img/huh.png" title="confus" alt="confus" onClick="javascript:smilies(' :s ');return(false)" />
	<img src="img/choc.png" title="choc" alt="choc" onClick="javascript:smilies(' :o ');return(false)" />
	<img src="img/point-int.gif" title="?" alt="?" onClick="javascript:smilies(' :interrogation: ');return(false)" />
	<img src="img/pirate.png" title="!" alt="!" onClick="javascript:smilies(' :exclamation: ');return(false)" />
	<br/>
	<p style="color:black; text-align:left">Message<br/>
	<textarea cols="80" rows="8" id="message" name="message"></textarea><br/><br/>
    	<input type="submit" name="submit" value="Envoyer" />
	<input type="reset" name = "Effacer" value = "Effacer"/></p>   
    	</fieldset>
 	</form>
	<?php
break;
 
	case "nouveautopic":
        
	?>
	<form method="post" action="postok.php?action=nouveautopic&amp;f=<?php echo $forum ?>" name="formulaire">

	<fieldset><p style="color:black;text-align:left">Titre<br/>
	<input type="text" size="30" id="titre" name="titre" /><br/><br/>
	<input type="button" id="gras" name="gras" value="Gras" onClick="javascript:bbcode('[g]', '[/g]');return(false)" />
	<input type="button" id="italic" name="italic" value="Italic" onClick="javascript:bbcode('[i]', '[/i]');return(false)" />
	<input type="button" id="souligné" name="souligné" value="Souligné" onClick="javascript:bbcode('[s]', '[/s]');return(false)" />
	<input type="button" id="lien" name="lien" value="Lien" onClick="javascript:bbcode('[url]', '[/url]');return(false)" />
	 &nbsp; &nbsp; 
	<img src="img/heureux.png" title="heureux" alt="heureux" onClick="javascript:smilies(' :D ');return(false)" />
	<img src="img/smile.png" title="lol" alt="lol" onClick="javascript:smilies(' :lol: ');return(false)" />
	<img src="img/triste.png" title="triste" alt="triste" onClick="javascript:smilies(' :triste: ');return(false)" />
	<img src="img/cool.png" title="cool" alt="cool" onClick="javascript:smilies(' :frime: ');return(false)" />
	<img src="img/langue.png" title="rire" alt="rire" onClick="javascript:smilies(' XD ');return(false)" />
	<img src="img/huh.png" title="confus" alt="confus" onClick="javascript:smilies(' :s ');return(false)" />
	<img src="img/choc.png" title="choc" alt="choc" onClick="javascript:smilies(' :o ');return(false)" />
	<img src="img/point-int.gif" title="?" alt="?" onClick="javascript:smilies(' :interrogation: ');return(false)" />
	<img src="img/pirate.png" title="!" alt="!" onClick="javascript:smilies(' :exclamation: ');return(false)" /><br/>
   	<p style="color:black;text-align:left">Message<br/>
	<textarea cols="80" rows="8" id="message" name="message"></textarea></p><br/>
        <?php
        $query=$db->prepare('SELECT membre_rang
                FROM forum_membres WHERE membre_id=:id');
                $query->bindValue(':id',$id,PDO::PARAM_INT);
                $query->execute();
                $data=$query->fetch();
        if ($data['membre_rang'] >=3){
        ?>     
	<label><input type="radio" name="mess" value="Annonce" />Annonce</label>       
        <?php
        }
        ?>
	<label><input type="radio" name="mess" value="Message" checked="checked" />Topic</label><br/><br/>
	<input type="submit" name="submit" value="Envoyer" />
	<input type="reset" name = "Effacer" value = "Effacer" />
	</fieldset>
	</form>

	<?php
	break;
        
        case "edit":
            $post = (int) $_GET['p'];

            $query=$db->prepare('SELECT post_createur, post_texte, auth_modo FROM forum_post
            LEFT JOIN forum_forum ON forum_post.post_forum_id = forum_forum.forum_id
            WHERE post_id=:post');
            $query->bindValue(':post',$post,PDO::PARAM_INT);
            $query->execute();
            $data=$query->fetch();

            $text_edit = $data['post_texte'];

            if($data['post_createur'] != $id)
            {
                erreur(ERR_AUTH_EDIT);
            }
            else 
            {
                ?>
                <form method="post" action="postok.php?action=edit&amp;p=<?php echo $post ?>" name="formulaire">
                <fieldset>
                <input type="button" id="gras" name="gras" value="Gras" onClick="javascript:bbcode('[g]', '[/g]');return(false)" />
                <input type="button" id="italic" name="italic" value="Italic" onClick="javascript:bbcode('[i]', '[/i]');return(false)" />
                <input type="button" id="souligné" name="souligné" value="Souligné" onClick="javascript:bbcode('[s]', '[/s]');return(false)"/>
                <input type="button" id="lien" name="lien" value="Lien" onClick="javascript:bbcode('[url]', '[/url]');return(false)" />
                 &nbsp; &nbsp;
                <img src="img/heureux.png" title="heureux" alt="heureux" onClick="javascript:smilies(' :D ');return(false)" />
                <img src="img/smile.png" title="lol" alt="lol" onClick="javascript:smilies(' :lol: ');return(false)" />
                <img src="img/triste.png" title="triste" alt="triste" onClick="javascript:smilies(' :triste: ');return(false)" />
                <img src="img/cool.png" title="cool" alt="cool" onClick="javascript:smilies(' :frime: ');return(false)" />
                <img src="img/langue.png" title="rire" alt="rire" onClick="javascript:smilies(' XD ');return(false)" />
                <img src="img/huh.png" title="confus" alt="confus" onClick="javascript:smilies(' :s ');return(false)" />
                <img src="img/choc.png" title="choc" alt="choc" onClick="javascript:smilies(' :o ');return(false)" />
                <img src="img/point-int.gif" title="?" alt="?" onClick="javascript:smilies(' :interrogation: ');return(false)" />
                <img src="img/pirate.png" title="!" alt="!" onClick="javascript:smilies(' :exclamation: ');return(false)" />
               

                <fieldset><p style="color:black;text-align:left">Message</p>
		<textarea cols="80" rows="8" id="message" name="message"><?php echo $text_edit ?>
                </textarea><br/>
                <br/><input type="submit" name="submit" value="Editer !" />
                <input type="reset" name = "Effacer" value = "Effacer"/>
                </fieldset>
                </form>
                <?php
            }
        break;
        
        case "delete": 
            $post = (int) $_GET['p'];
            echo'<h1>Suppression</h1>';
            $query=$db->prepare('SELECT post_createur, auth_modo
            FROM forum_post
            LEFT JOIN forum_forum ON forum_post.post_forum_id = forum_forum.forum_id
            WHERE post_id= :post');
            $query->bindValue(':post',$post,PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetch();

            if (!verif_auth($data['auth_modo']) && $data['post_createur'] != $id)
            {
                erreur(ERR_AUTH_DELETE); 
            }
            else
            {
                echo'<p>Êtes vous certains de vouloir supprimer ce post ?</p>';
                echo'<p><a href="./postok.php?action=delete&amp;p='.$post.'">Oui</a> ou <a href="./index.php">Non</a></p>';
            }
            $query->CloseCursor();
        break;
		
	

default:
echo'<p>Cette action est impossible</p>';
}
?>
</div>
</div>
</body>
</html>


