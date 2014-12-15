<?php
require_once(BASE.'lib/http_request.php');

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

// getPlugInfo
//	lit une info de la prise
//	p_address = addresse IP de la prise
//	p_info = type d'info à lire (I = Intensité, V = Voltage, W = Puissance, E = puissance cumulée)
//
function getPlugInfo($p_address, $p_info) {
	global $debug;
	// construit l'appel HTTP
	$params = Array(
		"url" => "http://".$p_address."/goform/SystemCommand", 
		"command" => 'GetInfo '.$p_info
		);
	// exécute la requête
	$result = makeHTTPRequest($params);
	// parse le résultat pour trouver la valeur
	$tag = 'textarea';
	preg_match("/<".$tag."[^>]*>(.*?)<\\/".$tag.">/si", $result, $match);
	$content = $match[1];
	if ($debug) echo "Retour HTTP = ".$content;
	$pattern = "/[^\s]\s+(\d*)/";
	preg_match($pattern, $content, $match);
	switch ($p_info) {
		case 'I' :
		case 'V' :
			$result = $match[1] * 0.001;
			break;
		case 'W' :
		case 'E' :
			$result = $match[1] * 0.01;
			break;
	}
	
	return $result;
}


?>

