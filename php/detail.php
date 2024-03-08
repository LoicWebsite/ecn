<?php

	// fonctions communes et récupération-contrôle des paramètres
	include_once "php/controleParametre.php";
	include_once "php/fonctionECN.php";

	// détail de la spécialité
	include "php/resume-specialite.php";

	// construction du tableau des CHU
	echo "<div id='tableau' class='container'>";

	// préparation de la requête pour la table Rang
	$where = " WHERE Type <> '' AND Rang.Poste2023 > 0 ";

	if (($type <> "") and ($type <> "typeIndifferent")) {
		if ($type == "medico-chirurgical") {
			$where = $where . " AND Type = 'mixte'";
		} elseif ($type == "organe") {
			$where = $where . " AND Nature = 'organe'";
		} elseif ($type == "transversal") {
			$where = $where . " AND Nature = 'transversale'";
		} elseif ($type == "chirurgie") {
			$where = $where . " AND Type = 'chirurgie'";
		}
	}

	if ($cesp == "on") {
		$where = $where . " AND Rang.CESP2023 <> '0' AND Rang.CESP2023 <> ''";
	}

	if (($lieu <> "") and ($lieu <> "lieuIndifferent")) {
//		$where = $where . " AND Lieu = '" . utf8_decode($lieu) . "'";
		$where = $where . " AND Lieu = '" . $lieu . "'";
	}

	if (($internat <> "") and ($internat <> "internatIndifferent") and ($internat > 0)) {
		$where = $where . " AND DureeInternat = $internat";
	}

	if (($benefice <> "") and ($benefice <> "beneficeIndifferent")) {
		if ($benefice == "benefice60") {$where = $where . " AND Benefice <= 60000";}
		elseif ($benefice == "benefice100") {$where = $where . " AND Benefice >= 60000 AND Benefice <= 100000";}
		elseif ($benefice == "benefice140") {$where = $where . " AND Benefice >= 100000 AND Benefice <= 140000";}
		elseif ($benefice == "benefice500") {$where = $where . " AND Benefice >= 140000";}
	}

	if (($rang <> "") and ($rang > 0) and ($rang <> "rangIndifferent")) {
		$whereSpecialite = $where . " AND Rang.Dernier" . $reference . " >= '" . $rang ."'";
	} else {
		$whereSpecialite = $where;
	}

// A ACTIVER PENDANT LA PHASE DE CHOIX DE POSTE
	// requête pour afficher la date de mise à jour des données 2023
	$date = 0;
	$heure = 0;
// 	if ($reference == "2023") {
// 		$sql = "
// 			SELECT
// 					DateMiseAJour,
// 					HeureMiseAJour
// 				FROM MiseAJour
// 				WHERE Id = 0;";
// 		if ($debug) echo "SQL = " . $sql ."<br/>";
// 		try {
// 			$result = $db->query($sql);
// 			while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
// 				extract($row);
// 				$date = $DateMiseAJour;
// 				$heure = $HeureMiseAJour;
// 			}
// 		}
// 		catch(PDOException $erreur)	{
// 			echo "Erreur SELECT MiseAjour : " . $erreur->getMessage();
// 		}
// 	}


	// requête pour compter le nombres de postes et de CESP
	$nbPoste = 0;
	$nbCESP = 0;
	$sql = "SELECT Rang.CodeSpecialite, SUM(Rang.Poste2023) AS totalPoste, SUM(Rang.CESP2023) AS totalCESP  FROM Specialite
			inner join Rang on Specialite.CodeSpecialite = Rang.CodeSpecialite " . $whereSpecialite . " AND Rang.CodeSpecialite='" . $CodeSpecialite . "';";
	if ($debug) echo "SQL POSTE = " . $sql ."<br/>";
	try {
		$result = $db->query($sql);
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			extract($row);
			$nbPoste += $totalPoste;
			$nbCESP += $totalCESP;
		}
	}
	catch(PDOException $erreur)	{
		echo "Erreur SELECT Nb Postes : " . $erreur->getMessage();
	}

	// requête pour aller chercher les rangs et le nombre de poste et de CESP
	$sql = "SELECT	Rang.CodeSpecialite as CodeSpecialite,
					Rang.CHU,
					Rang.Dernier2023 as Dernier2023,
					Rang.Dernier2022 as Dernier2022,
					Rang.Dernier2021 as Dernier2021,
					Rang.Dernier2020 as Dernier2020,
					Rang.Dernier2019 as Dernier2019,
					Rang.Dernier2018 as Dernier2018,
					Rang.Dernier2017 as Dernier2017,
					Rang.URLCeline,
					Rang.Poste2023 as Poste2023,
					Rang.CESP2023 as CESP2023
			FROM `Specialite` inner join Rang on Specialite.CodeSpecialite = Rang.CodeSpecialite " . $whereSpecialite . " AND Rang.CodeSpecialite='" . $CodeSpecialite . "';";
	if ($debug) echo "SQL = " . $sql ."<br/>";

	// exécution de la requête
	try {
		$result = $db->query($sql);
		$montant = new NumberFormatter("fr-FR", NumberFormatter::DECIMAL);
		
		// titre de la page
		echo "<h2 class='h5' style='text-align:left;'>". $result->rowCount() . " CHU possibles en " . $libelleSpecialite;
		if (($rang > "") and ($rang <> 0) and ($rang <> "rangIndifferent")) {
			echo " pour un rang de " . $montant->format($rang) . " en " . $reference;
		}
		if ($cesp == "on") {
			echo " en CESP";
		}
		echo "</h2><br/>";

		// en tête du tableau
		echo "<table class='table-hover' style='margin:auto;'>";
		echo "<thead class='text-center'>";
// A ACTIVER PENDANT LA PHASE DE CHOIX DE POSTE
		echo "<tr><th style='width:50%'>" . $result->rowCount() . " CHU<br><i class='fas fa-info-circle' data-toggle='tooltip'  data-html='true' title='Cliquer sur un CHU pour afficher le détail des rangs dans Celine (uniquement pour 2023).'></i></th>";
//		echo "<tr><th style='width:50%'>" . $result->rowCount() . " CHU</th>";
		echo "<th style='width:20%'> ".$montant->format($nbPoste)." postes 2023 <br/><i class='fas fa-info-circle' data-toggle='tooltip' data-html='true' title='Le nombre de postes 2023 est issu de l&apos;arrêté publié par le Journal Officiel du 4 août 2023.<br/>Le nombre de postes exclut les CESP.<br/>Les CHU avec zéro poste dans cette spécialité ne sont pas affichés.'></i></th>";
		$libelleReference = "2023";
// A ACTIVER PENDANT LA PHASE DE CHOIX DE POSTE
//  		if ($reference == "2023") {
//  			$libelleReference = $reference . "<br/><small>" . $date . " à " . $heure . "</small>";
//  			echo "<th style='width:20%'> Rang dernier " . $libelleReference . " <br/><i class='fas fa-info-circle' data-toggle='tooltip'  data-html='true' title='Le rang du dernier admis en " . $reference . " est issu de la simulation CELINE et est actualisé une fois par heure. <br/>Un rang à zéro signifie qu&apos;il n&apos;y avait pas de poste cette année là dans ce CHU pour cette spécialité, ou pas encore de voeux exprimé.'></i></th>";
//  		} else {
			$libelleReference = $reference;
			echo "<th style='width:20%'> Rang dernier " . $libelleReference . " <br/><i class='fas fa-info-circle' data-toggle='tooltip'  data-html='true' title='Le rang du dernier admis en " . $reference . " est issu du fichier &apos;Rangs limites " . $reference . "&apos; du site CNG Santé.<br/>Un rang à zéro signifie qu&apos;il n&apos;y avait pas de poste cette année là dans ce CHU pour cette spécialité.'></i></th>";
//		}
		echo "<th style='width:10%;'> ".$montant->format($nbCESP)." CESP 2023 <br/><i class='fas fa-info-circle' data-toggle='tooltip' data-html='true' title='Le nombre de postes 2023 réservés aux CESP est issu de l&apos;arrêté publié par le Journal Officiel du 4 août 2023.<br/>Une cellule vide signifie qu&apos;il n&apos;y a pas de poste CESP pour cette spécialité dans ce CHU.'></i></th>";
		echo "</tr></thead>\n";
		echo "<tbody>";

		// récupération des rangs à afficher
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			extract($row);
// A ACTIVER PENDANT LA PHASE DE CHOIX DE POSTE
 			if ($reference == "2023") {
 				$href = " ondblclick='window.open(&apos;" . $URLCeline . "&apos;, &apos;rangs Celine&apos;)' ";
 			} else {
				$href = "";
			}
			echo "<tr>";
			echo "<td style='padding-left:5%;' " . $href . ">" . $CHU . "</td><td class='text-center' " . $href . ">" . $montant->format($Poste2023) . "</td>";
			$dernier = "0";
			if ($reference == 2023) {
				$dernier = $montant->format($Dernier2023);
			} elseif  ($reference == 2022) {
				$dernier = $montant->format($Dernier2022);
			} elseif  ($reference == 2021) {
				$dernier = $montant->format($Dernier2021);
			} elseif  ($reference == 2020) {
				$dernier = $montant->format($Dernier2020);
			} elseif ($reference == 2019) {
				$dernier = $montant->format($Dernier2019);
			} elseif ($reference == 2018) {
				$dernier = $montant->format($Dernier2018);
			} elseif ($reference == 2017) {
				$dernier = $montant->format($Dernier2017);
			}
			echo "<td class='text-center' " . $href . ">" . $dernier .  "</td>";
			if ($CESP2023 == "0") {$CESP2023 = "";}
			echo "<td class='derniereColonne text-center' " . $href . ">" . $CESP2023 . "</td>";
			echo "<td class='milieu'></td>";
			echo "</tr>\n";
		}
		echo "<tr><td colspan=4 style='border-top-style:solid; border-left-style:hidden; border-right-style:hidden; border-bottom-style:hidden;'></td></tr>";
		echo "</tbody>";
		echo "</table><br/>";
// A ACTIVER quand Celine actif
 		if ($reference == "2023") {
 			echo "<p>Cliquer &nbsp;<i class='far fa-hand-pointer'></i>&nbsp; sur un CHU pour afficher le détail des rangs dans Celine (uniquement pour 2023).</p>";
 		}
	}
	catch(PDOException $erreur)	{
		echo "Erreur : " . $erreur->getMessage();
	}

	// fermeture de la base
	if (isset($result)) {$result->closeCursor();}
	$db = null;

	echo "</div>";
	
?>