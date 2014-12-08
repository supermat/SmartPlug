<?php
// lecture des paramètres
$data = file_get_contents('php://input');
$data = json_decode($_POST["params"], true);
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
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

echo $result;
?>
