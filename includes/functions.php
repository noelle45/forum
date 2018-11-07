<?php
function erreur($err='')
{
   $mess=($err!='')? $err:'Une erreur inconnue s\'est produite';
   exit('<p>'.$mess.'</p>
   <h3>Vous n\'êtes pas connecté ou vous n\'avez pas l\'autorisation d\'effectuer cette action <br/> Cliquez <a href="./accueil.php">ici</a> pour revenir à la page d\'accueil</h3></div></body></html>');
}   
?>

<?php
function move_avatar($avatar)
{
    $extension_upload = strtolower(substr(  strrchr($avatar['name'], '.')  ,1));
    $name = time();
    $nomavatar = str_replace(' ','',$name).".".$extension_upload;
    $name = "../membres/avatars/".str_replace(' ','',$name).".".$extension_upload;
    move_uploaded_file($avatar['tmp_name'],$name);
    return $nomavatar;
}
?>

<?php
function verif_auth($auth_necessaire)
{
$level=(isset($_SESSION['level']))?$_SESSION['level']:1;
return ($auth_necessaire <= intval($level));
}
?>

<?php
//function gest_err($err_no, $err_mesg)
//{
//	echo '<p>-</p>';
//}
//	set_error_handler('gest_err',E_NOTICE);
?>

<?php 
//fonction cryptage md5
function Cryptage($MDP, $Clef){
						
	$LClef = strlen($Clef);
	$LMDP = strlen($MDP);
						
	if ($LClef < $LMDP){
				
		$Clef = str_pad($Clef, $LMDP, $Clef, STR_PAD_RIGHT);
	
	}
				
	elseif ($LClef > $LMDP){

		$diff = $LClef - $LMDP;
		$_Clef = substr($Clef, 0, -$diff);

	}
			
	return $MDP ^ $Clef; // La fonction envoie le texte crypté
			
}

?>




















