<?php

	// fonctions communes et récupération-contrôle des paramètres
	require_once "php/controleParametre.php";
	require_once "php/fonctionECN.php";
	
	// ouverture de la base de données
	$db = openDatabase();

	// résumé de la spécialité
	include "php/resume-specialite.php";

	// construction du tableau des CHU
	echo "<div id='tableau' class='container'>";

	// préparation de la requête pour la table Rang

	$libellePoste = "Poste2025";
	$libelleCesp = "CESP2025";			// avant 2020 le nombre de postes et de CESP n'est pas en base (on prend 2025 par défaut)
	if ($reference == 2024) {
		$libellePoste = "Poste2024";
		$libelleCesp = "CESP2024";
	} elseif ($reference == 2023) {
		$libellePoste = "Poste2023";
		$libelleCesp = "CESP2023";
	} elseif ($reference == 2022) {
		$libellePoste = "Poste2022";
		$libelleCesp = "CESP2022";
	} elseif ($reference == 2021) {
		$libellePoste = "Poste2021";
		$libelleCesp = "CESP2021";
	} elseif ($reference == 2020) {
		$libellePoste = "Poste2020";
		$libelleCesp = "CESP2020";
	}

	$where = " WHERE Type <> '' AND Rang." . $libellePoste . " > 0 ";

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
		$where = $where . " AND Rang." . $libelleCesp . " <> '0' AND Rang." . $libelleCesp . " <> ''";
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

	// requête pour compter le nombres de postes et de CESP
	$nbPoste = 0;
	$nbCESP = 0;

	$sql = "SELECT Rang.CodeSpecialite, SUM(Rang." . $libellePoste . ") AS totalPoste, SUM(Rang." . $libelleCesp . ") AS totalCESP  FROM Specialite
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
					Rang.Dernier2025 as Dernier2025,
					Rang.Dernier2024 as Dernier2024,
					Rang.Dernier2023 as Dernier2023,
					Rang.Dernier2022 as Dernier2022,
					Rang.Dernier2021 as Dernier2021,
					Rang.Dernier2020 as Dernier2020,
					Rang.Dernier2019 as Dernier2019,
					Rang.Dernier2018 as Dernier2018,
					Rang.Dernier2017 as Dernier2017,
					Rang.URLCeline,
					Rang.Poste2025 as Poste2025,
					Rang.CESP2025 as CESP2025,
					Rang.Poste2024 as Poste2024,
					Rang.CESP2024 as CESP2024,
					Rang.Poste2023 as Poste2023,
					Rang.CESP2023 as CESP2023,
					Rang.Poste2022 as Poste2022,
					Rang.CESP2022 as CESP2022,
					Rang.Poste2021 as Poste2021,
					Rang.CESP2021 as CESP2021,
					Rang.Poste2020 as Poste2020,
					Rang.CESP2020 as CESP2020
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
		echo "<tr><th style='width:50%'>" . $result->rowCount() . " CHU</th>";
		if ($reference < 2020) {
			$libelle = "2025";
		} else {	
			$libelle = $reference;
		}
		echo "<th style='width:20%'> ".$montant->format($nbPoste)." postes " . $libelle . " <br/><i class='bi bi-info-circle-fill' data-toggle='tooltip' data-html='true' title='Le nombre de postes est issu de l&apos;arrêté publié par le Journal Officiel.<br/>L&apos;année correspond à l&apos;année de publication au Journal Officiel.<br/>Le nombre de postes exclut les CESP.<br/>Les CHU avec zéro poste dans cette spécialité ne sont pas affichés.'></i></th>";
		echo "<th style='width:20%'> Rang dernier " . $reference . " <br/><i class='bi bi-info-circle-fill' data-toggle='tooltip'  data-html='true' title='A partir de 2024, il s&apos;agit du rang limite par groupe de spécialités.<br>Auparavant c&apos;était le rang limite national par spécialité.<br/>Un rang à zéro signifie qu&apos;il n&apos;y avait pas de poste cette année là dans ce CHU pour cette spécialité.'></i></th>";
		echo "<th style='width:10%;'> ".$montant->format($nbCESP)." CESP " . $libelle . " <br/><i class='bi bi-info-circle-fill' data-toggle='tooltip' data-html='true' title='Le nombre de postes réservés aux CESP est issu de l&apos;arrêté publié par le Journal Officiel.<br/>Une cellule vide signifie qu&apos;il n&apos;y a pas de poste CESP pour cette spécialité dans ce CHU.'></i></th>";
		echo "</tr></thead>\n";
		echo "<tbody>";

		// récupération des rangs à afficher
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			extract($row);
			$href = "";
// A ACTIVER PENDANT LA PHASE DE CHOIX DE POSTE
//  			if ($reference == "2023") {
//  				$href = " ondblclick='window.open(&apos;" . $URLCeline . "&apos;, &apos;rangs Celine&apos;)' ";
//  			}
			echo "<tr>";
			$dernier = "0";
			$poste = "0";
			$libelleCesp = "0";
			if ($reference == 2025) {
				$dernier = $montant->format($Dernier2025);
				$poste = $Poste2025;
				$libelleCesp = $CESP2025;
			} elseif  ($reference == 2024) {
				$dernier = $montant->format($Dernier2024);
				$poste = $Poste2024;
				$libelleCesp = $CESP2024;
			} elseif  ($reference == 2023) {
				$dernier = $montant->format($Dernier2023);
				$poste = $Poste2023;
				$libelleCesp = $CESP2023;
			} elseif  ($reference == 2022) {
				$dernier = $montant->format($Dernier2022);
				$poste = $Poste2022;
				$libelleCesp = $CESP2022;
			} elseif  ($reference == 2021) {
				$dernier = $montant->format($Dernier2021);
				$poste = $Poste2021;
				$libelleCesp = $CESP2021;
			} elseif  ($reference == 2020) {
				$dernier = $montant->format($Dernier2020);
				$poste = $Poste2020;
				$libelleCesp = $CESP2020;
			} elseif ($reference == 2019) {
				$dernier = $montant->format($Dernier2019);
				$poste = $Poste2025;
				$libelleCesp = $CESP2025;
			} elseif ($reference == 2018) {
				$dernier = $montant->format($Dernier2018);
				$poste = $Poste2025;
				$libelleCesp = $CESP2025;
			} elseif ($reference == 2017) {
				$dernier = $montant->format($Dernier2017);
				$poste = $Poste2025;
				$libelleCesp = $CESP2025;
			}
			echo "<td style='padding-left:5%;' " . $href . ">" . $CHU . "</td><td class='text-center' " . $href . ">" . $montant->format($poste) . "</td>";
			echo "<td class='text-center' " . $href . ">" . $dernier .  "</td>";
			if ($libelleCesp == "0") {$libelleCesp = "";}
			echo "<td class='derniereColonne text-center' " . $href . ">" . $libelleCesp . "</td>";
			echo "<td class='milieu'></td>";
			echo "</tr>\n";
		}
		echo "<tr><td colspan=4 style='border-top-style:solid; border-left-style:hidden; border-right-style:hidden; border-bottom-style:hidden;'></td></tr>";
		echo "</tbody>";
		echo "</table><br/>";
	}
	catch(PDOException $erreur)	{
		echo "Erreur : " . $erreur->getMessage();
	}

	// fermeture de la base
	if (isset($result)) {$result->closeCursor();}
	$db = null;

	echo "</div>";
	
?>