<?php
$query=$db->query('SELECT COUNT(*) AS nbr FROM forum_membres');
$data = $query->fetch();

$total = $data['nbr'] +1;
$query->CloseCursor();
$MembreParPage = 25;
$NombreDePages = ceil($total / $MembreParPage);

$page = (isset($_GET['page']))?intval($_GET['page']):1;

$premier = ($page - 1) * $MembreParPage;

//Tri

$convert_order = array('membre_pseudo', 'membre_inscrit', 'membre_post', 'membre_derniere_visite'); 
$convert_tri = array('ASC', 'DESC');

if (isset ($_POST['s'])) $sort = $convert_order[$_POST['s']];
else $sort = $convert_order[0];

if (isset ($_POST['t'])) $tri = $convert_tri[$_POST['t']];
else $tri = $convert_tri[0];

//Requête
$query = $db->prepare('SELECT membre_id, membre_pseudo, membre_inscrit, membre_post, membre_derniere_visite, online_id, membre_avatar, membre_email
FROM forum_membres
LEFT JOIN forum_whosonline ON online_id = membre_id
ORDER BY '.$sort.', online_id '.$tri.'
LIMIT :premier, :membreparpage');
$query->bindValue(':premier',$premier,PDO::PARAM_INT);
$query->bindValue(':membreparpage',$MembreParPage, PDO::PARAM_INT);
$query->execute();

if(isset($_SESSION['pseudo'])){

    if ($query->rowCount() > 0)
    {

    ?><div style="text-align:center"><?php
        while ($data = $query->fetch()){

                if($_SESSION['pseudo'] == $data['membre_pseudo'] && (empty($data['membre_avatar']))){//si session = membre data et avatar absent affiche avatar de substitution + lien index
                        echo ' &nbsp; &nbsp; <a href="membres/accueil.php" ><img class="roundedImage" src="membres/avatars/compte100.png" title="'.$data['membre_pseudo'].' - Inscrit depuis le '.date('d/m/Y',$data['membre_inscrit']).' - Dernière visite le : '.date('d/m/Y',$data['membre_derniere_visite']).'" type="submit"</a>';
                        echo' &nbsp; &nbsp; ';
                }

                elseif($_SESSION['pseudo'] == $data['membre_pseudo'] && (!empty($data['membre_avatar']))){//si session = membre data et avatar présnt affiche avatar de membre + lien accueil
                        echo ' &nbsp; &nbsp; <a href="membres/accueil.php" ><img class="roundedImage" src="membres/avatars/'.$data['membre_avatar'].'" title="'.$data['membre_pseudo'].' - Inscrit depuis le '.date('d/m/Y',$data['membre_inscrit']).' - Dernière visite le : '.date('d/m/Y',$data['membre_derniere_visite']).'" type="submit"</a>';
                        echo' &nbsp; &nbsp; ';
                }

                elseif($_SESSION['pseudo'] != $data['membre_pseudo'] && (empty($data['membre_avatar']))){//si session != membre data et avatar absent affiche avatar de substitution + lien index
                        echo ' &nbsp; &nbsp; <a href="index.php" ><img class="roundedImage" src="membres/avatars/compte100.png" title="'.$data['membre_pseudo'].' - Inscrit depuis le '.date('d/m/Y',$data['membre_inscrit']).' - Dernière visite le : '.date('d/m/Y',$data['membre_derniere_visite']).'" type="submit"</a>';
                        echo' &nbsp; &nbsp; ';
                }

                elseif($_SESSION['pseudo'] != $data['membre_pseudo'] && (!empty($data['membre_avatar']))){//si session != membre data et avatar présnt affiche avatar de membre + lien accueil
                        echo ' &nbsp; &nbsp; <a href="index.php" ><img class="roundedImage" src="membres/avatars/'.$data['membre_avatar'].'" title="'.$data['membre_pseudo'].' - Inscrit depuis le '.date('d/m/Y',$data['membre_inscrit']).' - Dernière visite le : '.date('d/m/Y',$data['membre_derniere_visite']).'" type="submit"</a>';
                        echo' &nbsp; &nbsp; ';
                }
        }// while
    $query->CloseCursor();

    echo'<hr>';

    if(isset($_SESSION['pseudo'])){echo'<p> Bienvenu '.$_SESSION['pseudo'].' !<br/>Ta session est déjà active - Clique sur ton avatar pour entrer</p>';}

    } //if $query
}
//*****************************************************************PAS DE SESSION EN COURS **************************************************************
else
{
    if ($query->rowCount() > 0)
    {
       while ($data = $query->fetch())
       {   if(!empty($data['membre_avatar'])){
           echo ' &nbsp; &nbsp; <a href="membres/accueil.php" ><img class="roundedImage" src="membres/avatars/'.$data['membre_avatar'].'" title="'.$data['membre_pseudo'].' - Inscrit depuis le '.date('d/m/Y',$data['membre_inscrit']).' - Dernière visite le : '.date('d/m/Y',$data['membre_derniere_visite']).'" type="submit"</a>';
echo' &nbsp; &nbsp; ';
}
	   else{
	   echo ' &nbsp; &nbsp; <a href="membres/accueil.php" ><img class="roundedImage" src="membres/avatars/compte100.png" title="'.$data['membre_pseudo'].' - Inscrit depuis le '.date('d/m/Y',$data['membre_inscrit']).' - Dernière visite le : '.date('d/m/Y',$data['membre_derniere_visite']).'"</a>';
echo' &nbsp; &nbsp; ';}
           

       }
       $query->CloseCursor();
    echo'<hr>';

    echo'<p> Bienvenu !<br/>Pour te connecter saisie tes identifiants :';

    } //if $query
}

?>
</div>
