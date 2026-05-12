<?php
include('config.php');

echo "<br><br>";
echo "<center><table width='1000'><tr><td valign='top' align='left'>";
echo "<fieldset><legend><b>Chris</b></legend><table class='table'><tr><th><b>#</b></th><th width='300'><b>Naam</b></th><th><b>Categorie</b></th></tr>";
$sql = "SELECT * FROM dvd WHERE koffer = 1 ORDER BY nummer ASC";
$select = mysqli_query($link_id, $sql);
$count = 0;
while ($dvd = mysqli_fetch_array($select)) {
	$count++;
	$sqlcat = "SELECT * FROM categorie WHERE id = ".$dvd['categorie'];
	$selectcat = mysqli_query($link_id, $sqlcat);
	while ($categorie = mysqli_fetch_array($selectcat)) {
	$even_odd = $count % 2 ? "even" : "odd";
	echo "<tr class='$even_odd'><td>".$dvd['nummer']."</td><td>".$dvd['naam']."</td><td>".$categorie['categorie']."</td></tr>";
	}
}
echo "</table></fieldset></td><td valign='top' align='left'>";

echo "<fieldset><legend><b>Gerard</b></legend><table class='table'><tr><th><b>#</b></th><th width='300'><b>Naam</b></th><th><b>Categorie</b></th></tr>";
$sql = "SELECT * FROM dvd WHERE koffer = 2 ORDER BY nummer ASC";
$select = mysqli_query($link_id, $sql);
$count = 0;
while ($dvd = mysqli_fetch_array($select)) {
	$count++;
	$sqlcat = "SELECT * FROM categorie WHERE id = ".$dvd['categorie'];
	$selectcat = mysqli_query($link_id, $sqlcat);
	while ($categorie = mysqli_fetch_array($selectcat)) {
	$even_odd = $count % 2 ? "even" : "odd";
	echo "<tr class='$even_odd'><td>".$dvd['nummer']."</td><td>".$dvd['naam']."</td><td>".$categorie['categorie']."</td></tr>";
	}
}
echo "</table></fieldset></td></tr></table></center>";

?>
