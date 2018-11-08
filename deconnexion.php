<?php
session_start();
$titre="Déconnexion";
include("includes/identifiants.php");
include("includes/debut.php");


session_destroy();
$query=$db->prepare('DELETE FROM forum_whosonline WHERE online_id= :id');
$query->bindValue(':id',$id,PDO::PARAM_INT);
$query->execute();
$query->CloseCursor();

echo '<p>Vous êtes à présent déconnecté(e) !<br /></p>
<p>Cliquez <a href="index.php">ici</a> pour revenir à la page d\'accueil du forum.</p><br />';
echo '</div></body></html>';
?>
<?php
session_start();
if (isset ($_COOKIE['pseudo']))
{
setcookie('pseudo', '', -1);
}
session_destroy();
?>
<?php
include("includes/footer_non_connecte.php");
?>