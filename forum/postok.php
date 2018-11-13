<?php
session_start();
$titre="Poster";
include("../includes/identifiants.php");
include("../includes/constants.php");
include("../includes/debut.php");

$action = (isset($_GET['action']))?htmlspecialchars($_GET['action']):'';

if ($id==0) erreur(ERR_IS_CO);


switch($action)
{
    case "nouveautopic":
    $message = $_POST['message'];
    $mess = $_POST['mess'];

    $titre = $_POST['titre'];

    $forum = (int) $_GET['f'];
    $temps = time();

    if (empty($message) || empty($titre))
    {
        echo'<p>Votre message ou votre titre est vide, 
        cliquez <a href="poster.php?action=nouveautopic&amp;f='.$forum.'">ici</a> pour recommencer</p>';
    }
    else 
    {
        $query=$db->prepare('INSERT INTO forum_topic
        (forum_id, topic_titre, topic_createur, topic_vu, topic_time, topic_genre)
        VALUES(:forum, :titre, :id, 1, :temps, :mess)');
        $query->bindValue(':forum', $forum, PDO::PARAM_INT);
        $query->bindValue(':titre', $titre, PDO::PARAM_STR);
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->bindValue(':temps', $temps, PDO::PARAM_INT);
        $query->bindValue(':mess', $mess, PDO::PARAM_STR);
        $query->execute();


        $nouveautopic = $db->lastInsertId();
        $query->CloseCursor(); 

        $query=$db->prepare('INSERT INTO forum_post
        (post_createur, post_texte, post_time, topic_id, post_forum_id)
        VALUES (:id, :mess, :temps, :nouveautopic, :forum)');
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->bindValue(':mess', $message, PDO::PARAM_STR);
        $query->bindValue(':temps', $temps,PDO::PARAM_INT);
        $query->bindValue(':nouveautopic', (int) $nouveautopic, PDO::PARAM_INT);
        $query->bindValue(':forum', $forum, PDO::PARAM_INT);
        $query->execute();

        $nouveaupost = $db->lastInsertId();
        $query->CloseCursor(); 

        $query=$db->prepare('UPDATE forum_topic
        SET topic_last_post = :nouveaupost,
        topic_first_post = :nouveaupost
        WHERE topic_id = :nouveautopic');
        $query->bindValue(':nouveaupost', (int) $nouveaupost, PDO::PARAM_INT);    
        $query->bindValue(':nouveautopic', (int) $nouveautopic, PDO::PARAM_INT);
        $query->execute();
        $query->CloseCursor();

        $query=$db->prepare('UPDATE forum_forum SET forum_post = forum_post + 1 ,forum_topic = forum_topic + 1, 
        forum_last_post_id = :nouveaupost
        WHERE forum_id = :forum');
        $query->bindValue(':nouveaupost', (int) $nouveaupost, PDO::PARAM_INT);    
        $query->bindValue(':forum', (int) $forum, PDO::PARAM_INT);
        $query->execute();
        $query->CloseCursor();
    
        $query=$db->prepare('UPDATE forum_membres SET membre_post = membre_post + 1 WHERE membre_id = :id');
        $query->bindValue(':id', $id, PDO::PARAM_INT);    
        $query->execute();
        $query->CloseCursor();

        echo '<p class="p_message"> Votre message a bien été envoyé <br/> <a href="../membres/accueil.php"> Retour accueil  </p>';
        header('location : ../membres/accueil');

    }
    break;

    case "repondre":
    $message = $_POST['message'];
    $topic = (int) $_GET['t'];
        
            $query=$db->prepare('SELECT topic_locked FROM forum_topic WHERE topic_id = :topic');
            $query->bindValue(':topic',$topic,PDO::PARAM_INT);
            $query->execute(); 
            $data=$query->fetch();
            if ($data['topic_locked'] != 0)
            {
                echo 'Ce topic est cloturé';
            }
            $query->CloseCursor();
        
    $temps = time();

    if (empty($message))
    {
        echo'<p>Votre message est vide, cliquez <a href="poster.php?action=repondre&amp;t='.$topic.'">ici</a> pour recommencer</p>';
    }
    else
    {

        $query=$db->prepare('SELECT forum_id, topic_post FROM forum_topic WHERE topic_id = :topic');
        $query->bindValue(':topic', $topic, PDO::PARAM_INT);    
        $query->execute();
        $data=$query->fetch();
        $forum = $data['forum_id'];

        $query=$db->prepare('INSERT INTO forum_post
        (post_createur, post_texte, post_time, topic_id, post_forum_id)
        VALUES(:id,:mess,:temps,:topic,:forum)');
        $query->bindValue(':id', $id, PDO::PARAM_INT);   
        $query->bindValue(':mess', $message, PDO::PARAM_STR);  
        $query->bindValue(':temps', $temps, PDO::PARAM_INT);  
        $query->bindValue(':topic', $topic, PDO::PARAM_INT);   
        $query->bindValue(':forum', $forum, PDO::PARAM_INT); 
        $query->execute();

        $nouveaupost = $db->lastInsertId();
        $query->CloseCursor(); 


        $query=$db->prepare('UPDATE forum_topic SET topic_post = topic_post + 1, topic_last_post = :nouveaupost WHERE topic_id =:topic');
        $query->bindValue(':nouveaupost', (int) $nouveaupost, PDO::PARAM_INT);   
        $query->bindValue(':topic', (int) $topic, PDO::PARAM_INT); 
        $query->execute();
        $query->CloseCursor(); 


        $query=$db->prepare('UPDATE forum_forum SET forum_post = forum_post + 1 , forum_last_post_id = :nouveaupost WHERE forum_id = :forum');
        $query->bindValue(':nouveaupost', (int) $nouveaupost, PDO::PARAM_INT);   
        $query->bindValue(':forum', (int) $forum, PDO::PARAM_INT); 
        $query->execute();
        $query->CloseCursor(); 

        $query=$db->prepare('UPDATE forum_membres SET membre_post = membre_post + 1 WHERE membre_id = :id');
        $query->bindValue(':id', $id, PDO::PARAM_INT); 
        $query->execute();
        $query->CloseCursor(); 


        $nombreDeMessagesParPage = 15;
        $nbr_post = $data['topic_post']+1;
        $page = ceil($nbr_post / $nombreDeMessagesParPage);
        header("Location: ../membres/accueil.php");
    }
    break;
        
        case "edit": 
            $post = (int) $_GET['p'];


            $message = $_POST['message'];


            $query=$db->prepare('SELECT post_createur, post_texte, post_time, topic_id, auth_modo
            FROM forum_post
            LEFT JOIN forum_forum ON forum_post.post_forum_id = forum_forum.forum_id
            WHERE post_id=:post');
            $query->bindValue(':post',$post,PDO::PARAM_INT);
            $query->execute();
            $data1 = $query->fetch();
            $topic = $data1['topic_id'];


            $query = $db->prepare('SELECT COUNT(*) AS nbr FROM forum_post 
            WHERE topic_id = :topic AND post_time < '.$data1['post_time']);
            $query->bindValue(':topic',$topic,PDO::PARAM_INT);
            $query->execute();
            $data2=$query->fetch();

            if (!verif_auth($data1['auth_modo'])&& $data1['post_createur'] != $id)
            {

                erreur(ERR_AUTH_EDIT);    
            }
            else
            {
                $query=$db->prepare('UPDATE forum_post SET post_texte =  :message WHERE post_id = :post');
                $query->bindValue(':message',$message,PDO::PARAM_STR);
                $query->bindValue(':post',$post,PDO::PARAM_INT);
                $query->execute();
                $nombreDeMessagesParPage = 15;
                $nbr_post = $data2['nbr']+1;
                $page = ceil($nbr_post / $nombreDeMessagesParPage);
                echo'<p>Votre message a bien été édité!<br /><br />
                
                Cliquez <a href="./voirtopic.php?t='.$topic.'&amp;page='.$page.'#p_'.$post.'">ici</a> pour le voir</p>';
                $query->CloseCursor();
            }
        break;
        
        case "delete":
            $post = (int) $_GET['p'];
            $query=$db->prepare('SELECT post_createur, post_texte, forum_id, topic_id, auth_modo
            FROM forum_post
            LEFT JOIN forum_forum ON forum_post.post_forum_id = forum_forum.forum_id
            WHERE post_id=:post');
            $query->bindValue(':post',$post,PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetch();
            $topic = $data['topic_id'];
            $forum = $data['forum_id'];
            $poster = $data['post_createur'];

            if (!verif_auth($data['auth_modo']) && $poster != $id)
            {
                erreur(ERR_AUTH_DELETE); 
            }
            else
            {

                $query = $db->prepare('SELECT topic_first_post, topic_last_post FROM forum_topic
                WHERE topic_id = :topic');
                $query->bindValue(':topic',$topic,PDO::PARAM_INT);
                $query->execute();
                $data_post=$query->fetch();

                if ($data_post['topic_first_post']==$post) 
                {

                    if (!verif_auth($data['auth_modo']))
                    {
                        erreur('ERR_AUTH_DELETE_TOPIC');
                    }

                    echo'<p>Vous avez choisi de supprimer un post.
                    Cependant ce post est le premier du topic. Voulez vous supprimer le topic ? <br />
                    <a href="./postok.php?action=delete_topic&amp;t='.$topic.'">oui</a> - <a href="./voirtopic.php?t='.$topic.'">non</a>
                    </p>';
                    $query->CloseCursor();                     
                }
                elseif ($data_post['topic_last_post']==$post)
                {
                    $query=$db->prepare('DELETE FROM forum_post WHERE post_id = :post');
                    $query->bindValue(':post',$post,PDO::PARAM_INT);
                    $query->execute();
                    $query->CloseCursor();

                    $query=$db->prepare('SELECT post_id FROM forum_post WHERE topic_id = :topic 
                    ORDER BY post_id DESC LIMIT 0,1');
                    $query->bindValue(':topic',$topic,PDO::PARAM_INT);
                    $query->execute();
                    $data=$query->fetch();             
                    $last_post_topic=$data['post_id'];
                    $query->CloseCursor();

                    $query=$db->prepare('SELECT post_id FROM forum_post WHERE post_forum_id = :forum
                    ORDER BY post_id DESC LIMIT 0,1');
                    $query->bindValue(':forum',$forum,PDO::PARAM_INT);
                    $query->execute();
                    $data=$query->fetch();             
                    $last_post_forum=$data['post_id'];
                    $query->CloseCursor();   

                    $query=$db->prepare('UPDATE forum_topic SET topic_last_post = :last
                    WHERE topic_last_post = :post');
                    $query->bindValue(':last',$last_post_topic,PDO::PARAM_INT);
                    $query->bindValue(':post',$post,PDO::PARAM_INT);
                    $query->execute();
                    $query->CloseCursor();

                    $query=$db->prepare('UPDATE forum_forum SET forum_post = forum_post - 1, forum_last_post_id = :last
                    WHERE forum_id = :forum');
                    $query->bindValue(':last',$last_post_forum,PDO::PARAM_INT);
                    $query->bindValue(':forum',$forum,PDO::PARAM_INT);
                    $query->execute();
                    $query->CloseCursor(); 

                    $query=$db->prepare('UPDATE forum_topic SET  topic_post = topic_post - 1
                    WHERE topic_id = :topic');
                    $query->bindValue(':topic',$topic,PDO::PARAM_INT);
                    $query->execute();
                    $query->CloseCursor(); 

                    $query=$db->prepare('UPDATE forum_membres SET  membre_post = membre_post - 1
                    WHERE membre_id = :id');
                    $query->bindValue(':id',$poster,PDO::PARAM_INT);
                    $query->execute();
                    $query->CloseCursor();  

                    echo'<p>Le message a bien été supprimé !<br />
                    Cliquez <a href="./voirtopic.php?t='.$topic.'">ici</a> pour retourner au topic<br />
                    Cliquez <a href="./index.php">ici</a> pour revenir à l index du forum</p>';

                }
                else 
                {
                    $query=$db->prepare('DELETE FROM forum_post WHERE post_id = :post');
                    $query->bindValue(':post',$post,PDO::PARAM_INT);
                    $query->execute();
                    $query->CloseCursor();

                    $query=$db->prepare('UPDATE forum_forum SET forum_post = forum_post - 1  WHERE forum_id = :forum');
                    $query->bindValue(':forum',$forum,PDO::PARAM_INT);
                    $query->execute();
                    $query->CloseCursor(); 

                    $query=$db->prepare('UPDATE forum_topic SET  topic_post = topic_post - 1
                    WHERE topic_id = :topic');
                    $query->bindValue(':topic',$topic,PDO::PARAM_INT);
                    $query->execute();
                    $query->CloseCursor(); 

                    $query=$db->prepare('UPDATE forum_membres SET  membre_post = membre_post - 1
                    WHERE membre_id = :id');
                    $query->bindValue(':id',$data['post_createur'],PDO::PARAM_INT);
                    $query->execute();
                    $query->CloseCursor();  

                    echo'<p>Le message a bien été supprimé !<br />
                    Cliquez <a href="./voirtopic.php?t='.$topic.'">ici</a> pour retourner au topic<br />
                    Cliquez <a href="./index.php">ici</a> pour revenir à l index du forum</p>';
                }

            }
        break;
        
        case "delete_topic":
                $topic = (int) $_GET['t'];
                $query=$db->prepare('SELECT forum_topic.forum_id, auth_modo
                FROM forum_topic
                LEFT JOIN forum_forum ON forum_topic.forum_id = forum_forum.forum_id
                WHERE topic_id=:topic');
                $query->bindValue(':topic',$topic,PDO::PARAM_INT);
                $query->execute();
                $data = $query->fetch();
                $forum = $data['forum_id'];

                if (!verif_auth($data['auth_modo']))
                {
                    erreur('ERR_AUTH_DELETE_TOPIC');
                }
                else
                {
                    $query->CloseCursor();

                    $query=$db->prepare('SELECT topic_post FROM forum_topic WHERE topic_id = :topic');
                    $query->bindValue(':topic',$topic,PDO::PARAM_INT);
                    $query->execute();
                    $data = $query->fetch();
                    $nombrepost = $data['topic_post'] + 1;
                    $query->CloseCursor();

                    $query=$db->prepare('DELETE FROM forum_topic
                    WHERE topic_id = :topic');
                    $query->bindValue(':topic',$topic,PDO::PARAM_INT);
                    $query->execute();
                    $query->CloseCursor();

                    $query=$db->prepare('SELECT post_createur, COUNT(*) AS nombre_mess FROM forum_post
                    WHERE topic_id = :topic GROUP BY post_createur');
                    $query->bindValue(':topic',$topic,PDO::PARAM_INT);
                    $query->execute();

                    while($data = $query->fetch())
                    {
                        $query=$db->prepare('UPDATE forum_membres
                        SET membre_post = membre_post - :mess
                        WHERE membre_id = :id');
                        $query->bindValue(':mess',$data['nombre_mess'],PDO::PARAM_INT);
                        $query->bindValue(':id',$data['post_createur'],PDO::PARAM_INT);
                        $query->execute();
                    }

                    $query->CloseCursor();       
                    $query=$db->prepare('DELETE FROM forum_post WHERE topic_id = :topic');
                    $query->bindValue(':topic',$topic,PDO::PARAM_INT);
                    $query->execute();
                    $query->CloseCursor(); 

                    $query=$db->prepare('SELECT post_id FROM forum_post
                    WHERE post_forum_id = :forum ORDER BY post_id DESC LIMIT 0,1');
                    $query->bindValue(':forum',$forum,PDO::PARAM_INT);
                    $query->execute();
                    $data = $query->fetch();

                    $query=$db->prepare('UPDATE forum_forum
                    SET forum_topic = forum_topic - 1, forum_post = forum_post - :nbr, forum_last_post_id = :id
                    WHERE forum_id = :forum');
                    $query->bindValue(':nbr',$nombrepost,PDO::PARAM_INT);
                    $query->bindValue(':id',$data['post_id'],PDO::PARAM_INT);
                    $query->bindValue(':forum',$forum,PDO::PARAM_INT);
                    $query->execute(); 
                    $query->CloseCursor();

                    echo'<p>Le topic a bien été supprimé !<br />
                    Cliquez <a href="./index.php">ici</a> pour revenir à l index du forum</p>';

                }
            break;
        
            case "lock":
                $topic = (int) $_GET['t'];
                $query = $db->prepare('SELECT forum_topic.forum_id, auth_modo FROM forum_topic
                LEFT JOIN forum_forum ON forum_forum.forum_id = forum_topic.forum_id
                WHERE topic_id = :topic');
                $query->bindValue(':topic',$topic,PDO::PARAM_INT);
                $query->execute();
                $data = $query->fetch();

                if (!verif_auth($data['auth_modo']))
                {
                    erreur(ERR_AUTH_VERR);
                }  
                else
                {
                    $query->CloseCursor();
                    $query=$db->prepare('UPDATE forum_topic SET topic_locked = :lock WHERE topic_id = :topic');
                    $query->bindValue(':lock',1,PDO::PARAM_STR);
                    $query->bindValue(':topic',$topic,PDO::PARAM_INT);
                    $query->execute(); 
                    $query->CloseCursor();

                    echo'<p>Le topic a bien été verrouillé ! <br />
                    Cliquez <a href="./voirtopic.php?t='.$topic.'">ici</a> pour retourner au topic<br />
                    Cliquez <a href="./index.php">ici</a> pour revenir à l index du forum</p>';
                }
            break;

        case "unlock":
                $topic = (int) $_GET['t'];
            $query = $db->prepare('SELECT forum_topic.forum_id, auth_modo FROM forum_topic
            LEFT JOIN forum_forum ON forum_forum.forum_id = forum_topic.forum_id
            WHERE topic_id = :topic');
            $query->bindValue(':topic',$topic,PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetch();

            if (!verif_auth($data['auth_modo']))
            {
                erreur(ERR_AUTH_VERR);
            }  
            else
            {
                $query->CloseCursor();
                $query=$db->prepare('UPDATE forum_topic SET topic_locked = :lock WHERE topic_id = :topic');
                $query->bindValue(':lock',0,PDO::PARAM_STR);
                $query->bindValue(':topic',$topic,PDO::PARAM_INT);
                $query->execute(); 
                $query->CloseCursor();

                echo'<p>Le topic a bien été déverrouillé !<br />
                Cliquez <a href="./voirtopic.php?t='.$topic.'">ici</a> pour retourner au topic<br />
                Cliquez <a href="./index.php">ici</a> pour revenir à l index du forum</p>';
            }
        break;
        
        case "deplacer":

    $topic = (int) $_GET['t'];
    $query= $db->prepare('SELECT forum_topic.forum_id, auth_modo
    FROM forum_topic
    LEFT JOIN forum_forum 
    ON forum_forum.forum_id = forum_topic.forum_id
    WHERE topic_id =:topic');
    $query->bindValue(':topic',$topic,PDO::PARAM_INT);
    $query->execute();
    $data=$query->fetch();
    
    if (!verif_auth($data['auth_modo']))
    {
        erreur(ERR_AUTH_MOVE);
    }
    else
    {
        $query->CloseCursor();
        $destination = (int) $_POST['dest'];
        $origine = (int) $_POST['from'];
               
        $query=$db->prepare('UPDATE forum_topic SET forum_id = :dest WHERE topic_id = :topic');
        $query->bindValue(':dest',$destination,PDO::PARAM_INT);
        $query->bindValue(':topic',$topic,PDO::PARAM_INT);
        $query->execute();
        $query->CloseCursor(); 
 
        $query=$db->prepare('UPDATE forum_post SET post_forum_id = :dest
        WHERE topic_id = :topic');
        $query->bindValue(':dest',$destination,PDO::PARAM_INT);
        $query->bindValue(':topic',$topic,PDO::PARAM_INT);
        $query->execute(); 
        $query->CloseCursor();     
        
        $query=$db->prepare('SELECT COUNT(*) AS nombre_post
        FROM forum_post WHERE topic_id = :topic');
        $query->bindValue(':topic',$topic,PDO::PARAM_INT);
        $query->execute();    
        $data = $query->fetch();
        $nombrepost = $data['nombre_post'];
        $query->CloseCursor();       

        $query=$db->prepare('SELECT post_id FROM forum_post WHERE post_forum_id = :ori
        ORDER BY post_id DESC LIMIT 0,1');
        $query->bindValue(':ori',$origine,PDO::PARAM_INT);
        $query->execute();
        $data=$query->fetch();       
        $last_post=$data['post_id'];
        $query->CloseCursor();
        
        $query=$db->prepare('UPDATE forum_forum SET forum_post = forum_post - :nbr, forum_topic = forum_topic - 1,
        forum_last_post_id = :id
        WHERE forum_id = :ori');
        $query->bindValue(':nbr',$nombrepost,PDO::PARAM_INT);
        $query->bindValue(':ori',$origine,PDO::PARAM_INT);
        $query->bindValue(':id',$last_post,PDO::PARAM_INT);
        $query->execute();
        $query->CloseCursor();

        $query=$db->prepare('SELECT post_id FROM forum_post WHERE post_forum_id = :dest
        ORDER BY post_id DESC LIMIT 0,1');
        $query->bindValue(':dest',$destination,PDO::PARAM_INT);
        $query->execute();
        $data=$query->fetch();
        $last_post=$data['post_id'];
        $query->CloseCursor();

        $query=$db->prepare('UPDATE forum_forum SET forum_post = forum_post + :nbr,
        forum_topic = forum_topic + 1,
        forum_last_post_id = :last
        WHERE forum_id = :forum');
        $query->bindValue(':nbr',$nombrepost,PDO::PARAM_INT);
        $query->bindValue(':last',$last_post,PDO::PARAM_INT);
        $query->bindValue(':forum',$destination,PDO::PARAM_INT);
        $query->execute();
        $query->CloseCursor();

        echo'<p>Le topic a bien été déplacé <br />
        Cliquez <a href="./voirtopic.php?t='.$topic.'">ici</a> pour revenir au topic<br />
        Cliquez <a href="./index.php">ici</a> pour revenir à l index du forum</p>';
    }
break;

    default;
    echo '<p class="p_message"> Votre message n\'a pas pu être envoyé <br/> <a href="../membres/accueil"> Retour au forum </p>';
        
}
?>

</div>
</body>
</html>

