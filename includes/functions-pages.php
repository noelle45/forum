<?php
function get_list_page($page, $nb_page, $link, $nb = 2){
$list_page = array();
for ($i=1; $i <= $nb_page; $i++){
if (($i < $nb) OR ($i > $nb_page - $nb) OR (($i < $page + $nb) AND ($i > $page -$nb)))
$list_page[] = ($i==$page)?'<strong>'.$i.'</strong>':'<a href="'.$link.'&amp;page='.$i.'">'.$i.'</a>'; 
else{
if ($i >= $nb AND $i <= $page - $nb)
$i = $page - $nb;
elseif ($i >= $page + $nb AND $i <= $nb_page - $nb)
$i = $nb_page - $nb;
$list_page[] = '...';
}
}
$print= implode('-', $list_page);
return $print;
}
?>





















