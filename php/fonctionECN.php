<?php 

// ouvre la base de données
function openDatabase() {
    static $db = null; // Pour éviter de recréer la connexion à chaque appel
    if ($db === null) {
        try {
            $db = new PDO("mysql:host=localhost;dbname=ecn;charset=utf8", "USER", "PASSWORD");
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $erreur) {
            die('Erreur connexion base : ' . $erreur->getMessage());
        }
    }
    return $db;
}

// retourne le libellé du rang
function getLibelleRang ($rang) {
	$libelle = "indifférent";
	$montant = new NumberFormatter("fr-FR", NumberFormatter::DECIMAL);
	if (($rang <> "") and ($rang <> "rangIndifferent") and ($rang > 0)) {
		$libelle = $montant->format($rang);
	}
	return $libelle;
}

// retourne le libellé de l'année de référence
function getLibelleReference ($reference) {
	$libelle = "2026";
	if (($reference <> "") and ($reference <> 0)) {
		$libelle = $reference;
	}
	return $libelle;
}

// retourne les années à afficher dans les tooltips des tableaux Poste/CESP
// Fonction générique: aucune année codée en dur.
// - Si les sources dynamiques sont fournies, on affiche les années réellement utilisées.
// - Sinon, on affiche l'année de référence pour tous les libellés.
// Les clés de retour sont toujours présentes pour éviter les cas particuliers côté appelant.
function getLibellesTooltipPosteCesp($reference, $sources = null) {
	$referenceLibelle = strval($reference);
	$libelleDernier = $referenceLibelle;
	$libellePoste = $referenceLibelle;
	$libelleCesp = $referenceLibelle;

	if (is_array($sources)) {
		if (isset($sources['dernier']['year']) and ($sources['dernier']['year'] !== null)) {
			$libelleDernier = strval($sources['dernier']['year']);
		}
		if (isset($sources['poste']['year']) and ($sources['poste']['year'] !== null)) {
			$libellePoste = strval($sources['poste']['year']);
		}
		if (isset($sources['cesp']['year']) and ($sources['cesp']['year'] !== null)) {
			$libelleCesp = strval($sources['cesp']['year']);
		} else {
			$libelleCesp = $libellePoste;
		}

	}

	/*
	 * Ancienne logique supprimée car non générique:
	 * - cas spécifique "2026" / "2025"
	 * - fallback fixe avant 2020
	 *
	 * Le fallback est désormais piloté en amont par resolveAnnualRangSources(),
	 * qui choisit dynamiquement les années réellement disponibles en base.
	 */

	return [
		'dernier' => $libelleDernier,
		'poste' => $libellePoste,
		'cesp' => $libelleCesp,
		// clé conservée pour compatibilité avec l'ancien code
		'posteCesp' => $libellePoste
	];
}

// récupère les années disponibles en base pour DernierXXXX, PosteXXXX et CESPXXXX
// Ces listes servent de base pour déterminer la phase annuelle et les fallbacks.
// Règle métier historique: avant 2020, il n'existe pas de colonnes Poste/CESP annuelles.
// Dans ce cas, on prend par défaut les colonnes les plus récentes disponibles.
function getRangYearColumns($db) {
	$colonnes = [
		'Dernier' => [],
		'Poste' => [],
		'CESP' => []
	];

	$sql = "SELECT COLUMN_NAME
			FROM INFORMATION_SCHEMA.COLUMNS
			WHERE TABLE_SCHEMA = DATABASE()
			  AND TABLE_NAME = 'Rang'";

	try {
		$stmt = $db->prepare($sql);
		$stmt->execute();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$colonne = $row['COLUMN_NAME'];
			if (preg_match('/^(Dernier|Poste|CESP)(\\d{4})$/', $colonne, $matches)) {
				$prefixe = $matches[1];
				$annee = intval($matches[2]);
				$colonnes[$prefixe][] = $annee;
			}
		}
	} catch(PDOException $erreur) {
		// En cas de souci, on renvoie des listes vides.
	}

	foreach ($colonnes as $prefixe => $annees) {
		$anneesUniques = array_values(array_unique($annees));
		sort($anneesUniques, SORT_NUMERIC);
		$colonnes[$prefixe] = $anneesUniques;
	}

	return $colonnes;
}

// retourne la phase annuelle selon les colonnes présentes pour l'année demandée
// 1 = début d'année: données de l'année absentes/incomplètes
// 2 = milieu d'année: PosteAAAA et CESPAAAA présents, DernierAAAA absent
// 3 = fin d'année: DernierAAAA + PosteAAAA + CESPAAAA présents
// Note: pour les références < 2020, on se retrouve généralement en phase 1,
// car les colonnes Poste/CESP n'existent pas pour ces années.
function getAnnualDataPhase($rangYearColumns, $referenceYear) {
	$referenceYear = intval($referenceYear);
	$hasDernier = in_array($referenceYear, $rangYearColumns['Dernier'], true);
	$hasPoste = in_array($referenceYear, $rangYearColumns['Poste'], true);
	$hasCesp = in_array($referenceYear, $rangYearColumns['CESP'], true);

	if ($hasDernier and $hasPoste and $hasCesp) {
		return 3;
	}

	if ((!$hasDernier) and $hasPoste and $hasCesp) {
		return 2;
	}

	return 1;
}

// résout la colonne à utiliser pour un préfixe donné (Dernier/Poste/CESP)
// priorité:
//   1) année exacte
//   2) année inférieure la plus récente (comportement attendu en phase 1/2)
//   3) année la plus récente disponible (sécurité si l'année de référence est trop ancienne)
//      et comportement nominal pour les références avant 2020 côté Poste/CESP.
function resolveYearColumn($years, $prefix, $referenceYear) {
	$referenceYear = intval($referenceYear);
	if (empty($years)) {
		return [
			'column' => null,
			'year' => null,
			'mode' => 'missing'
		];
	}

	if (in_array($referenceYear, $years, true)) {
		return [
			'column' => $prefix . strval($referenceYear),
			'year' => $referenceYear,
			'mode' => 'exact'
		];
	}

	$yearsInferieures = array_filter($years, function($annee) use ($referenceYear) {
		return $annee < $referenceYear;
	});

	if (!empty($yearsInferieures)) {
		$anneeFallback = max($yearsInferieures);
		return [
			'column' => $prefix . strval($anneeFallback),
			'year' => $anneeFallback,
			'mode' => 'fallback_previous'
		];
	}

	$anneeFallback = max($years);
	return [
		'column' => $prefix . strval($anneeFallback),
		'year' => $anneeFallback,
		'mode' => 'fallback_latest'
	];
}

// résout les 3 sources annuelles (dernier, poste, cesp) pour une année de référence
// Les deux scripts tableau consomment ce résultat pour rester strictement cohérents.
function resolveAnnualRangSources($rangYearColumns, $referenceYear) {
	return [
		'dernier' => resolveYearColumn($rangYearColumns['Dernier'], 'Dernier', $referenceYear),
		'poste' => resolveYearColumn($rangYearColumns['Poste'], 'Poste', $referenceYear),
		'cesp' => resolveYearColumn($rangYearColumns['CESP'], 'CESP', $referenceYear)
	];
}

// retourne le libellé du type de spécialité
function getLibelleType ($type) {
	$libelle = "indifférent";
	switch ($type) {
		case "chirurgie" :			$libelle = "chirurgie"; break;
		case "medico-chirurgical" : $libelle = "médico-chirurgical"; break;
		case "organe" :				$libelle = "médecine d'organe"; break;
		case "transversal" :		$libelle = "médecine transversale"; break;
	}
	return $libelle;
}

// retourne le libellé du sélecteur CESP
function getLibelleCesp ($cesp) {
	$libelle = "non";
	if ($cesp == "on") {
		$libelle = "oui";
	}
	return $libelle;
}

// retourne le libellé du type de spécialité en fonction de la nature en base
function getLibelleTypeNature ($type, $nature) {
	$libelle = "indifférent";
	if (($type == "medecine") and ($nature == "transversale")) {
		$libelle = "médecine transversale";
	} elseif (($type == "medecine") and ($nature == "organe")) {
		$libelle = "médecine d'organe";
	} elseif ($type == "mixte") {
		$libelle = "médico-chirurgical";
	} elseif ($type == "chirurgie") {
		$libelle = "chirurgie";
	}
	return $libelle;
}

// retourne le libellé du lieu d'exercice
function getLibelleLieu ($lieu) {
	$libelle = "indifférent";
	switch ($lieu) {
		case "hopital" :	$libelle = "à l'hôpital (ou en clinique)"; break;
		case "ville" :		$libelle = "en cabinet (en ville)"; break;
		case "autre" :		$libelle = "autre"; break;
	}
	return $libelle;
}

// retourne le libellé de la durée d'internat
function getLibelleInternat ($internat) {
	$libelle = "indifférente";
	$montant = new NumberFormatter("fr-FR", NumberFormatter::DECIMAL);
	if (($internat <> "") and ($internat <> "internatIndifferent") and ($internat > 0)) {
		$libelle = $montant->format($internat) . " ans";
	}
	return $libelle;
}

// retourne le libellé du type de spécialité
function getLibelleBenefice ($benefice) {
	$libelle = "indifférent";
	switch ($benefice) {
		case "benefice60" :		$libelle = "&le; 60 k€"; break;
		case "benefice100" : 	$libelle = "= 60 - 100 k€"; break;
		case "benefice140" :	$libelle = "= 100 - 140 k€"; break;
		case "benefice500" :	$libelle = "&ge; 140 k€"; break;
	}
	return $libelle;
}

// retourne le libellé de la spécialité
function getLibelleSpecialite ($codeSpecialite) {
	$libelle = "spécialité inconnue";
	switch ($codeSpecialite) {
		case 'ATT' : $libelle = 'En attente de publication'; break;
		case 'CMF' : $libelle = 'Chirurgie maxillo-faciale'; break;
		case 'COR' : $libelle = 'Chirurgie orale'; break;
		case 'COT' : $libelle = 'Chirurgie orthopédique et traumatologique'; break;
		case 'CPD' : $libelle = 'Chirurgie pédiatrique'; break;
		case 'CPR' : $libelle = 'Chirurgie plastique, reconstructrice et esthétique'; break;
		case 'CTC' : $libelle = 'Chirurgie thoracique et cardiovasculaire'; break;
		case 'CVA' : $libelle = 'Chirurgie vasculaire'; break;
		case 'CVD' : $libelle = 'Chirurgie viscérale et digestive'; break;
		case 'GYO' : $libelle = 'Gynécologie obstétrique'; break;
		case 'NCU' : $libelle = 'Neurochirurgie'; break;
		case 'OPH' : $libelle = 'Ophtalmologie'; break;
		case 'ORL' : $libelle = 'Oto-rhino-laryngologie - chirurgie cervico-faciale'; break;
		case 'URO' : $libelle = 'Urologie'; break;
		case 'ALL' : $libelle = 'Allergologie'; break;
		case 'ACP' : $libelle = 'Anatomie et cytologie pathologiques'; break;
		case 'ARE' : $libelle = 'Anesthésie-réanimation'; break;
		case 'DVE' : $libelle = 'Dermatologie et vénéréologie'; break;
		case 'EDN' : $libelle = 'Endocrinologie-diabétologie-nutrition'; break;
		case 'GEN' : $libelle = 'Génétique médicale'; break;
		case 'GER' : $libelle = 'Gériatrie'; break;
		case 'GYM' : $libelle = 'Gynécologie médicale'; break;
		case 'HEM' : $libelle = 'Hématologie'; break;
		case 'HGE' : $libelle = 'Hépato-gastro-entérologie'; break;
		case 'MIT' : $libelle = 'Maladies infectieuses et tropicales'; break;
		case 'MCA' : $libelle = 'Médecine cardiovasculaire'; break;
		case 'MGE' : $libelle = 'Médecine générale'; break;
		case 'MIR' : $libelle = 'Médecine intensive-réanimation'; break;
		case 'MII' : $libelle = 'Médecine interne et immunologie clinique'; break;
		case 'MLE' : $libelle = 'Médecine légale et expertises médicales'; break;
		case 'NUC' : $libelle = 'Médecine nucléaire'; break;
		case 'MPR' : $libelle = 'Médecine physique et de réadaptation'; break;
		case 'MTR' : $libelle = 'Médecine et santé au travail'; break;
		case 'MUR' : $libelle = 'Médecine d’urgence'; break;
		case 'MVA' : $libelle = 'Médecine vasculaire'; break;
		case 'NEP' : $libelle = 'Néphrologie'; break;
		case 'NEU' : $libelle = 'Neurologie'; break;
		case 'ONC' : $libelle = 'Oncologie'; break;
		case 'PED' : $libelle = 'Pédiatrie'; break;
		case 'PNE' : $libelle = 'Pneumologie'; break;
		case 'PSY' : $libelle = 'Psychiatrie'; break;
		case 'RAI' : $libelle = 'Radiologie et imagerie médicale'; break;
		case 'RHU' : $libelle = 'Rhumatologie'; break;
		case 'SPU' : $libelle = 'Santé publique'; break;
		case 'BM' : $libelle = 'Biologie médicale'; break;
	}
	return $libelle;
}

// Convertit une chaîne en UTF-8 si nécessaire (fonction non utilisée)
// function toUtf8($string) {
//     if (is_string($string)) {
//         if (!mb_detect_encoding($string, 'UTF-8', true)) {
//             return mb_convert_encoding($string, 'UTF-8', 'ISO-8859-1');
//         }
//         return $string;
//     }
//     return $string; // Retourne tel quel si ce n'est pas une chaîne
// }

?>