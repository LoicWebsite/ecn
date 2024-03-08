<?php
	# Emplacement des dossiers pour les compteurs de téléchargement et les fichiers à télécharger
	# (avec un / à la fin des chemins)
	
//	$hits 		= dirname(__FILE__).'/';
//	$downloads 	= dirname(__FILE__).'/';
	$hits 		= '../';
	$downloads 	= '../';

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
	
	# Détermination des noms des fichiers
	if(isset($_GET['file']) AND !empty($_GET['file'])) {
		$filename = basename(nullbyteRemove($_GET['file']));
		if ($filename == "Simulation.xlsx") {
			$h_file = $hits."compteurXLS.txt";
		} elseif ($filename == "Simulation.ods") {
			$h_file = $hits."compteurODS.txt";
		}
		$d_file = $downloads.$filename;
	}

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