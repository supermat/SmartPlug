<HTML>  
<HEAD>  
	<TITLE>Affichage du plan d'activation des prises</TITLE>
	<meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="main.css" />
</HEAD>
<BODY>  

<!--
	Paramètres d'appel de la page :
	<ul>
	<li>aucun</li>
	</ul>  
-->

<?php
define('BASE', './');
require_once(BASE.'lib/debug.php');
require_once(BASE.'lib/http_request.php');
require_once(BASE.'lib/smartplug.php');

//
ini_set('max_execution_time', 300);

// ouverture de la connexion à la base de données
require_once(BASE.'lib/connect_db.php');
open_mysql_connection();

// lecture de la liste des prises
$sql = "select * from plug where 1=1 ";
//$sql .= " and ip_address = '192.168.0.191'";	//pour debug
//if ($group!="") $sql .= " and id_group = ".$group;
$sql .= " order by name";
if ($debug) echo "sql=$sql<br>";

$glb_err=false;

$res = mysql_query($sql, $conn);
if (!$res) {
	$glb_err=true;
	$msg = "Echec de la requête SQL : ".mysql_error();
} else {
	if (mysql_num_rows($res) > 0) {
		// traitement des enregistrements 
		while ($row = mysql_fetch_assoc($res)) {
			// affiche le contenu
			echo "<h2>Planification courante pour ".utf8_encode($row['name'])." (".$row['ip_address'].")</h2>";
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
				$plan = getPlugPlan($row['ip_address'], $i);
				$action = "";
				if (isset($plan['action'])) $action = $plan['action'];
				$start = "";
				if (isset($plan['start'])) $start = $plan['start'];
				$end = "";
				if (isset($plan['end'])) $end = $plan['end'];
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
		}
	}
}



// *************************************************************************************
// rapport d'exécution de la requête
// *************************************************************************************
if ($glb_err) {
	echo  '<p class="G10RBOLD">Erreur dans le traitement de la requ&ecirc;te :</p>'.htmlentities($msg)."<br />";
} else {
//	echo "Requ&ecirc;te ex&eacute;cut&eacute;e avec succ&egrave;s<br />";
}
// fermeture de la connexion à la base
close_mysql_connection();

?>

</BODY>  
</HTML>  
