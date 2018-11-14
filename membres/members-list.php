<?php
session_start();
$titre="Liste des membres";
include("../includes/identifiants.php");
include("../includes/debut.php");
include("../includes/baniere-membres.php");

$query=$db->query('SELECT COUNT(*) AS nbr FROM forum_membres');
$data = $query->fetch();

$total = $data['nbr'] +1;
$query->CloseCursor();
$MembreParPage = 25;
$NombreDePages = ceil($total / $MembreParPage);
echo '<p><i>Vous êtes ici</i> : <a href="accueil.php">Accueil</a>  &nbsp; &nbsp; <a href="members-list.php">Liste des membres</a></p>';

$page = (isset($_GET['page']))?intval($_GET['page']):1;

echo '<p>Page : ';
for ($i = 1 ; $i <= $NombreDePages ; $i++)
{
    if ($i == $page)
    {
        echo $i;
    }
    else
    {
        echo '<p><a href="members-list.php?page='.$i.'">'.$i.'</a></p>';
    }
}
echo '</p>';

$premier = ($page - 1) * $MembreParPage;

echo '<h1>Liste des membres</h1><br /><br />';

//Tri
$convert_order = array('membre_pseudo', 'membre_inscrit', 'membre_post', 'membre_derniere_visite'); 
$convert_tri = array('ASC', 'DESC');

if (isset ($_POST['s'])) $sort = $convert_order[$_POST['s']];
else $sort = $convert_order[0];

if (isset ($_POST['t'])) $tri = $convert_tri[$_POST['t']];
else $tri = $convert_tri[0];

?>
<div style="text-align:center">
<form action="members-list.php" method="post">
<p><label for="s"></label></p><br/>
<p>
<select name="s" id="s">
<option value="0" name="0">Pseudo</option>
<option value="1" name="1">Inscription</option>
<option value="2" name="2">Messages</option>
<option value="3" name="3">Dernière connexion</option>
</select>

<select name="t" id="t">
<option value="0" name="0">Croissant</option>
<option value="1" name="1">Décroissant</option>
</select>
<input type="submit" value="Trier" /></p>
</form>
</div>
<?php

//Requête
$query = $db->prepare('SELECT membre_id, membre_pseudo, membre_inscrit, membre_post, membre_derniere_visite, online_id
FROM forum_membres
LEFT JOIN forum_whosonline ON online_id = membre_id
ORDER BY '.$sort.', online_id '.$tri.'
LIMIT :premier, :membreparpage');
$query->bindValue(':premier',$premier,PDO::PARAM_INT);
$query->bindValue(':membreparpage',$MembreParPage, PDO::PARAM_INT);
$query->execute();

if ($query->rowCount() > 0)
{
?>
       <table>
       <tr>
       <th class="pseudo"><strong>Pseudo</strong></th>             
       <th class="posts"><strong>Messages</strong></th>
       <th class="inscrit"><strong>Inscrit depuis le</strong></th>
       <th class="derniere_visite"><strong>Dernière visite</strong></th>                       
       <th><strong>Connecté</strong></th>             

       </tr>
       <?php
       
       while ($data = $query->fetch())
       {
           echo '<tr>
	   <td style="text-align:center"><a href="voirprofil.php?m='.$data['membre_id'].'&amp;action=consulter">
           '.stripslashes(htmlspecialchars($data['membre_pseudo'])).'</a></td>
           <td style="text-align:center;color:black">'.$data['membre_post'].'</td>
           <td style="text-align:center;color:black">'.date('d/m/Y',$data['membre_inscrit']).'</td>
           <td style="text-align:center;color:black">'.date('d/m/Y',$data['membre_derniere_visite']).'</td>';

           if (empty($data['online_id'])) 
	   echo '<td style="text-align:center;color:black"><img class="roundedImageIcon" src="img/noconnect.png" height="30px"></td>'; 
           else echo '<td style="text-align:center;color:black"><img class="roundedImageIcon" src="img/connect.png" height="30px"></td>';
           echo '</tr>';
       }
       $query->CloseCursor();
       ?>
       </table>
       <?php
}
else
{
    echo'<p>Ce forum ne contient aucun membre actuellement</p>';
}

include("../includes/footer_membres.php");
?>
</div>
</body></html>

