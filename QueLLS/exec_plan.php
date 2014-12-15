<HTML>  
<HEAD>  
	<TITLE>Exécution du plan d'activation des prises</TITLE>
	<meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="main.css" />
</HEAD>
<BODY>  

<?php
define('BASE', './');
require_once(BASE.'lib/debug.php');
require_once(BASE.'lib/heure_soleil.php');
require_once(BASE.'lib/http_request.php');

$date = "";
if (isset($_GET['date'])) $date=$_GET['date'];

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

// function setPlugTimer
//	programme une prise et affiche la commande à l'écran
//	p_name = nom dela prise (pour l'affichage)
//	p_address = adresse IP de la prise (pour l'affichage
//	p_params = paramètres de la requête HTTP
//
function setPlugTimer($p_name, $p_address, $p_params) {
	// affiche le contenu
	echo "<h2>Exécution des commandes pour ".$p_name." (".$p_address.")</h2>";
	// crée un tableau pour les emplacements
	echo '
	<table
		id="planGrid"
		class="pgui-grid grid legacy stripped">
		<thead>
			<tr class="header">
				<th>
                    <span>N° Emplacement</span>
                </th>
                <th>
                    <span>Action</span>
                </th>			
				<th>
                    <span>Heure de début</span>
                </th>
                <th>
                    <span>Heure de fin</span>
                </th>
            </tr>
		</thead>
		<tbody>';
	for ($i=1; $i<=4; $i++) {
		// lecture des valeurs de paramètres
		$action = "";
		if (isset($p_params['GAPAction'.$i])) $action = $p_params['GAPAction'.$i];
		$start = "";
		if (isset($p_params['GAPSHour'.$i])) $start = $p_params['GAPSHour'.$i].":".$p_params['GAPSMinute'.$i];
		$end = "";
		if (isset($p_params['GAPEHour'.$i])) $end = $p_params['GAPEHour'.$i].":".$p_params['GAPEMinute'.$i];
		// affichage des valeurs de paramètres
		echo '<tr class="pg-row">';
		echo '<td style="">'.$i.'</td>';
		echo '<td id="GreenAPAction'.$i.'" style="">'.$action.'</td>';
		echo '<td id="GreenAPStart'.$i.'" style="">'.$start.'</td>';
		echo '<td id="GreenAPEnd'.$i.'" style="">'.$end.'</td>';
		echo '</tr>';
	}
	echo '
		</tbody>
	</table>';
	// Appel HTTP Request
	makeHTTPRequest($p_params); 

}

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
$sql = "select * from exec_plan where time_slot is not null ";
$sql .= " and ip_address = '192.168.0.191'";
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
		$params = Array();
		
		while ($row = mysql_fetch_assoc($res)) {
			// Test de rupture
			if ($plug_address != "" && $plug_address != $row["ip_address"]) {
				// exécution des commandes pour l'adresse de l'itération précédente
				setPlugTimer($plug_name, $plug_address, $params);
				// RAZ des variables
				$params = Array();
			} 
			// init de la boucle
			$plug_name = $row["plug_name"];
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
			$max_offset = $row["max_offset"];
			if (!$row["f_fixed_hour"] && $max_offset>0) {
				// variation sur l'heure de début
				$offset = intval(rand(-$max_offset, $max_offset));
				$start->modify($offset.' minutes');
				// variation sur l'heure de fin
				$offset = intval(rand(-$max_offset, $max_offset));
				$end->modify($offset.' minutes');
			}

			// fabrication du tableau de paramètres
			//$params["url"] = 'http://'.$row["ip_address"].'/goform/GreenAP';
			$params["url"] = replaceTags($row["command"], $row);
			if ($condition) {
				/*
				$slot = $row["time_slot"];
				$params["GAPAction".$slot] = $row["command"];
				// calcul de l'heure de début
				$params["GAPSHour".$slot] = $start->format('H');
				$params["GAPSMinute".$slot] = $start->format('i');
				// calcul de l'heure de fin
				$params["GAPEHour".$slot] = $end->format('H');
				$params["GAPEMinute".$slot] = $end->format('i');
				*/
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
