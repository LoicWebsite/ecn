<?php
	# Emplacement des dossiers pour les compteurs de téléchargement et les fichiers à télécharger
	# (avec un / à la fin des chemins)
	$baseDir = realpath(__DIR__ . '/../');
	if ($baseDir === false) {
		http_response_code(500);
		die('Configuration error');
	}
	$hits = $baseDir . '/';
	$downloads = $baseDir . '/';

	#==============================================
	
	/**
	 * Fonction qui comptabilise le nombre de téléchargements
	 *
	 * @param	filename	nom et emplacement du fichier des hits
	 **/
	function hitsCounter($filename) {
		$counter=0;
		if(@file_exists($filename)) {
			$f = @fopen($filename, "r");
			@flock($f, LOCK_EX); //lock the file
			$counter = intval(@fgets($f)); 
			@fclose($f);
		}
		$f = @fopen($filename, "w");
		@flock($f, LOCK_EX); //lock the file
		@fwrite($f, $counter+1);
		@fclose($f);
	}
	
	/**
	 * Protège une chaine contre un null byte
	 *
	 * @param	string chaine à nettoyer
	 * @return	string chaine nettoyée
	*/
	function nullbyteRemove($string) {
		return str_replace("\0", '', $string);
	}

	$h_file = null; # fichier compteur de téléchargement
	$d_file = null; # fichier à télécharger

	# Liste blanche stricte des fichiers téléchargeables
	$allowedDownloads = [
		'Simulation.xlsx' => 'compteurXLS.txt',
		'Simulation.ods' => 'compteurODS.txt',
	];

	# Détermination des noms des fichiers
	if (!isset($_GET['file']) || empty($_GET['file'])) {
		http_response_code(400);
		die('Missing file parameter');
	}

	$filename = basename(nullbyteRemove($_GET['file']));
	if (!array_key_exists($filename, $allowedDownloads)) {
		http_response_code(403);
		die('File not allowed');
	}

	$h_file = $hits . $allowedDownloads[$filename];
	$d_file = $downloads . $filename;

	# Contrôle de l'existence du fichier à télécharger
	if(!file_exists($d_file)) {
		die('File not found');
		exit;
	}
	
	# Mise à jour du compteur de téléchargement du fichier
	hitsCounter($h_file);
	
	# Envoi du fichier à l'utilisateur
	header('Content-Description: File Transfer');
	header('Content-Type: application/download');
	header('Content-Disposition: attachment; filename='.basename($d_file));
	header('Content-Transfer-Encoding: binary');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: no-cache');
	header('Content-Length: '.filesize($d_file));
	readfile($d_file);
	exit;
	
?>