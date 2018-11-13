<div class="footer">

<p style="margin-top:55px">&nbsp; &nbsp; 
<i>Qui est en ligne ?</i>
</p>
    
<?php
//On compte les membres
$TotalDesMembres = $db->query('SELECT COUNT(*) FROM forum_membres')->fetchColumn();
$query->CloseCursor();	
$query = $db->query('SELECT membre_pseudo, membre_id FROM forum_membres ORDER BY membre_id DESC LIMIT 0, 1');
$data = $query->fetch();
$derniermembre = stripslashes(htmlspecialchars($data['membre_pseudo']));

//echo'<p> &nbsp; &nbsp; Le total des messages du forum est <strong>'.$totaldesmessages.'</strong>.<br />';
echo'<p> &nbsp; &nbsp; Le forum comptent <strong>'.$TotalDesMembres.'</strong> membres.<br />';
echo'&nbsp; &nbsp; Le dernier membre inscrit est <a href="../membres/voirprofil.php?m='.$data['membre_id'].'&amp;action=consulter">'.$derniermembre.'</a>.</p>';
$query->CloseCursor();

$count_online = 0;

//Décompte des visiteurs
$count_visiteurs=$db->query('SELECT COUNT(*) AS nbr_visiteurs FROM forum_whosonline WHERE online_id = 0')->fetchColumn();
$query->CloseCursor();

//Décompte des membres
$texte_a_afficher = "<br /> &nbsp; &nbsp; Liste des personnes en ligne : ";
$time_max = time() - (60 * 10);
$query=$db->prepare('SELECT membre_id, membre_pseudo 
FROM forum_whosonline
LEFT JOIN forum_membres ON online_id = membre_id
WHERE online_time > :timemax AND online_id <> 0');
$query->bindValue(':timemax',$time_max, PDO::PARAM_INT);
$query->execute();
$count_membres=0;
while ($data = $query->fetch())
{
	$count_membres ++;
	$texte_a_afficher .= '<a href="../membres/voirprofil.php?m='.$data['membre_id'].'&amp;action=consulter">
	'.stripslashes(htmlspecialchars($data['membre_pseudo'])).'</a> ,';
}

$texte_a_afficher = substr($texte_a_afficher, 0, -1);
$count_online = $count_visiteurs + $count_membres;
echo '<p> &nbsp; &nbsp; Il y a '.$count_online.' connectés : '.$count_membres.' membres et '.$count_visiteurs.' invités';
echo $texte_a_afficher.'</p><br/>';
echo '<br/><p> &nbsp; &nbsp; <a href="../admin/accueil-admin.php> Administration du forum </a>';
$query->CloseCursor();

?>
</div>
