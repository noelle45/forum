<?php
session_start();
$titre="Poster";
include("../includes/identifiants.php");
include("../includes/debut.php");
//On récupère la valeur de la variable action
$action = (isset($_GET['action']))?htmlspecialchars($_GET['action']):'';

// Si le membre n'est pas connecté, il est arrivé ici par erreur
if ($id==0) erreur(ERR_IS_CO);


switch($action)
{
   case "repondremp": 

    $message = $_POST['message'];
    $titre = $_POST['titre'];
    $temps = time();

    $dest = (int) $_GET['dest'];

    $query=$db->prepare('INSERT INTO forum_mp
    (mp_expediteur, mp_receveur, mp_titre, mp_text, mp_time, mp_lu)
    VALUES(:id, :dest, :titre, :txt, :tps, 0)');
		
    $query->bindValue(':id',$id,PDO::PARAM_INT);   
    $query->bindValue(':dest',$dest,PDO::PARAM_INT);   
    $query->bindValue(':titre',$titre,PDO::PARAM_STR);   
    $query->bindValue(':txt',$message,PDO::PARAM_STR);   
    $query->bindValue(':tps',$temps,PDO::PARAM_INT);   
    $query->execute();
    $query->CloseCursor(); 

    echo'<p>Votre message a bien été envoyé!<br />
    <br />Cliquez <a href="../membres/accueil.php">ici</a> pour revenir à l\'accueil du   
    forum<br />
    <br />Cliquez <a href="messagerie.php">ici</a> pour retourner
    à la messagerie</p>';

    break;
		
		case "nouveaump":

            $message = $_POST['message'];
            $titre = $_POST['titre'];
            $temps = time();
            $dest = $_POST['to'];

            $query=$db->prepare('SELECT membre_id FROM forum_membres
            WHERE LOWER(membre_pseudo) = :dest');
            $query->bindValue(':dest',strtolower($dest),PDO::PARAM_STR);
            $query->execute();
            if($data = $query->fetch())
            {
                $query=$db->prepare('INSERT INTO forum_mp
                (mp_expediteur, mp_receveur, mp_titre, mp_text, mp_time, mp_lu)
                VALUES(:id, :dest, :titre, :txt, :tps, :lu)'); 
                $query->bindValue(':id',$id,PDO::PARAM_INT);   
                $query->bindValue(':dest',(int) $data['membre_id'],PDO::PARAM_INT);   
                $query->bindValue(':titre',$titre,PDO::PARAM_STR);   
                $query->bindValue(':txt',$message,PDO::PARAM_STR);   
                $query->bindValue(':tps',$temps,PDO::PARAM_INT);   
                $query->bindValue(':lu','0',PDO::PARAM_STR);   
                $query->execute();
                $query->CloseCursor(); 

               echo'<p>Votre message a bien été envoyé!
               <br /><br />Cliquez <a href="../membres/accueil.php">ici</a> pour revenir à l index du
               forum<br />
               <br />Cliquez <a href="messagerie.php">ici</a> pour retourner à
               la messagerie</p>';
            }

                else
                {
                    echo'<p>Désolé ce membre n\'existe pas, veuillez vérifier et
                    réessayez à nouveau.</p>';
                }
                break;



                default;
                echo '<p class="p_message"> Votre message n\'a pas pu être envoyé <br/> <a href="../messagerie/messagerie.php?action=nouveau"> Retour </p>';
            } //Fin du Switch
?>
</div>
</body>
</html>

