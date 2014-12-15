<?php
$debug=false;
if (isset($_GET['debug'])) $debug=true;
if (isset($_POST['debug'])) $debug=true;
if ($debug)	echo "Mode debug actif à ".date('Y-m-d G:i')."<br>";


function getmicrotime(){ 
  list($usec, $sec) = explode(" ",microtime()); 
  return ((float)$usec + (float)$sec); 
}

// array2xml : écrit le contenu d'un tableau au format xml. Les clés du tableau sont les balises.
// 	$array = le tableau à écrire
//	$tag = nom de la balise racine
// 	$level = niveau d'indentation
function array2xml($array, $tag, $level) {
	$ret = "";
	$ret .= str_repeat("\t", $level)."<".$tag.">\n";
	foreach (array_keys($array) as $key) {
		$ret .= str_repeat("\t", $level+1)."<".$key.">".$array[$key]."</".$key.">\n";
	}
	$ret .= str_repeat("\t", $level)."</".$tag.">\n";
	
	return $ret;
}

$debug_time = Array("start" => getmicrotime());

function printGenerationTime() {
	$debug_time["end"] = getmicrotime();
	
	if ($debug) 
		echo "Page générée en ".round($debug_time["end"]  - $debug_time["start"], 3) ." secondes"; 
}

?>
