<?php
	/*
	 * Gestion annuelle des données Rang/Poste/CESP (3 phases)
	 *
	 * Phase 1 - début d'année:
	 *   Les colonnes de l'année peuvent être absentes (ou incomplètes).
	 *
	 * Phase 2 - milieu d'année:
	 *   PosteAAAA et CESPAAAA existent, mais DernierAAAA n'existe pas encore.
	 *
	 * Phase 3 - fin d'année:
	 *   Toutes les colonnes de l'année existent.
	 *
	 * Cas particulier avant 2020:
	 *   Les colonnes Poste/CESP n'existent pas historiquement pour ces années.
	 *   Le script prend alors les colonnes les plus récentes disponibles.
	 *
	 * Implémentation:
	 *   - Détection des colonnes présentes via INFORMATION_SCHEMA.
	 *   - Résolution des sources avec fallback
	 *     (année exacte > année précédente > dernière disponible).
	 *   - Application cohérente aux filtres, totaux et colonnes affichées.
	 */

	// fonctions communes et récupération-contrôle des paramètres
	require_once "php/controleParametre.php";
	require_once "php/fonctionECN.php";
	
	// ouverture de la base de données
	$db = openDatabase();

	// Détection de la phase annuelle et résolution des colonnes disponibles.
	$referenceAnnee = intval($reference);
	$rangYearColumns = getRangYearColumns($db);
	$phaseAnnuelle = getAnnualDataPhase($rangYearColumns, $referenceAnnee);
	$rangSources = resolveAnnualRangSources($rangYearColumns, $referenceAnnee);
	$colonneDernier = $rangSources['dernier']['column'];
	$colonnePoste = $rangSources['poste']['column'];
	$colonneCesp = $rangSources['cesp']['column'];

	// résumé de la spécialité
	include "php/resume-specialite.php";
	$CodeSpecialite = isset($CodeSpecialite) ? $CodeSpecialite : $code;

	// construction du tableau des CHU
	echo "<div id='tableau' class='container'>";

	// préparation de la requête pour la table Rang

	$where = " WHERE Type <> ''";
	if ($colonnePoste !== null) {
		$where = $where . " AND COALESCE(Rang." . $colonnePoste . ", 0) > 0";
	} else {
		$where = $where . " AND 1 = 0";
	}

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
		if ($colonneCesp !== null) {
			$where = $where . " AND COALESCE(Rang." . $colonneCesp . ", 0) <> 0";
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
		if ($colonneDernier !== null) {
			$whereSpecialite = $where . " AND COALESCE(Rang." . $colonneDernier . ", 0) >= " . intval($rang);
		} else {
			$whereSpecialite = $where;
		}
	} else {
		$whereSpecialite = $where;
	}

	// requête pour compter le nombres de postes et de CESP
	$nbPoste = 0;
	$nbCESP = 0;

	$posteExpr = ($colonnePoste !== null) ? "SUM(COALESCE(Rang." . $colonnePoste . ", 0))" : "0";
	$cespExpr = ($colonneCesp !== null) ? "SUM(COALESCE(Rang." . $colonneCesp . ", 0))" : "0";

	$sql = "SELECT Rang.CodeSpecialite, " . $posteExpr . " AS totalPoste, " . $cespExpr . " AS totalCESP  FROM Specialite
			inner join Rang on Specialite.CodeSpecialite = Rang.CodeSpecialite " . $whereSpecialite . " AND Rang.CodeSpecialite=:codeSpecialite;";
	if ($debug) echo "SQL POSTE = " . $sql ."<br/>";
	try {
		$stmt = $db->prepare($sql);
		$stmt->execute([':codeSpecialite' => $CodeSpecialite]);
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
	$dernierExpr = ($colonneDernier !== null) ? "COALESCE(Rang." . $colonneDernier . ", 0)" : "0";
	$posteCellExpr = ($colonnePoste !== null) ? "COALESCE(Rang." . $colonnePoste . ", 0)" : "0";
	$cespCellExpr = ($colonneCesp !== null) ? "COALESCE(Rang." . $colonneCesp . ", 0)" : "0";

	$sql = "SELECT	Rang.CodeSpecialite as CodeSpecialite,
					Rang.CHU,
					" . $dernierExpr . " as DernierRef,
					Rang.URLCeline,
					" . $posteCellExpr . " as PosteRef,
					" . $cespCellExpr . " as CESPRef
			FROM `Specialite` inner join Rang on Specialite.CodeSpecialite = Rang.CodeSpecialite " . $whereSpecialite . " AND Rang.CodeSpecialite=:codeSpecialite;";
	if ($debug) echo "SQL = " . $sql ."<br/>";

	// exécution de la requête
	try {
		$stmt = $db->prepare($sql);
		$stmt->execute([':codeSpecialite' => $CodeSpecialite]);
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

		// en tête du tableau
		echo "<table class='table-hover' style='margin:auto;'>";
		echo "<thead class='text-center'>";
		echo "<tr><th style='width:50%'>" . $result->rowCount() . " CHU</th>";
		$libellesTooltip = getLibellesTooltipPosteCesp($reference, $rangSources);
		$libelleDernier = $libellesTooltip['dernier'];
		$libellePoste = $libellesTooltip['poste'];
		$libelleCesp = $libellesTooltip['cesp'];
		echo "<th style='width:20%'> ".$montant->format($nbPoste)." postes " . escapeHtml($libellePoste) . " <br/><i class='bi bi-info-circle-fill' data-toggle='tooltip' data-html='true' title='Le nombre de postes est issu de l&apos;arrêté publié par le Journal Officiel.<br/>L&apos;année correspond à l&apos;année de publication au Journal Officiel.<br/>Le nombre de postes exclut les CESP.<br/>Les CHU avec zéro poste dans cette spécialité ne sont pas affichés.'></i></th>";
		echo "<th style='width:20%'> Rang dernier " . escapeHtml($libelleDernier) . " <br/><i class='bi bi-info-circle-fill' data-toggle='tooltip'  data-html='true' title='A partir de 2024, il s&apos;agit du rang limite par groupe de spécialités.<br>Auparavant c&apos;était le rang limite national par spécialité.<br/>Un rang à zéro signifie qu&apos;il n&apos;y avait pas de poste cette année là dans ce CHU pour cette spécialité.'></i></th>";
		echo "<th style='width:10%;'> ".$montant->format($nbCESP)." CESP " . escapeHtml($libelleCesp) . " <br/><i class='bi bi-info-circle-fill' data-toggle='tooltip' data-html='true' title='Le nombre de postes réservés aux CESP est issu de l&apos;arrêté publié par le Journal Officiel.<br/>Une cellule vide signifie qu&apos;il n&apos;y a pas de poste CESP pour cette spécialité dans ce CHU.'></i></th>";
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
			$dernierValeur = intval($DernierRef);
			$posteValeur = intval($PosteRef);
			$cespValeur = intval($CESPRef);
			$dernier = ($dernierValeur > 0) ? $montant->format($dernierValeur) : "";
			echo "<td style='padding-left:5%;' " . $href . ">" . $CHU . "</td><td class='text-center' " . $href . ">" . $montant->format($posteValeur) . "</td>";
			echo "<td class='text-center' " . $href . ">" . $dernier .  "</td>";
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