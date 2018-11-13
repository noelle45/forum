<?php

session_start();

$titre = "Forum girly";
include("../includes/identifiants.php");
include("../includes/debut.php");
include("../includes/constants.php");
        // infos du membre
                $query=$db->prepare('SELECT membre_pseudo, membre_email,
                membre_siteweb, membre_signature, membre_msn, membre_localisation,
                membre_avatar, membre_rang
                FROM forum_membres WHERE membre_id=:id');
                $query->bindValue(':id',$id,PDO::PARAM_INT);
                $query->execute();
                $data=$query->fetch();

 
include("../includes/baniere-membres.php");
?>
	<div style="text-align: center">
<?php

if (isset($_SESSION['pseudo']))
{
        echo'&nbsp; &nbsp; <i><p class="p_baniere">Vous êtes ici : &nbsp; &nbsp; </i><a href ="accueil.php"> Accueil forum</a><br/><br/>';
        $totaldesmessages = 0;
        $categorie = NULL;
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

	
        <table class="table_shadow" style="background-color:white;width:80%;box-shadow:1px 2px 18px #A5A4A4;">
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
            //Contenu de chaque catégorie
            echo'<tr><td><img src="../img/icones/a_lire.gif" alt="message" /></td>
            <td class="titre"><strong>
            <a href="../forum/voirforum.php?f='.$data['forum_id'].'">'.stripslashes(htmlspecialchars($data['forum_name'])).'</a></strong>
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
                 '.date('H\hi \l\e d/M/Y',$data['post_time']).' <a href="../forum/voirtopic.php?t='.$data['topic_id'].'&amp;page='.$page.'#p_'.$data['post_id'].'">
                 <img src="../img/icones/fleche_suivante_petit.png" alt="go" height="40px"/><br/>
                 Posté par : <a href="voirprofil.php?m='.stripslashes(htmlspecialchars($data['membre_id'])).'&amp;action=consulter">'.$data['membre_pseudo'].'</a>
                 </a></td></tr>';
             }
             else
             {
                 echo'<td class="nombremessages">Pas de message</td></tr>';
             }
             //Cette variable stock le nombre de messages, on la met à jour
             $totaldesmessages += $data['forum_post'];
             //On ferme notre boucle et nos balises
        } //fin de la boucle
        $query->CloseCursor();
            ?></table>
		</div>
            </div> <?php
    
include("../includes/footer_accueil.php");
}
?>
