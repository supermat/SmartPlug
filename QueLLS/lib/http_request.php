<?php
// function makeHTTPRequest
// exécute la requête HTTP
// l'url appelée est dans le paramètre, avec l'indice 'url'
function makeHTTPRequest($data) {
	global $debug;
	// isolation de l'url
	$url = $data["url"];

	// use key 'http' even if you send the request to https://...
	$options = array(
		'http' => array(
			'header'  => "Authorization: Basic " . base64_encode("admin:admin") . "; ".
			"Content-type: application/\r\n",
			'method'  => 'POST',
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
