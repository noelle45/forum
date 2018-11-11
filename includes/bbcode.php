<?php
function code($texte)
{
//Smileys
$texte = str_replace(':D ', '<img src="../forum/img/heureux.png" title="heureux" alt="heureux" />', $texte);
$texte = str_replace(':lol: ', '<img src="../forum/img/smile.png" title="lol" alt="lol" />', $texte);
$texte = str_replace(':triste:', '<img src="../forum/img/triste.png" title="triste" alt="triste" />', $texte);
$texte = str_replace(':frime:', '<img src="../forum/img/cool.png" title="cool" alt="cool" />', $texte);
$texte = str_replace(':rire:', '<img src="../forum/img/langue.png" title="rire" alt="rire" />', $texte);
$texte = str_replace(':s', '<img src="../forum/img/huh.png" title="confus" alt="confus" />', $texte);
$texte = str_replace(':O', '<img src="../forum/img/choc.png" title="choc" alt="choc" />', $texte);
$texte = str_replace(':question:', '<img src="../forum/img/point-int.gif" title="?" alt="?" />', $texte);
$texte = str_replace(':exclamation:', '<img src="../forum/img/pirate.png" title="!" alt="!" />', $texte);

//Mise en forme du texte
//gras
$texte = preg_replace('`\[g\](.+)\[/g\]`isU', '<strong>$1</strong>', $texte); 
//italique
$texte = preg_replace('`\[i\](.+)\[/i\]`isU', '<em>$1</em>', $texte);
//souligné
$texte = preg_replace('`\[s\](.+)\[/s\]`isU', '<u>$1</u>', $texte);
//lien
$texte = preg_replace('#http://[a-z0-9._/-]+#i', '<a href="$0">$0</a>', $texte);
//etc., etc.
$texte = preg_replace('`\[quote\](.+)\[/quote\]`isU', '<div id="quote">$1</div>', $texte);


//On retourne la variable texte
return $texte;
}
?>
