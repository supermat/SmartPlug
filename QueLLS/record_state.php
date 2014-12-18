<HTML>  
<HEAD>  
	<TITLE>Enregistrement de l'état d'une prise</TITLE>
	<!--meta charset="UTF-8"-->
	<link rel="stylesheet" type="text/css" href="main.css" />
</HEAD>
<BODY>  

<?php
define('BASE', './');
require_once(BASE.'lib/debug.php');
require_once(BASE.'lib/http_request.php');
require_once(BASE.'lib/smartplug.php');

// getURLparameter
//	récupère un paramètre de l'URL quel que soit la méthode (GET/POST) et renvoie la valeur
//	p_name = nom du paramètre
function getURLparameter($p_name) {
	$r="";
	if (isset($_GET[$p_name])) $r=$_GET[$p_name];
	if (isset($_POST[$p_name])) $r=$_POST[$p_name];
	return $r;
}

$show_help_message = !(getURLparameter("nohelp")=="true");	// détermine l'affichageou non du message d'aide

if ($show_help_message) {
?>
Paramètres d'appel de la page :
<ul>
<li>Mode 'save' : les informations sont collectées en amont de l'appel de cette page, la page se contente de les enregistrer dans la base de données</li>
	<ul>
	<li>mode = save</li>
	<li>id = Identifiant de la prise, tel que défini dans la BDD</li>
	<li>A = intensité en Ampères</li>
	<li>V = tension en Volts</li>
	<li>W = puissance en Watts</li>
	<li>E = consommation en Watts</li>
	<br />
	Exemple : http://localhost/quells/record_state.php?mode=save&id=99&I=0.1&V=230.15&W=23.01&E=46.02
	</ul>  
	<br />
<li>Mode 'fetch' (défaut): les informations sont obtenues par interrogation de la prise puis enregistrées dans la base de données</li>
	<ul>
	<li>mode = fetch</li>
	<li>id = Identifiant de la prise, tel que définit dans la BDD</li>
	<li>ip_address = adresse IP de la prise</li>
	<br />
	Exemple : http://localhost/quells/record_state.php?id=2&ip_address=192.168.0.192
	</ul>  
	<br />
<li>Quel que soit le mode, le paramètre debug affiche des infos de débogage</li>
<li>Le paramètre nohelp=true n'affiche pas ce message</li>
</ul>  

<?php
}

// Initialisation des variables
$plug_id = getURLparameter("id");					// id de la prise en base
$ip_address = getURLparameter("ip_address");		// adresse IP de la prise
$mode = getURLparameter("mode");  					// mode de fonctionnement : fetch = interroge la prise; save = enregistre l'état passé en paramètre de l'URL
if ($mode=="") $mode = "fetch";	

echo "Enregistrement de l'état de la prise id=$plug_id ($ip_address) <br />";		
echo "Mode = ".$mode."<br />";

if ($mode=="save") {
	// récupération des valeurs à sauvegarder
	$intensity = getURLparameter("I");
	$voltage = getURLparameter("V");
	$power = getURLparameter("W");
	$power_sum = getURLparameter("E");
}

if ($mode=="fetch") {
	// lecture des valeurs à sauvegarder
	$intensity = getPlugInfo($ip_address, "I");
	$voltage = getPlugInfo($ip_address, "V");
	$power = getPlugInfo($ip_address, "W");
	$power_sum = getPlugInfo($ip_address, "E");
}

//
ini_set('max_execution_time', 300);

// ouverture de la connexion à la base de données
require_once(BASE.'lib/connect_db.php');
open_mysql_connection();
$glb_err=false;
$msg = "";

// fabrication de la requête
$sql = "insert into state_history ";
$sql .= "(id_plug, record_date, intensity, voltage, power, agr_power)";
$sql .= "values (";
$sql .= $plug_id.", ";
$sql .= "NOW(), ";
$sql .= $intensity.", ";
$sql .= $voltage.", ";
$sql .= $power.", ";
$sql .= $power_sum;
$sql .= ");";
echo "Exécution de la requête SQL = ".$sql."<br />";

// exécution de la requête
$res = mysql_query($sql, $conn);
if (!$res) {
	$glb_err=true;
	$msg = "Echec de la requête SQL : ".mysql_error();
}


// *************************************************************************************
// rapport d'exécution de la requête
// *************************************************************************************
if ($glb_err) {
	echo  '<p class="G10RBOLD">Erreur dans le traitement de la requ&ecirc;te :</p>'.htmlentities($msg)."<br />";
} else {
	echo "Requ&ecirc;te ex&eacute;cut&eacute;e avec succ&egrave;s<br />";
}// fermeture de la connexion à la base
close_mysql_connection();

?>

</BODY>  
</HTML>  
