<?php
session_start();
$titre = "Forum girly";
include("../includes/identifiants.php");
include("../includes/debut.php");
include("../includes/constants.php");

                $query=$db->prepare('SELECT membre_pseudo, membre_email,
                membre_siteweb, membre_signature, membre_msn, membre_localisation,
                membre_avatar, membre_rang, membre_mdp2
                FROM forum_membres WHERE membre_id=:id');
                $query->bindValue(':id',$id,PDO::PARAM_INT);
                $query->execute();
                $data=$query->fetch();
?>

<form method="post" action="../index.php">
    <input type="text" id="pseudo" value="<?php echo $data['membre_pseudo'] ?>">
    <label for="password"></label>
    <input type="password" id="password" placeholder="Mot de passe">
    <input type="hidden" name="page" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" />
    <input type="submit" class="arrondi" value="Envoyer" />
</form>

<?php
while ($data = $query->fetch())
echo $data['membre_pseudo'];
