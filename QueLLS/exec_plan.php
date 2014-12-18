<HTML>  
<HEAD>  
	<TITLE>Exécution du plan d'activation des prises</TITLE>
	<meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="main.css" />
</HEAD>
<BODY>  

<!--
	Paramètres d'appel de la page :
	<ul>
	<li>date = date pour laquelle la planification est faite (par défaut : date du jour)</li>
	<li>group = identifiant du groupe à exécuter (par défaut : tous)</li>
	</ul>  
-->

<?php
define('BASE', './');
require_once(BASE.'lib/debug.php');
require_once(BASE.'lib/heure_soleil.php');
require_once(BASE.'lib/http_request.php');
require_once(BASE.'lib/smartplug.php');

$date = "";
if (isset($_GET['date'])) $date=$_GET['date'];
$group = "";
if (isset($_GET['group'])) $group=$_GET['group'];

// Initialisation de la date + Heures du soleil
$today = new DateTime($date." 12:00:00");
$sun = getSunTimes("", 47.316, -5.033, $today->format('m'), $today->format('d'));
echo "<h1>Exécution du plan pour la date du ".$today->format('d/m/Y')." </h1>";

if ($debug) {
	echo "Aujourd'hui = ".$today->format('d/m/Y H:i:s');
	echo "Heures du soleil ";
	echo "<PRE> ";
	print_r($sun);
	echo "</PRE> ";
}
echo "Pour ce jour, <br/>";
echo "le soleil se lève à : ".$sun["sunrise"]->format('H:i')."<br/>";
echo "et se couche à : ".$sun["sunset"]->format('H:i')."<br/>";

// function replaceTags
//	dans une chaine de caractère, remplace tous les tags <xxx> par la valeur du champ xxx de l'enregistrement
// 	les tags doivent avoir le nom exact du champ dans la requête
//	command = texte à parcourir
//	record = enregistrement contenant les champs
function replaceTags($command, $record) {
	global $debug;
	// construit un tableau de tous les tags contenus dans la commande
	preg_match_all("/#.*?#/", $command, $tags);
	$tags = $tags[0];
	if ($debug) {
		echo "replaceTags / tags = ";
		echo "<PRE> ";
		print_r($tags);
		echo "</PRE> ";
	}
	// construit le tableau des valeurs de remplacement à partir des champs de l'enregistrement
	$repValues = Array(); // valeurs de remplacement;
	foreach ($tags as $id => $value) {
		$field = substr($value, 1, strlen($value)-2);
		$repValues[$id] = $record[$field];
	}
	// transforme le tag en expression régulière pour le preg_replace
	foreach ($tags as $id => $value) {
		$tags[$id] = '/'.$value.'/';		
	}
	if ($debug) {
		echo "replaceTags / repValues = ";
		echo "<PRE> ";
		print_r($repValues);
		echo "</PRE> ";
	}
	// renvoie la commande avec les tags remplacés par des valeurs
	return preg_replace($tags, $repValues, $command);
}

//
ini_set('max_execution_time', 300);

// ouverture de la connexion à la base de données
require_once(BASE.'lib/connect_db.php');
open_mysql_connection();

// lecture des tâches à effectuer
$sql = "select * from exec_plan where 1=1 ";
//$sql .= " and ip_address = '192.168.0.191'";	//pour debug
if ($group!="") $sql .= " and id_group = ".$group;
$sql .= " order by plug_name, time_slot";
if ($debug) echo "sql=$sql<br>";

$glb_err=false;

$res = mysql_query($sql, $conn);
if (!$res) {
	$glb_err=true;
	$msg = "Echec de la requête SQL : ".mysql_error();
} else {
	if (mysql_num_rows($res) > 0) {
		// traitement des enregistrements du plan 1 à 1
		$plug_name = "";
		$plug_address = "";
		$params = Array(
						"url" => "",
					);
		$last_url = "";
		
		while ($row = mysql_fetch_assoc($res)) {
			$url = replaceTags($row["command"], $row);
			// Test de rupture
			// - si l'enregistrement courant concerne une autre prise
			// - ou une autre URL
			// alors déclencher l'appel
			if (($plug_address != "" && $plug_address != $row["ip_address"]) 
				|| ($params["url"] != "" && $params["url"] != $url)) {
				// exécution des commandes pour l'adresse de l'itération précédente
				setPlugTimer($plug_name, $plug_address, $params);
				// RAZ des variables
				$params = Array(
								"url" => "",
							);
			} 
			// init de la boucle
			$plug_name = utf8_encode($row["plug_name"]);
			$plug_address = $row["ip_address"];
			if ($debug) 
				echo "Traitement de l'enregistrement ".$row["plug_name"]." - Slot ".$row["time_slot"]."<br />";
			// Conditions d'exécution de la commande
			// 1. test que la commande est active
			$condition = $row["f_enabled"];
			// 2. test du jour
			//$today = date( "w", strtotime($date." 12:00:00"));
			$condition &= (substr($row["days_of_week"], $today->format('w'), 1)=='1');
			// 3. test de la pertinence d'allumer par rapport au soleil
			$start =  DateTime::createFromFormat('H:i:s', $row["start_time"]);
			$end =  DateTime::createFromFormat('H:i:s', $row["end_time"]);
			if ($row["f_follow_sun"]) {
				// Cas 1 : allumage le matin par rapport au lever du soleil
				if ($start->format('H') < 12) {
					// si le soleil se lève avant l'heure de début, pas besoin d'allumer
					if ($sun["sunrise"]->format('H:i') < $start->format('H:i')) {
						$condition = false;
					} else {
					//sinon on allume à l'heure prévue et on éteint dès que le soleil est levé
						if ($sun["sunrise"]->format('H:i') < $end->format('H:i')) {
							$end = $sun["sunrise"];
						}
					}
				} 
				else {
				// Cas 2 : allumage le soir par rapport au coucher du soleil
					// si le soleil se couche après l'heure de fin, pas besoin d'allumer
					if ($sun["sunset"]->format('H:i') > $end->format('H:i')) {
						$condition = false;
					} else {
					//sinon on allume dès que le soleil se couche et on éteint à l'heure prévue
						if ($sun["sunset"]->format('H:i') > $start->format('H:i')) {
							$start = $sun["sunset"];
						}
					}
				}
			}
			// 4. Application de la variation d'horaire aléatoire
			if ($debug) {
				echo "Max Offset = ".$row["max_offset"]."<br />";
				echo "Heures fixes = ".$row["f_fixed_hour"]."<br />";
			}
			$max_offset = $row["max_offset"];
			if (!$row["f_fixed_hour"] && $max_offset>0) {
				// variation sur l'heure de début
				$offset = intval(rand(-$max_offset, $max_offset));
				$start->modify($offset.' minutes');
				if ($debug) echo "Application d'une variation de ".$offset." minutes sur l'heure de début<br />";
				// variation sur l'heure de fin
				$offset = intval(rand(-$max_offset, $max_offset));
				$end->modify($offset.' minutes');
				if ($debug) echo "Application d'une variation de ".$offset." minutes sur l'heure de fin<br />";
			}
			// Modification de l'enregistrement avec les nouvelles heures calculées
			$row["start_time"] = $start->format('H:i:s');
			$row["start_time_hr"] = $start->format('H');
			$row["start_time_mn"] = $start->format('i');
			$row["end_time"] = $start->format('H:i:s');
			$row["end_time_hr"] = $end->format('H');
			$row["end_time_mn"] = $end->format('i');

			// fabrication du tableau de paramètres
			//$params["url"] = 'http://'.$row["ip_address"].'/goform/GreenAP';
			$params["url"] = replaceTags($row["command"], $row);
			if ($condition) {
				$params = array_merge($params, json_decode(replaceTags($row["parameters"], $row), true));
			}
			if ($debug) {
				echo "params final = ";
				echo "<PRE> ";
				print_r($params);
				echo "</PRE> ";
			}
		}
		// exécution des commandes pour l'adresse de la dernière itération
		if ($plug_address != "") { 
			setPlugTimer($plug_name, $plug_address, $params);
		}
	}
}



// *************************************************************************************
// rapport d'exécution de la requête
// *************************************************************************************
if ($glb_err) {
	echo  '<p class="G10RBOLD">Erreur dans le traitement de la requ&ecirc;te :</p>'.htmlentities($msg)."<br />";
} else {
	echo "Requ&ecirc;te ex&eacute;cut&eacute;e avec succ&egrave;s<br />";
}
// fermeture de la connexion à la base
close_mysql_connection();

?>

</BODY>  
</HTML>  
