<?php
session_start();
session_destroy();
$titre="Déconnexion";
include("../includes/debut.php");

if ($id==0) erreur(ERR_IS_NOT_CO);

echo '<p>Vous êtes à présent déconnecté <br />

Cliquez <a href="../index.php">ici</a> pour revenir à la page principale</p>';
echo '</div></body></html>';
?>
