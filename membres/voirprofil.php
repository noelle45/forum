<?php
session_start();
$titre="Profil";
include("../includes/identifiants.php");
include("../includes/debut.php");



if(isset($_SESSION['pseudo'])){

    $action = isset($_GET['action'])?htmlspecialchars($_GET['action']):'consulter';
    $membre = isset($_GET['m'])?(int) $_GET['m']:'';

    switch($action)
    {
        case "consulter":

            $query=$db->prepare('SELECT membre_id, membre_pseudo, membre_avatar,
            membre_email, membre_msn, membre_signature, membre_siteweb, membre_post,
            membre_inscrit, membre_localisation
            FROM forum_membres WHERE membre_id=:membre');
            $query->bindValue(':membre',$membre, PDO::PARAM_INT);
            $query->execute();
            $data=$query->fetch();

            if($_SESSION['pseudo'] != $data['membre_pseudo']){
            echo'&nbsp; &nbsp; <i><p class="p_baniere">Vous êtes ici : &nbsp; &nbsp; </i><a href ="accueil.php">Accueil forum</a>  &nbsp; &nbsp;  Profil de '.stripslashes(htmlspecialchars($data['membre_pseudo'])).'<br/>';
            }
            else{
            echo'&nbsp; &nbsp; <i><p class="p_baniere">Vous êtes ici : &nbsp; &nbsp; </i><a href ="accueil.php">Accueil forum</a>  &nbsp; &nbsp;  Mon profil<br/><br/>';
            }

            if($_SESSION['pseudo'] != $data['membre_pseudo']) {
                if($data['membre_avatar'] !=0){
                echo'<p><div class="img_message_avatar"><img src="avatars/'.$data['membre_avatar'].'"alt="Ce membre n\'a pas d\'avatar" /></div></p>';
                }
                else{
                echo'<p><div class="img_message_avatar"><img src="avatars/compte100.png" alt="Ce membre n\'a pas d\'avatar" /></div></p>';
                }
            }

            if($_SESSION['pseudo'] == $data['membre_pseudo']) 
            {
            echo'<p><strong>Adresse E-Mail : </strong><a href="mailto:'.stripslashes($data['membre_email']).'">'.stripslashes(htmlspecialchars($data['membre_email'])).'</a><br/><br/>';
            }
            else
            {
            echo '<p> Adresse email masquèe </p>'; 
            }

            if($_SESSION['pseudo'] == $data['membre_pseudo']) {
            echo'<strong>MSN Messenger : </strong>'.stripslashes(htmlspecialchars($data['membre_msn'])).'<br/>';
            }
            else{
            echo '<p> Adresse messenger masquèe </p>'; 
            }

            echo'<strong><p>Site Web : </strong><a href="'.stripslashes($data['membre_siteweb']).'">'.stripslashes(htmlspecialchars($data['membre_siteweb'])).'</a></p>';
            echo'<p>Ce membre est inscrit depuis le <strong>'.date('d/m/Y',$data['membre_inscrit']).'</strong> et a posté <strong>'.$data['membre_post'].'</strong> messages</p>';
            echo'<p><strong>Localisation : </strong>'.stripslashes(htmlspecialchars($data['membre_localisation'])).'</p>';
            echo'<p><strong>Signature : </strong>'.stripslashes(htmlspecialchars($data['membre_signature'])).'</p><br/>';

            if($_SESSION['pseudo'] != $data['membre_pseudo']) {
            echo'<p><a href="../messagerie/messagerie.php?action=repondre&amp;dest='.$data['membre_id'].'"><div class="img_message_prive"><img src="../messagerie/img/enveloppe.png" height="80px" alt="Envoyer un MP"/><br/>Envoyer un message privé à ' .$data['membre_pseudo'].'</a>  </p><br/></div>';
//-------------------------------------------------------------------------------------------------------------------------------------------------------
$_GET[$data['membre_pseudo']]=$data['membre_pseudo'];
echo '<form action="friends.php?action=add" method="post">
    <p><label for="pseudo"></label>
    <input type="hidden" name="pseudo" id="pseudo" value="'.$_GET[$data['membre_pseudo']].'"/>
    <input type="submit" value="Ajouter ce membre à mes amis" />
    </p></form>';
            }
            
            if($_SESSION['pseudo'] == $data['membre_pseudo']) {
                if(isset($_SESSION['pseudo'])){
                echo '<br/><p>  &nbsp; &nbsp;  <a href="voirprofil.php?m='.$_SESSION['id'].'&amp;action=modifier">Modifier mon profil</p>';

                if(!empty($_SESSION['avatar'])){
                echo '<p class="p_baniere_petit"><img class="roundedImage" src="avatars/'.$_SESSION['avatar'].'" alt="Avatar" title="Voir mon profil"/>';}
                else{
                echo '<p class="p_baniere_petit"><img class="roundedImage" src="avatars/compte100.png" alt="Avatar" title="Voir mon profil" /></a><br/>';}
                echo '<br/><p class="p_baniere_petit"><a href="deconnexion.php" title="vous avez terminé votre visite ?"> Me deconnecter</a></p>';
                }
            }
            $query->CloseCursor();
        break;

        case "modifier":
            include("../includes/baniere-membres.php");
            echo'<p>&nbsp; &nbsp;<i><p class="p_baniere">Vous êtes ici : &nbsp; &nbsp; </i><a href ="accueil.php">Accueil forum</a>  &nbsp; &nbsp; <a href="./voirprofil.php?m='.$_SESSION['id'].'&amp;action=consulter"> Profil </a>  &nbsp; &nbsp; Modifier</p>';
            if (empty($_POST['sent'])) {
                if ($id==0) erreur(ERR_IS_NOT_CO);

                //On prend les infos du membre
                $query=$db->prepare('SELECT membre_pseudo, membre_email,
                membre_siteweb, membre_signature, membre_msn, membre_localisation,
                membre_avatar, membre_mdp2
                FROM forum_membres WHERE membre_id=:id');
                $query->bindValue(':id',$id,PDO::PARAM_INT);
                $query->execute();
                $data=$query->fetch();

                echo'<p style="text-align:center">Modification de mon profil '.stripslashes(htmlspecialchars($data['membre_pseudo'])).'</p>';
                echo '<form method="post" action="voirprofil.php?action=modifier" enctype="multipart/form-data">
                <fieldset><br/>	
                <p style="color:white">Mes identifiants</p><br/>Pseudo : <strong>'.stripslashes(htmlspecialchars($data['membre_pseudo'])).'</strong><br /><br/>
                <label for="password">Nouveau mot de Passe :</label><br/><input type="password" name="password" id="password" value="'.stripslashes($data['membre_mdp2']).'" /><br /><br/>
                <label for="confirm" style="color:red" >Confirmez votre mot de passe :</label><br/></br>
                <input type="password" name="confirm" id="confirm"  />
                </fieldset><br/>

                <fieldset><br/>
                <p style="color:white">Mes coordonnées</p><br/>
                <label for="email">Votre adresse E_Mail :</label><br/><input type="text" name="email" id="email" value="'.stripslashes($data['membre_email']).'" /><br /><br/>
                <label for="msn">Votre adresse MSN :</label><br/><input type="text" name="msn" id="msn" value="'.stripslashes($data['membre_msn']).'" /><br /><br/>
                <label for="website">Votre site web :</label><br/><input type="text" name="website" id="website" value="'.stripslashes($data['membre_siteweb']).'" /><br /><br/>
                </fieldset><br/>

                <fieldset><br/>
                <p style="color:white">Informations complémentaires</p><br/><label for="localisation">Localisation :</label><br/><input type="text" name="localisation" id="localisation" value="'.stripslashes($data['membre_localisation']).'" /><br /><br/>
                </fieldset><br/>

                <fieldset><br/>
                <p style="color:white">Mon avatar - Je souhaite : </p><br/><label for="avatar">Changer mon avatar :</label><br/><input type="file" name="avatar" id="avatar" /><br/>(Taille max : 30 ko)<br /><br /> Ou<br/><br/>
                <label><input type="checkbox" name="delete" value="Delete" />
                Supprimer mon avatar actuel : </label><br/><img src="avatars/'.$data['membre_avatar'].'"alt="pas d\'avatar" />
                <br /><br />
                <label for="signature">Signature :</label><br/><textarea cols="40" rows="4" name="signature" id="signature">'.stripslashes($data['membre_signature']).'</textarea><br/><br/><input type="submit" value="Enregistrer les modifications" />
                </fieldset>
                <p>		  
                <input type="hidden" id="sent" name="sent" value="1" />
                </p></form>';
                $query->CloseCursor();   
            }
            
            else{
            $mdp_erreur = NULL;
            $email_erreur1 = NULL;
            $email_erreur2 = NULL;
            $msn_erreur = NULL;
            $signature_erreur = NULL;
            $avatar_erreur = NULL;
            $avatar_erreur1 = NULL;
            $avatar_erreur2 = NULL;
            $avatar_erreur3 = NULL;

            $i = 0;
            $temps = time(); 
            $signature = $_POST['signature'];
            $email = $_POST['email'];
            $msn = $_POST['msn'];
            $website = $_POST['website'];
            $localisation = $_POST['localisation'];
            $pass = ($_POST['password']);
            $confirm = ($_POST['confirm']);

                if ($pass != $confirm || empty($confirm) || empty($pass)){
                $mdp_erreur = "Votre mot de passe et votre confirmation diffèrent ou sont vides";
                $i++;
                }

                $query=$db->prepare('SELECT membre_email FROM forum_membres WHERE membre_id =:id'); 
                $query->bindValue(':id',$id,PDO::PARAM_INT);
                $query->execute();
                $data=$query->fetch();

                if (strtolower($data['membre_email']) != strtolower($email)){
                $query=$db->prepare('SELECT COUNT(*) AS nbr FROM forum_membres WHERE membre_email =:mail');
                $query->bindValue(':mail',$email,PDO::PARAM_STR);
                $query->execute();
                $mail_free=($query->fetchColumn()==0)?1:0;
                $query->CloseCursor();
                
                    if(!$mail_free){
                    $email_erreur1 = "Votre adresse email est déjà utilisé par un membre";
                    $i++;
                    }

                    if (!preg_match("#^[a-z0-9A-Z._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email) || empty($email)){
                    $email_erreur2 = "Votre nouvelle adresse E-Mail n'a pas un format valide";
                    $i++;
                    }
                }

            if (!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $msn) && !empty($msn)){
            $msn_erreur = "Votre nouvelle adresse MSN n'a pas un format valide";
            $i++;
            }

            if (strlen($signature) > 200){
            $signature_erreur = "Votre nouvelle signature est trop longue";
            $i++;
            }

            if (!empty($_FILES['avatar']['size']))
            {

                $maxsize = 30072;
                $maxwidth = 200;
                $maxheight = 200;
                $extensions_valides = array( 'jpg' , 'jpeg' , 'gif' , 'png', 'bmp' );

                if ($_FILES['avatar']['error'] > 0){
                $avatar_erreur = "Erreur lors du tranfsert de l'avatar : ";
                }

                if ($_FILES['avatar']['size'] > $maxsize){
                $i++;
                $avatar_erreur1 = "Le fichier est trop gros :
                (<strong>".$_FILES['avatar']['size']." Octets</strong>contre <strong>".$maxsize." Octets</strong>)";
                }

                $image_sizes = getimagesize($_FILES['avatar']['tmp_name']);

                if ($image_sizes[0] > $maxwidth OR $image_sizes[1] > $maxheight){
                $i++;
                $avatar_erreur2 = "Image trop large ou trop longue :
                (<strong>".$image_sizes[0]."x".$image_sizes[1]."</strong> contre <strong>".$maxwidth."x".$maxheight."</strong>)";
                }

                $extension_upload = strtolower(substr(  strrchr($_FILES['avatar']['name'], '.')  ,1));

                if (!in_array($extension_upload,$extensions_valides) )
                {
                $i++;
                $avatar_erreur3 = "Extension de l'avatar incorrecte";
                }
            }

            echo '<h1>Modification de mon profil</h1>';

            if ($i == 0){
                if (!empty($_FILES['avatar']['size']))
                {
                $nomavatar=move_avatar($_FILES['avatar']);
                $query=$db->prepare('UPDATE forum_membres
                SET membre_avatar = :avatar 
                WHERE membre_id = :id');
                $query->bindValue(':avatar',$nomavatar,PDO::PARAM_STR);
                $query->bindValue(':id',$id,PDO::PARAM_INT);
                $query->execute();
                $query->CloseCursor();
                }

                if (isset($_POST['delete']))
                {
                $query=$db->prepare('UPDATE forum_membres
                SET membre_avatar=0 WHERE membre_id = :id');
                $query->bindValue(':id',$id,PDO::PARAM_INT);
                $query->execute();
                $query->CloseCursor();
                }

                echo'<p>Modification terminée</p>';
                echo'<p>Votre profil a été modifié avec succès !</p><br/>';
                echo'<p>Cliquez <a href="accueil.php">ici</a> 
                pour revenir à la page d\'accueil</p>';

                $query=$db->prepare('UPDATE forum_membres
                SET  membre_mdp2 = :mdp, membre_email=:mail, membre_msn=:msn, membre_siteweb=:website,
                membre_signature=:sign, membre_localisation=:loc
                WHERE membre_id=:id');
                $query->bindValue(':mdp',$pass,PDO::PARAM_INT);
                $query->bindValue(':mail',$email,PDO::PARAM_STR);
                $query->bindValue(':msn',$msn,PDO::PARAM_STR);
                $query->bindValue(':website',$website,PDO::PARAM_STR);
                $query->bindValue(':sign',$signature,PDO::PARAM_STR);
                $query->bindValue(':loc',$localisation,PDO::PARAM_STR);
                $query->bindValue(':id',$id,PDO::PARAM_INT);
                $query->execute();
                $query->CloseCursor();
            }
                
            else
            {
            echo'<p>Modification interrompue</p>';
            echo'<p>Une ou plusieurs erreurs se sont produites pendant la modification du profil</p>';
            echo'<p>'.$i.' erreur(s)</p>';
            echo'<p>'.$mdp_erreur.'</p>';
            echo'<p>'.$email_erreur1.'</p>';
            echo'<p>'.$email_erreur2.'</p>';
            echo'<p>'.$msn_erreur.'</p>';
            echo'<p>'.$signature_erreur.'</p>';
            echo'<p>'.$avatar_erreur.'</p>';
            echo'<p>'.$avatar_erreur1.'</p>';
            echo'<p>'.$avatar_erreur2.'</p>';
            echo'<p>'.$avatar_erreur3.'</p>';
            echo'<p> Cliquez <a href="./voirprofil.php?action=modifier">ici</a> pour recommencer</p>';
            }
        }
        break;

        default;
        echo'<p>Cette action est impossible</p>';
    }

    include("../includes/footer_simple.php");
}
else{
echo  'Vous ne pouvez pas accéder à cette page si vous n\'êtes pas connecté(e)<br/><br/>';
echo '<a href="accueil.php"> Aller à l\'accueil du forum</a>'; 
}
include("../includes/footer_membres.php");
?>
</div>
</body>
</html>
</html>

