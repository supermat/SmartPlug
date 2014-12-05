<?php
$command = "";
if (isset($_GET['command'])) { $command = $_GET['command']; }
if ($command=="" && isset($_POST['command'])) { $command = $_POST['command']; }

$url = ''; //'http://192.168.0.131/goform/SystemCommand';
if (isset($_GET['url'])) { $url = $_GET['url']; }
if ($url=="" && isset($_POST['url'])) { $url = $_POST['url']; }

$data = array('command' => $command
	//, 'key2' => 'value2'
	);

// use key 'http' even if you send the request to https://...
$options = array(
    'http' => array(
		'header'  => "Authorization: Basic " . base64_encode("admin:admin") . "; ".
		"Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    ),
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

echo $result;
?>
