<?php

/*****************************************************************************************
	script de chargement du nombre de postes 2024 dans la table Specialite
	depuis la table Rang (après avoir mis cette dernière à jour)
*****************************************************************************************/
	
	$debug = true;
	// récupération du paramètre pour passer en mode debug (par défaut pas de debug)
	if (isset($_GET['debug']))  {
		if ($_GET['debug'] == "true") {
			$debug=true;
		}
	}
	if ($debug) {echo "********** début de script *********\n";}

	// conexion à mySQL serveur et la base ECN et passage au mode exception pour les erreurs
	if ($debug) {echo  "Connexion mySQL serveur \n";}
	try {
		$db = new PDO("mysql:host=localhost;dbname=ecn", "ecn", "ecn");
	}
	catch(PDOException $erreur)	{
		die('Erreur : ' . $erreur->getMessage());
	}
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
	// préparation de la requête pour afficher les spécialités
	$sql = "SELECT CodeSpecialite as Code, SUM(Poste2024) as Poste FROM Rang GROUP BY CodeSpecialite;";

	if ($debug) {echo "SQL = " . $sql . "\n";}

	// exécution de la requête
	$nbRow = 0;
	try {
		$result = $db->query($sql);

		// récupération de chaque ligne pour update Rang
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			extract($row);
			if ($debug) {echo $Code . " - " . $Poste . "\n";}
			$update = "UPDATE Specialite SET Poste2024=$Poste WHERE CodeSpecialite='$Code';";
			if ($debug) echo $update."\n";
			try {
				$retour = $db->exec($update);
			}
			catch(PDOException $erreur)	{
				echo "Erreur : " . $erreur->getMessage();
			}
			$nbRow +=1;
		}
	}
	catch(PDOException $erreur)	{
		echo "Erreur SELECT Rang : " . $erreur->getMessage();
	}

	// fermeture de la base
	if (isset($result)) {$result->closeCursor();}
	$db = null;

	if ($debug) {echo "records mis à jour = " . $nbRow . "\n";}
	if ($debug) {echo "********** fin de script *********\n";}
?>