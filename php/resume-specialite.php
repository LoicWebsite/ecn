<?php
	echo "<div id='resume' class='container'>";
	
		// initialisation des variables
		$where = "";
		$libelleSpecialite = "";
		$nbCESP = "0";

		// constrcution clausse where
		// c'est le code spécialité qui est passé en paramètre par la page principale
		if (strlen($specialite) == 3) {
			$where = " WHERE CodeSpecialite = '" . $specialite . "';";
		} elseif (strlen($specialite) > 3) {
		// c'est le libellé de la spécialité qui est passé en paramètre par la page principale
			if (mb_detect_encoding($specialite,'UTF-8', true)) {			// en local les caractères accentués passent, mais pas sur le serveur Gandi
				$libelleSpecialite =  $specialite;
			} else {
				$libelleSpecialite = utf8_encode($specialite);
			}
			$where = " WHERE Specialite = '" . $libelleSpecialite . "';";
		// c'est le code spécialité qui est passé en paramètre par la questionnaire
		} else {
			$where = " WHERE CodeSpecialite = '" . $code . "';";
		}

		// conexion à la base ecn 
		try {
			$db = new PDO("mysql:host=localhost;dbname=ecn;charset=utf8", "USER", "PASSE" );
		}
		catch(PDOException $erreur)	{
			die('Erreur : ' . $erreur->getMessage());
		}

		// passage au mode exception pour les erreurs
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// A ACTIVER PENDANT LA PHASE DE CHOIX DE POSTE
		// requête pour afficher la date de mise à jour des données 2022
		$date = 0;
		$heure = 0;
		if ($reference == "2022") {
			$sql = "
				SELECT
					DateMiseAJour,
					HeureMiseAJour
				FROM MiseAJour
				WHERE Id = 0;";
			if ($debug) echo "SQL = " . $sql ."<br/>";
			try {
				$result = $db->query($sql);
				while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
					extract($row);
					$date = $DateMiseAJour;
					$heure = $HeureMiseAJour;
				}
			}
			catch(PDOException $erreur)	{
				echo "Erreur SELECT MiseAjour : " . $erreur->getMessage();
			}
		}

		// préparation de la requête pour la table Specialite
		$sql = "
			SELECT
					CodeSpecialite,
					Specialite,
					Poste2021,
					Poste2020,
					CESP2021,
					CESP2020,
					Dernier2023,
					CHUDernier2023,
					Dernier2022,
					CHUDernier2022,
					Dernier2021,
					CHUDernier2021,
					Dernier2020,
					CHUDernier2020,
					Dernier2019,
					CHUDernier2019,
					Dernier2018,
					CHUDernier2018,
					Dernier2017,
					CHUDernier2017,
					Benefice,
					Type,
					Nature,
					Lieu,
					DureeInternat
				FROM Specialite"
				. $where;
		if ($debug) echo "SQL = " . $sql ."<br/>";

		// exécution de la requête
		try {
			$result = $db->query($sql);
			$montant = new NumberFormatter("fr-FR", NumberFormatter::DECIMAL);

			// récupération des données à transmettre
			while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
				extract($row);
				if (mb_detect_encoding($Specialite,'UTF-8', true)) {			// en local les caractères accentués passent, mais pas sur le serveur Gandi
					$libelleSpecialite =  $Specialite;
				} else {
					$libelleSpecialite = utf8_encode($Specialite);
				}

				$nbCESP = $CESP2021; // mémorisation nu nombre de CESP pour la spécialité
				
				echo "<h1 class='h5' style='text-align:left; margin-top:20px;'>";
    			echo "<a class='h5' data-toggle='collapse' aria-expanded='false' aria-controls='detail' href='#detail'><i id='symbole' class='fa fa-plus-circle' aria-hidden='true'></i>";
				if ((!isset($code)) or ($code == null) or ($code == "") or ($code == "inconnu")) {
					echo "&nbsp; Détail de la spécialité " . $libelleSpecialite ."...";
				} else {
					echo "&nbsp; Détail de la spécialité $code - " . $libelleSpecialite ."...";
				}
				echo "</a></h1><br/>\n";

				echo "<div id='detail' class='collapse'>";
				echo "<div class='row'>";
				echo "<div class='col-sm'>";
				echo "<p class='text-left' style='background:gainsboro'>&nbsp; Spécialité</p>";
				echo "</div>";
				echo "<div class='col-sm'>";

				echo "<p class='text-left'>" . $CodeSpecialite . " - " . $libelleSpecialite . "</p>";
				echo "</div>";

				echo "<div class='col-sm'>";
				echo "<p class='text-left' style='background:gainsboro'>&nbsp; Type de spécialité</p>";
				echo "</div>";
				echo "<div class='col-sm'>";
				echo "<p class='text-left'>" . getLibelleTypeNature($Type, $Nature) . "</p>";
				echo "</div>";
				echo "</div>";


				echo "<div class='row'>";
				echo "<div class='col-sm'>";
				echo "<p class='text-left' style='background:gainsboro'>&nbsp; Durée de l'internat</p>";
				echo "</div>";
				echo "<div class='col-sm'>";
				echo "<p class='text-left'>" . $DureeInternat . " ans</p>";			
				echo "</div>";

				echo "<div class='col-sm'>";
				echo "<p class='text-left' style='background:gainsboro'>&nbsp; Lieu d'exercice</p>";
				echo "</div>";
				echo "<div class='col-sm'>";
				echo "<p class='text-left'>" . getLibelleLieu($Lieu) . "</p>";			
				echo "</div>";
				echo "</div>";

				echo "<div class='row'>";
				echo "<div class='col-sm'>";
				echo "<p class='text-left' style='background:gainsboro'>&nbsp; Bénéfice net moyen</p>";
				echo "</div>";
				echo "<div class='col-sm'>";
				echo "<p class='text-left'>" . $montant->format($Benefice) . " €</p>";		
				echo "</div>";
				echo "<div class='col-sm'>";
				echo "<p>&nbsp;</p>";
				echo "</div>";
				echo "<div class='col-sm'>";	
				echo "<p>&nbsp;</p>";
				echo "</div>";
				echo "</div>";
				echo "<br>";
				
				echo "<div class='row'>";
				echo "<div class='col-sm'>";
				echo "<p class='text-left' style='background:gainsboro'>&nbsp; Dernier admis en 2023</p>";
				echo "</div>";
				echo "<div class='col-sm'>";
				echo "<p class='text-left'>" . $montant->format($Dernier2023) . " à " . $CHUDernier2023 . "</p>";		
				echo "</div>";
				echo "<div class='col-sm'>";
				echo "<p class='text-left' style='background:gainsboro'>&nbsp; Dernier admis en 2022</p>";
				echo "</div>";
				echo "<div class='col-sm'>";	
				echo "<p class='text-left'>" . $montant->format($Dernier2022) . " à " . $CHUDernier2022 . "</p>";
				echo "</div>";
				echo "</div>";

				echo "<div class='row'>";
				echo "<div class='col-sm'>";
				echo "<p class='text-left' style='background:gainsboro'>&nbsp; Dernier admis en 2021</p>";
				echo "</div>";
				echo "<div class='col-sm'>";
				echo "<p class='text-left'>" . $montant->format($Dernier2021) . " à " . $CHUDernier2021 . "</p>";
				echo "</div>";

				echo "<div class='col-sm'>";
				echo "<p class='text-left' style='background:gainsboro'>&nbsp; Dernier admis en 2020</p>";
				echo "</div>";
				echo "<div class='col-sm'>";
				echo "<p class='text-left'>" . $montant->format($Dernier2020) . " à " . $CHUDernier2020 . "</p>";
				echo "</div>";
				echo "</div>";


				echo "<div class='row'>";
				echo "<div class='col-sm'>";
				echo "<p class='text-left' style='background:gainsboro'>&nbsp; Dernier admis en 2019</p>";
				echo "</div>";
				echo "<div class='col-sm'>";
				echo "<p class='text-left'>" . $montant->format($Dernier2019) . " à " . $CHUDernier2019 . "</p>";
				echo "</div>";				
				
				echo "<div class='col-sm'>";
				echo "<p class='text-left' style='background:gainsboro'>&nbsp; Dernier admis en 2018</p>";
				echo "</div>";
				echo "<div class='col-sm'>";
				echo "<p class='text-left'>" . $montant->format($Dernier2018) . " à " . $CHUDernier2018 . "</p>";
				echo "</div>";
				echo "</div>";


				echo "<div class='row'>";
				echo "<div class='col-sm'>";
				echo "<p class='text-left' style='background:gainsboro'>&nbsp; Dernier admis en 2017</p>";
				echo "</div>";
				echo "<div class='col-sm'>";
				echo "<p class='text-left'>" . $montant->format($Dernier2017) . " à " . $CHUDernier2017 . "</p>";
				echo "</div>";				
				echo "<div class='col-sm'>";
				echo "<p class='text-left' style='background:gainsboro'></p>";
				echo "</div>";
				echo "<div class='col-sm'>";
				echo "<p class='text-left'>"."</p>";
				echo "</div>";
				echo "</div>";
				
				echo "<br/><br/>";
				echo "</div>\n";
			}
		}
		catch(PDOException $erreur)	{
			echo "Erreur : " . $erreur->getMessage();
		}

	echo "</div>";
?>