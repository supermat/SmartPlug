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
	$p_params["Content-type"]  = "application/x-www-form-urlencoded";
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
		"url" 			=> "http://".$p_address."/goform/SystemCommand", 
		"Content-type"  => "application/x-www-form-urlencoded",
		"command" 		=> 'GetInfo '.$p_info,
		);
	// exécute la requête
	$result = makeHTTPRequest($params);
	if ($debug) echo "Retour HTTP = ".$result;
	// parse le résultat pour trouver la valeur
	$tag = 'textarea';
	preg_match("/<".$tag."[^>]*>(.*?)<\\/".$tag.">/si", $result, $match);
	$content = $match[1];
	if ($debug) echo "Contenu de la balise = ".$content;
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
	
	if ($debug) echo "Valeur retournée = ".$result;
	return $result;
}

// getPlugPlan
//	lit le plan d'exécution de la prise
//	p_address = addresse IP de la prise
//	p_slot = numéro d'emplacement
//
function getPlugPlan($p_address, $p_slot) {
	global $debug;
	// construit l'appel HTTP pour la lecture de l'action
	$params = Array(
		"url" 			=> "http://".$p_address."/goform/SystemCommand", 
		"Content-type"  => "application/x-www-form-urlencoded",
		"command" 		=> 'nvram_get 2860 GreenAPAction'.$p_slot,
		);
	// exécute la requête
	$result = makeHTTPRequest($params);
	if ($debug) echo "Retour HTTP = ".$result;
	// parse le résultat pour trouver la valeur
	$tag = 'textarea';
	preg_match("/<".$tag."[^>]*>(.*?)<\\/".$tag.">/si", $result, $match);
	$plan['action'] = $match[1];
	
	// construit l'appel HTTP pour la lecture de l'heure de début
	$params = Array(
		"url" 			=> "http://".$p_address."/goform/SystemCommand", 
		"Content-type"  => "application/x-www-form-urlencoded",
		"command" 		=> 'nvram_get 2860 GreenAPStart'.$p_slot,
		);
	// exécute la requête
	$result = makeHTTPRequest($params);
	if ($debug) echo "Retour HTTP = ".$result;
	// parse le résultat pour trouver la valeur
	$tag = 'textarea';
	preg_match("/<".$tag."[^>]*>(.*?)<\\/".$tag.">/si", $result, $match);
	$plan['start'] = preg_replace("/(\d+) (\d+)/", '$2:$1', $match[1]);

	// construit l'appel HTTP pour la lecture de l'heure de fin
	$params = Array(
		"url" 			=> "http://".$p_address."/goform/SystemCommand", 
		"Content-type"  => "application/x-www-form-urlencoded",
		"command" 		=> 'nvram_get 2860 GreenAPEnd'.$p_slot,
		);
	// exécute la requête
	$result = makeHTTPRequest($params);
	if ($debug) echo "Retour HTTP = ".$result;
	// parse le résultat pour trouver la valeur
	$tag = 'textarea';
	preg_match("/<".$tag."[^>]*>(.*?)<\\/".$tag.">/si", $result, $match);
	$plan['end'] = preg_replace("/(\d+) (\d+)/", '$2:$1', $match[1]);

	
	if ($debug) echo "Valeur retournée = ".$plan;
	return $plan;
}

?>

