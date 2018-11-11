<?php
session_start();
$titre="Déconnexion";
include("../includes/identifiants.php");
include("../includes/debut.php");

echo'<i><p class="p_baniere">Vous êtes ici : </i><a href ="../index.php"> Accueil forum</a>  ||  Déconnexion<br/><br/>';

session_destroy();
$query=$db->prepare('DELETE FROM forum_whosonline WHERE online_id= :id');
$query->bindValue(':id',$id,PDO::PARAM_INT);
$query->execute();
$query->CloseCursor();

echo '<p class="p_message">Vous êtes à présent déconnecté(e) !<br /></p>
<p class="p_message">Cliquez <a href="../index.php">ici</a> pour revenir à la page d\'accueil du forum.</p><br />';
echo '</div></body></html>';
?>
<?php
session_start();
if (isset ($_COOKIE['pseudo']))
{
setcookie('pseudo', '', -1);
}
session_destroy();

header('Location: accueil.php');
exit;
?>