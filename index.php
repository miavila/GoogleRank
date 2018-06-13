<?php
// BUSCAR CADENA EN BASE_DIR //
// define ('DIR','/var/www/googlerank.sw/');
define ('DIR','C:/AppServ/www/googlerank.sw/');
define('BASE_DIR','http://www.google.com/search?hl=es&q=');
$palabras = array ('servidores+cloud', 'servidor+cloud', 'cloud+administrado', 'housing+servidores', 'cloud+profesional');
$cadena = 'swhosting';

// RECORRER PALABRAS EN GOOGLE //
foreach ($palabras as $palabra) {
	$url = BASE_DIR.$palabra;
	$html = file_get_contents($url);
	$enlaces = recuperar($html);
	posicionCadena($enlaces, $palabra, $cadena);
	mejorPosicion($palabra);
}

// RECUPERAR CODIGO HTML DE LOS BLOQUES DE LOS ENLACES //
function recuperar ($html){
	preg_match_all('|<cite>(.*?)</cite>|is',$html, $enlace);
	return $enlace[0];
}

// POSICION PALABRA CLAVE //
function posicionCadena ($enlaces, $palabra, $cadena){
	foreach ($enlaces as $key => $enlace){
		$existe = strpos ($enlace, $cadena);
		if ($existe!=''){
			escribirLog($palabra, $key);
		}
	}
}

// COMPROVAR CADENA EN ENLACES //
function buscarCadena ($enlace, $cadena){
	return strpos($enlace, $cadena);
}

// GRABAR LOG TXT //
function escribirLog($palabra,$key){
	$arch = fopen(DIR."/google_rank.txt", "a+");
	fwrite($arch, "[".date("Y-m-d")."]".";".$palabra.";".($key+1)."\n");
	fclose($arch);
}

// BUSCAR FEHA DE LA MEJOR POSICION PALABRA //
function mejorPosicion ($palabra){

	global $mejorPosicion;

	$archivo = file(DIR.'/google_rank.txt');
	$posicionFinal = 20;
	$fechaFinal = '';
	foreach ($archivo as $linea) {
		$parteArchivo = explode(';', $linea);
		$fecha = $parteArchivo[0];
		$nombrePalabra = $parteArchivo[1];
		$posicion = $parteArchivo[2];
		if ($nombrePalabra == $palabra && $posicion <= $posicionFinal){
			$posicionFinal = $posicion;
			$fechaFinal = $fecha;
		}
	}
	$mejorPosicion.=  $fechaFinal.' - '.$palabra.' - '.$posicionFinal;
}

// ENVIAR MAIL //
// $to = "my@domain.com";
// $subject = "Google Rank SWHosting";
// $message = file_get_contents(DIR.'/google_rank.txt')."\n".'Fechas Mejores Posiciones:'."\n".$mejorPosicion;
// $headers = "From: my@domain.com" . "\r\n" . "CC: your@domain.com";
// mail($to, $subject, $message, $headers);