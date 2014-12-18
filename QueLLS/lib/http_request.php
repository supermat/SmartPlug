<?php
// function makeHTTPRequest
// exécute la requête HTTP
// l'url appelée est dans le paramètre, avec l'indice 'url'
function makeHTTPRequest($data) {
	global $debug;
	// isolation de l'url
	$url = $data["url"];
	unset($data["url"]);	//retire l'URL des paramètres
	// isolation de la méthode
	$method = "POST";
	if (isset($data["method"])) {
		$method = $data["method"]; 
		unset($data["method"]);
	}
	// isolation du Content-Type s'il est défini
	$content_type = "";
	if (isset($data["Content-type"])) {
		$content_type = "Content-type: ".$data["Content-type"]."; "; 
		unset($data["Content-type"]);
	}

	// use key 'http' even if you send the request to https://...
	$options = array(
		'http' => array(
			'header'  => "Authorization: Basic " . base64_encode("admin:admin") . "; ".
							$content_type,
//			Je n'ai pas trouvé quel Content-type il fallait mettre pour que ça fontionne en appelant record_state.php...
//			Si je laisse application/x-www-form-urlencoded, je n'ai aucun paramètre dans $_GET/$_POST
//			En revanche, sans Conten-type, l'appel HTTP à la prise renvoie une erreur
//			"Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => $method,
			'content' => http_build_query($data),
		),
	);
	
	if ($debug) {
		echo "Appel de ".$url;
		echo "<PRE> ";
		print_r($options);
		echo "</PRE> ";
	}
	
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);

	return $result;
}

?>
