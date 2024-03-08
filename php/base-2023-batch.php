<?php

	// fonctions communes et récupération-contrôle des paramètres
	include "controleParametre.php";
	include "fonctionECN.php";

	// initialisation des variables
	$numeroRecord = 0;
	$rangDernier = 0;
	$debutString = 0;
	$longueurString = 0;
	$nextIsCHU = false;
	$nextIsSpecialite = false;
	$CHU = "";
	$lastCHU = "";
	$specialite ="";
	$enteteLu = false;
	$heure = "00h00";
	$date = "lu01jan";
	$tooltip = "auncune donnée";
	$lien = "#";
	$aucuneOffre = false;
	$texteRangDernier = "";
	$premiereLigne = false;
	$listeSpecialite = array();
	$numeroSpecialite = 0;
	$codeSpecialiteBase = "";
	$nbCHUSpecialite = 0;
	$nbSpecialite = 0;

	// conexion à la base ecn 
	try {
		$db = new PDO("mysql:host=localhost;dbname=ecn;charset=utf8", "USER", "PASSE" );
	}
	catch(PDOException $erreur)	{
		die('Erreur : ' . $erreur->getMessage());
	}

	// passage au mode exception pour les erreurs
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "";

	// préparation de la connexion SSL pour les ouverture de page Web du CNG
	// comme ils ont un problème de certificats, on désactive le contrôle du certificat
	// (voir https://stackoverflow.com/questions/26148701/file-get-contents-ssl-operation-failed-with-code-1-failed-to-enable-crypto)
	$arrContextOptions=array(
		"ssl"=>array(
			"verify_peer"=>false,
			"verify_peer_name"=>false,
		),
	);  

	// récupération de la page rangs limites
	$page = fopen("https://www.cngsante.fr/chiron/celine/limite.html","r", false, stream_context_create($arrContextOptions)); //lecture du fichier

	// ******** format de la page lue
	//
	// ******** cellule renseignée
	// </span></a></td>  <td class="rn"><a href="https://www.cngsante.fr/chiron/celine/affect/norm/020437" target="norm020437" class="limite">3225<br>
	// 5017<span>dispo : 0<br>
	// placé : 3<br>
	// offre : 3<hr>
	// STRASBOURG<br>
	// Médecine intensive-réanimation<br>
	// 
	// ********* cellule vide
	// </span></a></td>  <td class="rn"><a href="https://www.cngsante.fr/chiron/celine/affect/norm/020438" target="norm020438" class="limite">&nbsp;&nbsp;&nbsp;&nbsp;<br>
	// &nbsp;&nbsp;&nbsp;&nbsp;<span>aucune offre<hr>
	// STRASBOURG<br>
	// Médecine légale et expertises médicales<br>
	//
	// ********* cas spécial de la 1ère colonne
	//   <td class="rg">STRASBOURG</td>  <td class="rn"><a href="https://www.cngsante.fr/chiron/celine/affect/norm/020457" target="norm020457" class="limite">&nbsp;&nbsp;&nbsp;&nbsp;<br>
	// &nbsp;&nbsp;&nbsp;&nbsp;<span>aucune offre<hr>
	// STRASBOURG<br>
	// En attente de publication<br>

	if ($page != false) {

// code temporaire en attendant que l'heure soit affichée dans la page limite.html
    	date_default_timezone_set('Europe/Paris');		// Définir le fuseau horaire
    	$date = date('d/m/y');							// Obtenir la date et l'heure actuelles
    	$heure = date('H:i');
// fin code temporaire
		while (!feof($page)) { //on parcourt toutes les lignes
			$ligne = fgets($page, 4096); // lecture du contenu de la ligne

			// récupération de la date et heure du listing
			if (strstr($ligne,"font-size : 12px ; font-weight : bold ; color : blue")) {
// à activer quand l'heure aparaîtra sur la page limite.html
// 				$debutString = strpos($ligne,"</span>") - 5;
// 				$heure = substr($ligne,$debutString,5);		
// 				$date = substr($ligne,$debutString +12,8);
// 				$date = str_replace("?","û", mb_convert_encoding($date, "UTF-8"));
			}

			// ligne d'entête : les code spécialités sont juste avant une balise <span> et sur la 1ère ligne qui a class="bleu". Boucle sur toute la ligne.
			elseif (strstr($ligne,'class="bleu"') and ($enteteLu == false)) {
				$enteteLu = true;
				$nbSpecialite = substr_count($ligne,"<span>");
				while ($nbSpecialite > 0) {
					$debutString = strpos($ligne,"<span>") - 3;
					$codeSpecialite = substr($ligne,$debutString,3);
					if ($codeSpecialite == ">BM") {
						$codeSpecialite = "&nbsp;&nbsp;BM&nbsp;";
						$codeSpecialiteBase = "BM";
					} else {
						$codeSpecialiteBase = $codeSpecialite;
					}
					$listeSpecialite[] = $codeSpecialiteBase;
					$ligne = substr($ligne,$debutString + 8);
					$nbSpecialite = $nbSpecialite - 1;	
				}
				$premiereLigne = true;
			}

			// 1ère ligne d'une cellule
			elseif (strstr($ligne,'<td class="rn">')) {
				$numeroRecord = 1;
				$debutString = strpos($ligne,"href=") + 6;
				$longueurString = strpos($ligne,"target") - $debutString -2;
				$lien = substr($ligne, $debutString, $longueurString);
			}

			// 2ème ligne d'une cellule vide
			elseif (strstr($ligne,"aucune offre")) {
				$numeroRecord = 2;
				$rangDernier = 0;
				$texteRangDernier = "";
				$aucuneOffre = true;
				$nextIsCHU = true;
			}

			// 2ème ligne d'une cellule non vide
			elseif (strstr($ligne,"<span>dispo")) {
				$numeroRecord = 2;
				$longueurString = strpos($ligne,"<span>dispo");
				$rangDernier = substr($ligne, 0, $longueurString);
				$texteRangDernier = $rangDernier;
			}

			// 3ème ligne d'une cellule non vide
			elseif (substr($ligne,0,7) == "placé :") {
				$numeroRecord = 3;
			}

			// 4ème ligne d'une cellule non vide
			elseif (substr($ligne,0,7) == "offre :") {
				$numeroRecord = 4;
				$nextIsCHU = true;
			}

			// 5ème ligne d'une cellule non vide ou 3ème ligne d'une cellule vide
			elseif ($nextIsCHU) {
				$numeroRecord = 5; // ou 3 si aucune offre
				$nextIsCHU = false;
				$longueurString = strpos($ligne,"<br>");
				$CHU = substr($ligne, 0, $longueurString);
				if ($CHU[0]== " ") {							// cas particulier où le nom commence (à tort) par un blanc dans la page CELINE: cas de ' LA REUNION'
					$CHU = substr($CHU, 1);
				}
				if ($CHU == "GUADELOUPE") {					// cas particulier en 2023 MARTINIQUE/POINTE A PITRE s'appelle GUADELOUPE
					$CHU = "MARTINIQUE/POINTE A PITRE";
				}
				$nextIsSpecialite = true;
			}

			// 6ème et dernière ligne d'une cellule non vide ou 4ème et dernière ligne d'une cellule vide
			elseif ($nextIsSpecialite) {
				$numeroRecord = 6; // ou 4 si aucune offre
				$nextIsSpecialite = false;
				$longueurString = strpos($ligne,"<br>");
				$specialite = substr($ligne, 0, $longueurString);

			// affichage de la cellule
				$href = "<a onclick='return false' ondblclick='window.open(this.href, &apos;_blank&apos;)' style='text-decoration:none; color:darkslategray;' " . $lien . " target=blank>";
				if ($aucuneOffre) {
					$aucuneOffre = false;
					$rangDernier = "";	
				}
				if (($rangDernier >= $rang) and ($rangDernier != 0)) {
					if  ($CHU == $lastCHU) {
						$numeroSpecialite += 1;
					} else {
						$lastCHU = $CHU;
						if (!$premiereLigne) {
						} else {
							$premiereLigne = false;
						}
						$numeroSpecialite = 0;
					}
				} else {
					if  ($CHU == $lastCHU) {
						$numeroSpecialite += 1;
					} else {
						$lastCHU = $CHU;
						if (!$premiereLigne) {
						} else {
							$premiereLigne = false;
						}
						$numeroSpecialite = 0;
					}
				}

				// mise à jour de la table RANG pour chaque cellule CHU-Specialité
				if ($rangDernier != "") {
					$where = " WHERE CHU like '" . $CHU . "%' AND CodeSpecialite='" . $listeSpecialite[$numeroSpecialite] . "' ";
					$sql = " UPDATE Rang SET Dernier2023=" . $rangDernier . ", URLCeline='" . $lien . "' " . $where . ";";
					if ($debug) {echo $sql . "<br/>";}
					try {
						$result = $db->query($sql);
					}
					catch(PDOException $erreur)	{
						echo "Erreur UPDATE Rang : " . $erreur->getMessage() . "<br/>";
					}
				$nbCHUSpecialite +=1;
				}				
			}
		}
	}
	if ($debug) {echo "***************** ".$nbCHUSpecialite . " CHU-Spécialités mises à jour *****************<br/>";}
	
	// recherche du dernier admis pour chaque spécialité
	$nbSpecialite = 0;
	foreach ($listeSpecialite as $specialite) {
		$max2023 = "";
		$maxCHU = "";
		$sql = "SELECT CHU, Dernier2023 FROM Rang WHERE Dernier2023 = (SELECT MAX(Dernier2023) FROM Rang WHERE CodeSpecialite = '" . $specialite . "');";
		if ($debug) {echo $sql . "<br/>";}
		try {
			$result = $db->query($sql);
			while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
				extract($row);
				$max2023 = $Dernier2023;
				$maxCHU = $CHU;
				if ($debug) {echo $specialite . " = " . $maxCHU . " : " . $max2023 . "<br/>";}
			}
		}
		catch(PDOException $erreur)	{
			echo "Erreur SELECT Max: " . $erreur->getMessage() . "<br/>";
		}

		// mise à jour de la table spécialité avec le dernier de chaque spécialité
		if (($max2023 != "") and ($maxCHU != "")) {
			$sql = "UPDATE Specialite SET Dernier2023 =" . $max2023 . ", CHUDernier2023 ='" . $maxCHU . "' WHERE CodeSpecialite='" . $specialite . "';";
			if ($debug) {echo $sql . "<br/>";}
			try {
				$result = $db->query($sql);
			}
			catch(PDOException $erreur)	{
				echo "Erreur UPDATE Specialité : " . $erreur->getMessage() . "<br/>";
			}
		$nbSpecialite += 1;
		}
	}
	if ($debug) {echo "***************** ".$nbSpecialite . " spécialités mises à jour *****************<br/>";}

	// au final, mise à jour de la date et heure de mise à jour (si le site n'était pas en maintenance, la date de maj est renseignée)
	if ($heure != "00h00") {
		$sql = "REPLACE INTO MiseAJour (Id, DateMiseAJour, HeureMiseAJour) VALUES ('0', '" . $date . "', '" . $heure . "');";
		if ($debug) {echo $sql . "<br/>";}
		try {
			$result = $db->query($sql);
		}
		catch(PDOException $erreur)	{
			echo "Erreur UPDATE MiseAjour : " . $erreur->getMessage() . "<br/>";
		}
	}

	// fermeture de la base
	if (isset($result)) {$result->closeCursor();}
	$db = null;
?>