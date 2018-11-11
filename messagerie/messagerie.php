<?php
session_start();
$titre="Messages Privés";
$balises = true;
include("../includes/identifiants.php");
include("../includes/debut.php");
include("../includes/bbcode.php");
include("../includes/baniere-messagerie.php");
$action = (isset($_GET['action']))?htmlspecialchars($_GET['action']):'';
if(isset($_SESSION['pseudo']))
{
        switch($action) //On switch sur $action
        {
            case "consulter": //Si on veut lire un message
                 echo'<p>  &nbsp; &nbsp;<i>Vous êtes ici</i> :   &nbsp; &nbsp; <a href="../membres/accueil">Index du forum</a>  &nbsp; &nbsp;  <a href="messagerie.php">Ma messagerie</a>  &nbsp; &nbsp;Consulter un message</p>';
                 $id_mess = (int) $_GET['id']; //On récupère la valeur de l'id
//La requête nous permet d'obtenir les infos sur ce message :
                 $query = $db->prepare('SELECT  mp_expediteur, mp_receveur, mp_titre,               
                 mp_time, mp_text, mp_lu, membre_id, membre_pseudo, membre_avatar,
                 membre_localisation, membre_inscrit, membre_post, membre_signature
                 FROM forum_mp
                 LEFT JOIN forum_membres ON membre_id = mp_expediteur
                 WHERE mp_id = :id');
                 $query->bindValue(':id',$id_mess,PDO::PARAM_INT);
                 $query->execute();
                 $data=$query->fetch();
                 // Attention ! Seul le receveur du mp peut le lire !
                 if ($id != $data['mp_receveur']) erreur(ERR_WRONG_USER);
                ?>
                 <table>     
                     <tr>
                         <th class="vt_auteur"><strong>Auteur</strong></th>             
                         <th class="vt_mess"><strong>Message</strong></th>       
                     </tr>
                     <tr>
                         <td>
                             <?php 
                             echo'<strong>
                             <a href="../membres/voirprofil.php?m='.$data['membre_id'].'&amp;action=consulter">
                             '.stripslashes(htmlspecialchars($data['membre_pseudo'])).'</a></strong></td>
                             <td>Posté à '.date('H\hi \l\e d M Y',$data['mp_time']).'</td>';
                             ?>
                         </td>
                     </tr>
                 </table>
<?php
                 echo'<p><img src="../membres/avatars/'.$data['membre_avatar'].'" alt="Ce membre n\'a pas d\'avatar" />
                 <br />Membre inscrit le '.date('d/m/Y',$data['membre_inscrit']).'
                 <br />Messages : '.$data['membre_post'].'
                 <br />Localisation : '.stripslashes(htmlspecialchars($data['membre_localisation'])).'</p>
                 </td><td>';
                 echo code(nl2br(stripslashes(htmlspecialchars($data['mp_text'])))).'
                 <hr />'.code(nl2br(stripslashes(htmlspecialchars($data['membre_signature'])))).'
                 <br/><br/>';
                  //bouton de réponse
                 echo '<a href="messagerie.php?action=repondre&amp;dest='.$data['mp_expediteur'].'"><img src="img/repondre.gif" alt="Répondre" title="Répondre à ce message"></a>
                 </table>
                 </td></tr><br/><br/>';
                      if ($data['mp_lu'] == 0)
                 {
                      $query->CloseCursor();
                      $query=$db->prepare('UPDATE forum_mp SET mp_lu = :lu WHERE mp_id= :id');
                      $query->bindValue(':id',$id_mess, PDO::PARAM_INT);
                      $query->bindValue(':lu','1', PDO::PARAM_STR);
                      $query->execute();
                      $query->CloseCursor();
                 }
                
        break;
                
                case "nouveau": 
                echo'<p>  &nbsp; &nbsp;  <i>Vous êtes ici</i> :   &nbsp; &nbsp;   <a href="../membres/accueil.php">Index du forum</a>  &nbsp; &nbsp;  <a href="messagerie.php">Ma messagerie</a>  &nbsp; &nbsp;  Ecrire un message</p>';
                ?>
                <form method="post" action="postok.php?action=nouveaump" name="formulaire">
                <p><br/>
                <label for="to">Envoyer à : </label><br/>
                <input type="text" size="30" id="to" name="to" />
                <br /><br/>
                <label for="titre">Titre : </label><br/>
                <input type="text" size="30" id="titre" name="titre" />
                <br /><br />
                <input type="button" id="gras" name="gras" value="Gras" onClick="javascript:bbcode('[g]', '[/g]');return(false)" />
                <input type="button" id="italic" name="italic" value="Italic" onClick="javascript:bbcode('[i]', '[/i]');return(false)" />
                <input type="button" id="souligné" name="souligné" value="Souligné" onClick="javascript:bbcode('[s]', '[/s]');return(false)" />
                <input type="button" id="lien" name="lien" value="Lien" onClick="javascript:bbcode('[url]', '[/url]');return(false)" />
                <br /><br />
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
                <textarea cols="80" rows="8" id="message" name="message"></textarea>
                <br />
                <input type="submit" name="submit" value="Envoyer" />
                <input type="reset" name="Effacer" value="Effacer" /></p>
                </form>
            <?php   
        break;
                
                case "repondre": 
                echo '<h1> Répondre </h1>';
                 $dest = (int) $_GET['dest'];
           ?>
           <form method="post" action="postok.php?action=repondremp&amp;dest=<?php echo $dest ?>" name="formulaire">
           <p><br/>
           <label for="titre">Titre : </label><br /><input type="text" size="30" id="titre" name="titre" />
           <br /><br />
           <input type="button" id="gras" name="gras" value="Gras" onClick="javascript:bbcode('[g]', '[/g]');return(false)" />
           <input type="button" id="italic" name="italic" value="Italic" onClick="javascript:bbcode('[i]', '[/i]');return(false)" />
           <input type="button" id="souligné" name="souligné" value="Souligné" onClick="javascript:bbcode('[s]', '[/s]');return(false)" />
           <input type="button" id="lien" name="lien" value="Lien" onClick="javascript:bbcode('[url]', '[/url]');return(false)" />
           <br /><br />
           <img src="img/heureux.png" title="heureux" alt="heureux" onClick="javascript:smilies(' :D ');return(false)" />
                <img src="img/smile.png" title="lol" alt="lol" onClick="javascript:smilies(' :lol: ');return(false)" />
                <img src="img/triste.png" title="triste" alt="triste" onClick="javascript:smilies(' :triste: ');return(false)" />
                <img src="img/cool.png" title="cool" alt="cool" onClick="javascript:smilies(' :frime: ');return(false)" />
                <img src="img/langue.png" title="rire" alt="rire" onClick="javascript:smilies(' XD ');return(false)" />
                <img src="img/huh.png" title="confus" alt="confus" onClick="javascript:smilies(' :s ');return(false)" />
                <img src="img/choc.png" title="choc" alt="choc" onClick="javascript:smilies(' :o ');return(false)" />
                <img src="img/point-int.gif" title="?" alt="?" onClick="javascript:smilies(' :interrogation: ');return(false)" />
                <img src="img/pirate.png" title="!" alt="!" onClick="javascript:smilies(' :exclamation: ');return(false)" />

           <br /><br />
           <textarea cols="80" rows="8" id="message" name="message"></textarea>
           <br />
           <input type="submit" name="submit" value="Envoyer" />
           <input type="reset" name="Effacer" value="Effacer"/>
           </p></form>
           <?php
        break;
                
                case "supprimer":
            $id_mess = (int) $_GET['id'];
            $query=$db->prepare('SELECT mp_receveur
            FROM forum_mp WHERE mp_id = :id');
            $query->bindValue(':id',$id_mess,PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetch();
            if ($id != $data['mp_receveur']) erreur(ERR_WRONG_USER);
            $query->CloseCursor(); 
            $sur = (int) $_GET['sur'];
            if ($sur == 0)
            {
            echo'<p>Etes-vous certain de vouloir supprimer ce message ?<br />
            <a href="messagerie.php?action=supprimer&amp;id='.$id_mess.'&amp;sur=1">
            Oui</a> - <a href="messagerie.php">Non</a></p>';
            }
            else
            {
                $query=$db->prepare('DELETE from forum_mp WHERE mp_id = :id');
                $query->bindValue(':id',$id_mess,PDO::PARAM_INT);
                $query->execute();
                $query->CloseCursor(); 
                echo'<p>Le message a bien été supprimé.<br />
                Cliquez <a href="messagerie.php">ici</a> pour revenir à la boite
                de messagerie.</p>';
            }
            break;
                default;
            echo'<p>  &nbsp; &nbsp;  <i>Vous êtes ici</i> :   &nbsp; &nbsp;   <a href="../membres/accueil.php">Index du forum</a>  &nbsp; &nbsp;  <a href="messagerie.php">Ma messagerie</a>';
                
            $query=$db->prepare('SELECT mp_lu, mp_id, mp_expediteur, mp_titre, mp_time, membre_id, membre_pseudo
            FROM forum_mp
            LEFT JOIN forum_membres ON forum_mp.mp_expediteur = forum_membres.membre_id
            WHERE mp_receveur = :id ORDER BY mp_id DESC');
            $query->bindValue(':id',$id,PDO::PARAM_INT);
            $query->execute();
                
                //Bouton nouveau message
                 echo'<p><a href="messagerie.php?action=nouveau"><img src="img/nouveau.gif" alt="Nouveau" title="Nouveau message" /></a></p>';
                
            if ($query->rowCount()>0)
            {
                ?>
                <table>
                <tr>
                <th></th>
                <th class="mp_titre"><strong>Titre</strong></th>
                <th class="mp_expediteur"><strong>Expéditeur</strong></th>
                <th class="mp_time"><strong>Date</strong></th>
                <th><strong>Action</strong></th>
                </tr>

                <?php
                while ($data = $query->fetch())
                {
                    echo'<tr>';
                    //Mp jamais lu
                    if($data['mp_lu'] == 0)
                    {
                    echo'<td><img src="img/enveloppe.png" alt="Non lu" height=30px"/></td>';
                    }
                    else //sinon une autre icone
                    {
                    echo'<td><img src="img/enveloppe-lu.png" alt="Déja lu" height=30px /></td>';
                    }
                    echo'<td id="mp_titre">
                    <a href="messagerie.php?action=consulter&amp;id='.$data['mp_id'].'">
                    '.stripslashes(htmlspecialchars($data['mp_titre'])).'</a></td>
                    <td id="mp_expediteur">
                    <a href="../membres/voirprofil.php?action=consulter&amp;m='.$data['membre_id'].'">
                    '.stripslashes(htmlspecialchars($data['membre_pseudo'])).'</a></td>
                    <td id="mp_time">'.date('H\hi \l\e d M Y',$data['mp_time']).'</td>
                    <td>
                    <a href="messagerie.php?action=supprimer&amp;id='.$data['mp_id'].'&amp;sur=0">supprimer</a></td></tr>';
                } //Fin de la boucle
                $query->CloseCursor();
                echo '</table>';
            } //Fin du if
            else
            {
                echo'<p>Vous n\'avez aucun message privé pour l\'instant, cliquez
                <a href="../membres/accueil.php">ici</a> pour revenir à la page d\'accueil</p>';
            }
        } //Fin du switch
    }
    else
    {
        echo '<br/><p class="p_message"> Vous ne pouvez pas accéder à cette page';
}
?>
            
            
</div>
</body>
</html>