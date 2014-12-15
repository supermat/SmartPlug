<?php
require_once(BASE.'lib/config.php');

function open_mysql_connection() {
	global $debug, $db_hostname, $db_login, $db_pwd, $db_name, $conn;
	$cnx_err=false;	// erreur de connexion
	
	if ($debug) echo "Ouverture de la connexion MySql<br />";
	$conn = mysql_connect($db_hostname, $db_login, $db_pwd);
	if (!$conn) {
		$cnx_err=true;
		$msg = "Echec de la connexion au serveur : ".mysql_error();
		if ($debug) echo $msg."<br>";
	}
	$db = mysql_select_db($db_name, $conn);
	if (!$db) {
		$cnx_err=true;
		$msg = "Echec de la connexion à la base : ".mysql_error();
		if ($debug) echo $msg."<br>";
	}
	return $cnx_err;
}

function close_mysql_connection() {
	global $debug, $conn;
	if ($debug) echo "Fermeture de la connexion MySql<br />";
	mysql_close($conn);
}

function open_odbc_connection() {
	global $debug, $ze_hostname, $ze_login, $ze_pwd, $conn_o;
	$cnx_err=false;	// erreur de connexion
	
	if ($debug) echo "Ouverture de la connexion ODBC<br />";
	$conn_o = odbc_connect($ze_hostname, $ze_login, $ze_pwd);

	if (!$conn_o) {
		$cnx_err=true;
		$msg = "Echec de la connexion au serveur : ".odbc_error();
		if ($debug) echo $msg."<br>";
	}
	return $cnx_err;
}
?>
