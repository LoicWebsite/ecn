<?php
	/*
	 * Rang stocke une ligne par spécialité, CHU et année.
	 * Les années sont résolues séparément :
	 * - Dernier : année choisie, ou dernier rang connu ;
	 * - Poste/CESP : année choisie, ou dernière année disponible.
	 * Les requêtes joignent les deux sources sur CodeSpecialite et CHU
	 * afin de ne pas multiplier les lignes dans les totaux.
	 */

	// fonctions communes et récupération-contrôle des paramètres
	require_once "php/controleParametre.php";
	require_once "php/fonctionECN.php";
	
	// ouverture de la base de données
	$db = openDatabase();

	// Résolution des années effectives avant de construire les filtres SQL.
	$referenceAnnee = intval($reference);
	$rangYearColumns = getRangYearColumns($db);
	$rangSources = resolveAnnualRangSources($rangYearColumns, $referenceAnnee);
	$rangMode = getRangDisplayMode($db, $reference, null, $rangYearColumns);
	$rangCespDisponible = $rangMode['available'];
	$afficherDernierCesp = ($rangMode['mode'] === 'cesp');
	$rangFilterExpression = getRangFilterExpression($cesp);

	// résumé de la spécialité
	include "php/resume-specialite-dynamique.php";
	$CodeSpecialite = isset($CodeSpecialite) ? $CodeSpecialite : $code;
	$anneeReference = $rangMode['year'];
	$anneePoste = getRangDisplayPosteYear($db, $reference);

	// construction du tableau des CHU
	echo "<div id='tableau' class='container'>";

	// préparation de la requête pour la table Rang

	$where = " WHERE Specialite.Type <> ''
		AND COALESCE(rangPoste.Poste, 0) > 0";

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
		if ($rangSources['cesp']['year'] !== null) {
			$where = $where . " AND COALESCE(rangPoste.CESP, 0) <> 0";
		} else {
			$where = $where . " AND 1 = 0";
		}
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
		if ($rangSources['dernier']['year'] !== null) {
			$whereSpecialite = $where . " AND COALESCE(" . $rangFilterExpression . ", 0) >= " . intval($rang);
		} else {
			$whereSpecialite = $where;
		}
	} else {
		$whereSpecialite = $where;
	}

	// requête pour compter le nombres de postes et de CESP
	$nbPoste = 0;
	$nbCESP = 0;

	$sql = "SELECT Specialite.CodeSpecialite,
				SUM(COALESCE(rangPoste.Poste, 0)) AS totalPoste,
				SUM(COALESCE(rangPoste.CESP, 0)) AS totalCESP
			FROM Specialite
			INNER JOIN Rang rangPoste
				ON Specialite.CodeSpecialite = rangPoste.CodeSpecialite
				AND rangPoste.Annee = :anneePoste
			LEFT JOIN Rang rangDernier
				ON Specialite.CodeSpecialite = rangDernier.CodeSpecialite
				AND rangDernier.CHU = rangPoste.CHU
				AND rangDernier.Annee = :anneeReference
			" . $whereSpecialite . " AND Specialite.CodeSpecialite=:codeSpecialite;";
	if ($debug) echo "SQL POSTE = " . $sql ."<br/>";
	try {
		$stmt = $db->prepare($sql);
		$stmt->execute([
			':anneePoste' => $anneePoste,
			':anneeReference' => $anneeReference,
			':codeSpecialite' => $CodeSpecialite
		]);
		$result = $stmt;
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
	$sql = "SELECT rangPoste.CodeSpecialite AS CodeSpecialite,
					rangPoste.CHU,
					COALESCE(rangDernier.Dernier, 0) AS DernierRef,
					COALESCE(rangDernier.DernierCESP, 0) AS DernierCESPRef,
					COALESCE(rangPoste.Poste, 0) AS PosteRef,
					COALESCE(rangPoste.CESP, 0) AS CESPRef
			FROM Rang rangPoste
			INNER JOIN Specialite
				ON Specialite.CodeSpecialite = rangPoste.CodeSpecialite
			LEFT JOIN Rang rangDernier
				ON rangDernier.CodeSpecialite = rangPoste.CodeSpecialite
				AND rangDernier.CHU = rangPoste.CHU
				AND rangDernier.Annee = :anneeReference
			WHERE rangPoste.Annee = :anneePoste
				AND rangPoste.CodeSpecialite=:codeSpecialite
				" . preg_replace('/^ WHERE /', ' AND ', $whereSpecialite) . ";";
	if ($debug) echo "SQL = " . $sql ."<br/>";

	// exécution de la requête
	try {
		$stmt = $db->prepare($sql);
		$stmt->execute([
			':anneePoste' => $anneePoste,
			':anneeReference' => $anneeReference,
			':codeSpecialite' => $CodeSpecialite
		]);
		$result = $stmt;
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
		if ($rangCespDisponible) {
			$paramsModeRang = ['code' => $code, 'specialite' => $specialite, 'rang' => $rang, 'reference' => $reference, 'type' => $type, 'cesp' => $cesp, 'lieu' => $lieu, 'internat' => $internat, 'benefice' => $benefice, 'depuis' => $depuis];
			echo renderRangModeSelector($paramsModeRang, $rangMode['mode']);
		}

		// en tête du tableau
		echo "<table class='table-hover' style='margin:auto;'>";
		echo "<thead class='text-center'>";
		echo "<tr><th style='width:50%'>" . $result->rowCount() . " CHU</th>";
		$libellesTooltip = getLibellesTooltipPosteCesp($reference, $rangSources);
		$libelleDernier = $libellesTooltip['dernier'];
		$libellePoste = $libellesTooltip['poste'];
		$libelleCesp = $libellesTooltip['cesp'];
		echo "<th style='width:20%'> ".$montant->format($nbPoste)." postes " . escapeHtml($libellePoste) . " <br/><i class='bi bi-info-circle-fill' data-toggle='tooltip' data-html='true' title='Le nombre de postes est issu de l&apos;arrêté publié par le Journal Officiel.<br/>L&apos;année correspond à l&apos;année de publication au Journal Officiel.<br/>Le nombre de postes exclut les CESP.<br/>Les CHU avec zéro poste dans cette spécialité ne sont pas affichés.'></i></th>";
		$infoTooltipRang = "À partir de 2024, il s&apos;agit du rang limite par groupe de spécialités.<br>Auparavant c&apos;était le rang limite national par spécialité.";
		if ($rangCespDisponible) {
			$infoTooltipRang .= "<br/>" . getRangModeTooltipDescription();
		}
		$infoTooltipRang .= "<br/>Un rang à zéro signifie qu&apos;il n&apos;y avait pas de poste cette année-là dans ce CHU pour cette spécialité.";
		$libelleEnteteRang = $afficherDernierCesp ? "Rang dernier CESP " : "Rang dernier ";
		echo "<th style='width:20%'>" . $libelleEnteteRang . escapeHtml($libelleDernier) . " <br/><i class='bi bi-info-circle-fill' data-toggle='tooltip' data-html='true' title='" . $infoTooltipRang . "'></i></th>";
		echo "<th style='width:10%;'> ".$montant->format($nbCESP)." CESP " . escapeHtml($libelleCesp) . " <br/><i class='bi bi-info-circle-fill' data-toggle='tooltip' data-html='true' title='Le nombre de postes réservés aux CESP est issu de l&apos;arrêté publié par le Journal Officiel.<br/>Une cellule vide signifie qu&apos;il n&apos;y a pas de poste CESP pour cette spécialité dans ce CHU.'></i></th>";
		echo "</tr></thead>\n";
		echo "<tbody>";

		// récupération des rangs à afficher
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			extract($row);
			$href = "";
			echo "<tr>";
			$dernierPrincipalValeur = intval($DernierRef);
			$dernierCespValeur = intval($DernierCESPRef);
			$dernierValeur = $afficherDernierCesp ? $dernierCespValeur : $dernierPrincipalValeur;
			$posteValeur = intval($PosteRef);
			$cespValeur = intval($CESPRef);
			$dernier = ($dernierValeur > 0) ? $montant->format($dernierValeur) : "-";
			$tooltipRangs = " data-toggle='tooltip' data-html='true' title='Dernier " . escapeHtml($anneeReference) . " : " . escapeHtml($dernierPrincipalValeur > 0 ? $montant->format($dernierPrincipalValeur) : "0") . "<br/>Dernier CESP " . escapeHtml($anneeReference) . " : " . escapeHtml($dernierCespValeur > 0 ? $montant->format($dernierCespValeur) : "0") . "'";
			echo "<td style='padding-left:5%;' " . $href . ">" . $CHU . "</td><td class='text-center' " . $href . ">" . $montant->format($posteValeur) . "</td>";
			echo "<td class='text-center' " . $tooltipRangs . $href . ">" . $dernier .  "</td>";
			$libelleCespCellule = ($cespValeur == 0) ? "" : $montant->format($cespValeur);
			echo "<td class='derniereColonne text-center' " . $href . ">" . $libelleCespCellule . "</td>";
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