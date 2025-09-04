<?php

/*****************************************************************************************
	script pour concaténer les colonnes des 2 tables Rang et CESP.
*****************************************************************************************/
	
	$debug = true;
	// récupération du paramètre pour passer en mode debug (par défaut pas de debug)
	if (isset($_GET['debug']))  {
		if ($_GET['debug'] == "true") {
			$debug=true;
		}
	}
	if ($debug) {echo "********** début de script *********\n";}


	// fontions communes
	require_once "php/fonctionECN.php";

	// ouverture de la base de données
	$db = openDatabase();
		
	// préparation de la requête pour lire tous les lignes de la table Rang
	$sql = "SELECT CodeSpecialite, CHU FROM Rang";
	if ($debug) {echo "SQL = " . $sql . "\n";}

	// exécution de la requête
	$nbRow = 0;
	try {
		$result = $db->query($sql);

		// récupération de chaque ligne pour update Rang
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			extract($row);
			if ($debug) {echo $CodeSpecialite . " - " . $CHU . "\n";}

			// lecteur du CESP
			$sqlCESP = "SELECT CESP2021, CESP2020 FROM CESP where CESP.CodeSpecialite='" . $CodeSpecialite . "' AND CESP.CHU='" . $CHU . "';";
			if ($debug) {echo "SQL CESP = " . $sqlCESP . "\n";}
			try {
				$resultCESP = $db->query($sqlCESP);			
				while ($rowCESP = $resultCESP->fetch(PDO::FETCH_ASSOC)) {
					extract($rowCESP);

					if ($CESP2021 == '') {$CESP2021 = 0;}
					if ($CESP2020 == '') {$CESP2020 = 0;}

					// mise à jour de Rang
					$update = "UPDATE Rang SET Rang.CESP2021=" . $CESP2021 . ", Rang.CESP2020=" . $CESP2020 . " WHERE Rang.CHU='$CHU' AND Rang.CodeSpecialite='$CodeSpecialite';";					
					$nbRow +=1;
					if ($debug) {echo "SQL UPDATE = " . $update . "\n";}
					try {
						$retour = $db->exec($update);
					}
					catch(PDOException $erreur)	{
						echo "Erreur : " . $erreur->getMessage();
					}

				}
			}
			catch(PDOException $erreur)	{
				echo "Erreur UPDATE Rang : " . $erreur->getMessage();
			}
		}
	}
	catch(PDOException $erreur)	{
		echo "Erreur SELECT CESP2021 : " . $erreur->getMessage();
	}

	// fermeture de la base
	if (isset($result)) {$result->closeCursor();}
	$db = null;

	if ($debug) {echo "records mis à jour = " . $nbRow . "\n";}
	if ($debug) {echo "********** fin de script *********\n";}
?>