<?php
/******************************************************************************/
/*                                                                            */
/*                       __        ____                                       */
/*                 ___  / /  ___  / __/__  __ _____________ ___               */
/*                / _ \/ _ \/ _ \_\ \/ _ \/ // / __/ __/ -_|_-<               */
/*               / .__/_//_/ .__/___/\___/\_,_/_/  \__/\__/___/               */
/*              /_/       /_/                                                 */
/*                                                                            */
/*                                                                            */
/******************************************************************************/
/*                                                                            */
/* Titre          : Calcul du lever et coucher du soleil n'importe quand et...*/
/*                                                                            */
/* URL            : http://www.phpsources.org/scripts385-PHP.htm              */
/* Auteur         : Olravet                                                   */
/* Date édition   : 07 Mai 2008                                               */
/* Website auteur : http://olravet.fr/                                        */
/*                                                                            */
/******************************************************************************/
?>
<?php
function getSunTimes(
		$fh,		// fuseau horaire
		$La,		// Latitude
		$Lo,		// Longitude
		$mois,
		$jour
	) 
{
	$returnArray = Array(
		"sunrise" => null,
		"sunset" => null,
		);
		
	IF ($fh == "") {$fh = date("H") - gmdate("H") ;}
	IF ($La == "") { $La = 48.833;}
	IF ($Lo == "") { $Lo = -2.333;}
	IF ($mois == "") {$mois = date("m") ;}
	IF ($jour == "") {$jour = date("d") ;}
	// Fuseau horaire et coordonnées géographiques
	$k = 0.0172024;
	$jm = 308.67;
	$jl = 21.55;
	$e = 0.0167;
	$ob = 0.4091;
	$PI= 3.1415926536;
	//Hauteur du soleil au lever et au coucher
	$dr = $PI/ 180;
	$hr = $PI/ 12;
	$ht = (-40 / 60);
	$ht = $ht * $dr;
	$La = $La * $dr;
	$Lo = $Lo * $dr;
	//Date
	IF ($mois < 3) {
		$mois = $mois + 12;
	}
	//Heure TU du milieu de la journée
	$h = 12 + ($Lo / $hr);
	//Nombre de jours écoulés depuis le 1 Mars O h TU
	$J = floor(30.61 * ($mois + 1)) + $jour + ($h / 24) - 123;
	//Anomalie et longitude moyenne
	$M = $k * ($J - $jm);
	$L = $k * ($J - $jl);
	//Longitude vraie
	$S =$L + 2 * $e * Sin($M) + 1.25 * $e * $e * Sin(2 * $M);
	//Coordonnées rectangulaires du soleil dans le repère équatorial
	$X = Cos($S);
	$Y = Cos($ob) * Sin($S);
	$Z = Sin($ob) * Sin($S);
	//Equation du temps et déclinaison
	$R = $L;
	$rx = Cos($R) * $X + Sin($R) * $Y;
	$ry = -Sin($R) * $X + Cos($R) * $Y;
	$X = $rx;
	$Y = $ry;
	$ET = atan($Y / $X);
	$DC = atan($Z / Sqrt(1 - $Z * $Z));
	//Angle horaire au lever et au coucher
	$cs = (Sin($ht) - Sin($La) * Sin($DC)) / Cos($La) / Cos($DC);
	$CalculSol = "";
	IF ($cs > 1) { $CalculSol = "Ne se lève pas";}
	IF ($cs < -1) { $CalculSol = "Ne se couche pas";}
	IF ($cs == 0) {
		$ah = $PI / 2;
	}ELSE{
		$ah = atan(Sqrt(1 - $cs * $cs) / $cs);
	}
	IF ($cs < 0) { $ah = $ah + $PI;}
	//Lever du soleil
	$Pm = $h + $fh + ($ET - $ah) / $hr;
	IF ($Pm < 0) { $Pm = $Pm + 24;}
	IF ($Pm > 24) { $Pm = $Pm - 24;}
	$hs = floor($Pm);
	$Pm = floor(60 * ($Pm - $hs));
	IF (strlen($hs)<2) {$hs = "0".$hs;}
	IF (strlen($Pm)<2) {$Pm = "0".$Pm;}
	IF ($CalculSol ==""){
		$lev = $hs. ":" .$Pm;
	}ELSE{
		$lev = "00:00";
	}
	//Coucher du soleil
	$Pm = $h + $fh + ($ET + $ah) /$hr;
	IF ($Pm > 24) { $Pm = $Pm - 24;}
	IF ($Pm < 0) { $Pm = $Pm + 24;}
	$hs = floor($Pm);
	$Pm = floor(60 * ($Pm - $hs));
	IF (strlen($hs)<2) {$hs = "0".$hs;}
	IF (strlen($Pm)<2) {$Pm = "0".$Pm;}
	IF ($CalculSol ==""){
		$couch = $hs. ":" .$Pm;
	}ELSE{
		$couch  = "00:00";
	}
	$returnArray["sunrise"] = date("d/M/y G:i:s", strtotime($lev));
	$returnArray["sunset"] = date("d/M/y G:i:s", strtotime($couch));
	$returnArray["sunrise"] =  DateTime::createFromFormat('H:i', $lev);
	$returnArray["sunset"] =  DateTime::createFromFormat('H:i', $couch);
	
	
	return $returnArray;
}
?>
