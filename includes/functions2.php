<meta http-equiv="refresh" content="3 ; url=./membres/accueil.php">
<?php
function erreur($err='')
{
   $mess=($err!='')? $err:'';
   exit('<p>'.$mess.'</p>
   <p class"p_message"><br/>Vous êtes déjà connecté(e) <br/> <a href ="membres/accueil.php">Retour accueil</a><br/> Redirection dans 3 secondes <img src="messagerie/img/langue.png"/></p></div></body></html>');
}   
?>