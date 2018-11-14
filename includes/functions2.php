<meta http-equiv="refresh" content="1 ; url=../index.php">
<?php
function erreur($err='')
{
   $mess=($err!='')? $err:'';
   exit('<p class"p_message">'.$mess.'</p>
   <p class"p_message"><br/><br/> <a href ="membres/accueil.php">Retour accueil</a><br/> Redirection dans 1 seconde <img src="messagerie/img/langue.png"/></p></div></body></html>');
}   
?>
