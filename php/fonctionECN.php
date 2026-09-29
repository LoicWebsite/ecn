<?php 

/** Ouvre et met en cache la connexion PDO à la base ECN pour la requête courante.
 * @return PDO Connexion configurée pour lever les exceptions SQL.
 */
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

/** Formate un rang numérique pour son affichage en français.
 * @param mixed $rang Rang numérique ou valeur représentant l'absence de filtre.
 * @return string Libellé formaté ou « indifférent ».
 */
function getLibelleRang ($rang) {
	$libelle = "indifférent";
	$montant = new NumberFormatter("fr-FR", NumberFormatter::DECIMAL);
	if (($rang <> "") and ($rang <> "rangIndifferent") and ($rang > 0)) {
		$libelle = $montant->format($rang);
	}
	return $libelle;
}

/** Retourne l'année de référence sous forme de libellé, avec 2026 par défaut.
 * @param mixed $reference Année sélectionnée.
 * @return string Année à afficher.
 */
function getLibelleReference ($reference) {
	$libelle = "2026";
	if (($reference <> "") and ($reference <> 0)) {
		$libelle = $reference;
	}
	return $libelle;
}

/** Détermine les années source à afficher dans les tooltips des tableaux Poste/CESP.
 * Utilise les années réellement résolues quand elles sont fournies, sinon l'année de référence.
 * @param mixed $reference Année demandée.
 * @param array|null $sources Années source renvoyées par resolveAnnualRangSources().
 * @return array Libellés `dernier`, `poste` et `cesp`.
 */
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

	return [
		'dernier' => $libelleDernier,
		'poste' => $libellePoste,
		'cesp' => $libelleCesp
	];
}

/** Liste les années ayant des données pour chaque colonne de Rang.
 * Une année peut avoir des postes/CESP sans rang principal ou CESP publié.
 * @param PDO $db Connexion à la base ECN.
 * @return array Années disponibles pour Dernier, DernierCESP, Poste et CESP.
 */
function getRangYearColumns($db) {
	$colonnes = [
		'Dernier' => [],
		'DernierCESP' => [],
		'Poste' => [],
		'CESP' => []
	];

	$sql = "SELECT Annee,
				MAX(Dernier IS NOT NULL) AS hasDernier,
				MAX(DernierCESP IS NOT NULL) AS hasDernierCESP,
				MAX(Poste IS NOT NULL) AS hasPoste,
				MAX(CESP IS NOT NULL) AS hasCesp
			FROM Rang
			GROUP BY Annee
			ORDER BY Annee";

	try {
		$stmt = $db->prepare($sql);
		$stmt->execute();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$annee = intval($row['Annee']);
			if ((int)$row['hasDernier'] === 1) {
				$colonnes['Dernier'][] = $annee;
			}
			if ((int)$row['hasDernierCESP'] === 1) {
				$colonnes['DernierCESP'][] = $annee;
			}
			if ((int)$row['hasPoste'] === 1) {
				$colonnes['Poste'][] = $annee;
			}
			if ((int)$row['hasCesp'] === 1) {
				$colonnes['CESP'][] = $annee;
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

/** Résout l'année du rang principal à afficher, avec repli sur la dernière année connue.
 * @param PDO $db Connexion à la base ECN.
 * @param mixed $referenceYear Année demandée.
 * @return int Année source du rang principal.
 */
function getRangDisplayYear($db, $referenceYear) {
	// Règle métier : le rang du dernier admis peut ne pas être publié pour
	// l'année choisie. On affiche alors le dernier rang de l'année précédente
	// connue, et les écrans doivent reprendre cette année dans leur libellé.
	$years = getRangYearColumns($db);
	$source = resolveYearSource($years['Dernier'], $referenceYear);
	return ($source['year'] !== null) ? $source['year'] : intval($referenceYear);
}

/** Résout l'année des postes et contrats CESP, indépendamment de l'année des rangs.
 * @param PDO $db Connexion à la base ECN.
 * @param mixed $referenceYear Année demandée.
 * @return int Année source des postes/CESP.
 */
function getRangDisplayPosteYear($db, $referenceYear) {
	// Les postes et les CESP suivent la même année que la sélection lorsque
	// les données existent. Pour une référence ancienne sans postes/CESP,
	// on utilise la dernière année disponible, indépendamment de l'année du rang.
	$years = getRangYearColumns($db);
	$poste = resolveYearSource($years['Poste'], $referenceYear);
	$cesp = resolveYearSource($years['CESP'], $referenceYear);
	$yearsAvailable = array_filter([$poste['year'], $cesp['year']], function($year) {
		return $year !== null;
	});
	return empty($yearsAvailable) ? intval($referenceYear) : max($yearsAvailable);
}

/** Résout le rang sélectionné et vérifie que des données CESP existent pour l'année affichée.
 * Le mode CESP est ignoré si l'année effective n'a pas de rang CESP distinct.
 * @param PDO $db Connexion à la base ECN.
 * @param mixed $referenceYear Année demandée.
 * @param string|null $requestedMode Mode explicite, ou mode reçu dans `modeRang`.
 * @param array|null $yearColumns Inventaire annuel déjà chargé, s'il est disponible.
 * @return array Année effective, disponibilité CESP, mode et colonne SQL autorisée.
 */
function getRangDisplayMode($db, $referenceYear, $requestedMode = null, $yearColumns = null) {
	$referenceYear = intval($referenceYear);
	$years = is_array($yearColumns) ? $yearColumns : getRangYearColumns($db);
	$rangSource = resolveYearSource($years['Dernier'], $referenceYear);
	$displayYear = ($rangSource['year'] !== null) ? $rangSource['year'] : $referenceYear;
	if (isset($_GET['modeRangCesp']) && $_GET['modeRangCesp'] === 'cesp') {
		$requestedMode = 'cesp';
	} elseif ($requestedMode === null && isset($_GET['modeRang'])) {
		$requestedMode = $_GET['modeRang'];
	}
	if ($requestedMode === null && isset($_GET['cesp']) && $_GET['cesp'] === 'on') {
		$requestedMode = 'cesp';
	}
	$cespAvailable = in_array($displayYear, $years['DernierCESP'], true);
	$mode = ($requestedMode === 'cesp' && $cespAvailable) ? 'cesp' : 'principal';

	return [
		'year' => $displayYear,
		'available' => $cespAvailable,
		'mode' => $mode,
		'column' => ($mode === 'cesp') ? 'DernierCESP' : 'Dernier',
	];
}

/** Retourne l'expression SQL du rang utilisé pour filtrer l'accessibilité.
 * Le filtre CESP utilise le rang CESP quand il existe, sinon le rang principal.
 * @param string $cespFilter État du filtre « CESP uniquement ».
 * @return string Expression SQL sûre, limitée aux colonnes connues.
 */
function getRangFilterExpression($cespFilter) {
	if ($cespFilter === 'on') {
		return 'COALESCE(NULLIF(rangDernier.DernierCESP, 0), rangDernier.Dernier)';
	}
	return 'rangDernier.Dernier';
}

/** Contrepartie PHP de getRangFilterExpression(), pour la coloration et les comptages.
 * Doit rester alignée sur l'expression SQL, sans quoi une cellule peut être retenue
 * par la requête mais affichée comme inaccessible.
 * @param int $dernier Rang du dernier admis en liste principale.
 * @param int $dernierCesp Rang du dernier admis CESP.
 * @param string $cespFilter État du filtre « CESP uniquement ».
 * @return int Rang à comparer au rang visé.
 */
function getRangComparaison($dernier, $dernierCesp, $cespFilter) {
	if ($cespFilter === 'on' && intval($dernierCesp) > 0) {
		return intval($dernierCesp);
	}
	return intval($dernier);
}

/** Rend le poussoir Principal/CESP en préservant les critères de la vue courante.
 * @param array $params Critères de recherche à conserver dans des champs cachés.
 * @param string $mode Mode actif (`principal` ou `cesp`).
 * @return string Formulaire GET du sélecteur.
 */
function renderRangModeSelector($params, $mode) {
	$fields = [
		'code', 'specialite', 'chu', 'rang', 'reference', 'type', 'cesp',
		'lieu', 'internat', 'benefice', 'depuis', 'page'
	];
	$html = "<form method='get' class='d-flex align-items-center flex-wrap mb-3' aria-label='Mode de rang'>";
	foreach ($fields as $field) {
		if (isset($params[$field])) {
			$html .= "<input type='hidden' name='" . escapeHtml($field) . "' value='" . escapeHtml($params[$field]) . "'>";
		}
	}
	$html .= "<span class='mr-2'>Rang affiché :</span>";
	$html .= "<span class='mr-2'>Principal</span>";
	$html .= "<input type='hidden' name='modeRang' value='principal'>";
	$html .= "<span class='rang-mode-switch-control mx-1'>";
	$html .= "<input type='checkbox' class='rang-mode-switch-input' id='modeRangCesp' name='modeRangCesp' value='cesp' role='switch' aria-label='Rang CESP' onchange='this.form.submit()'" . (($mode === 'cesp') ? " checked" : "") . ">";
	$html .= "<label class='rang-mode-switch' for='modeRangCesp' title='Basculer le rang affiché'><span class='sr-only'>Basculer le rang affiché</span></label>";
	$html .= "</span><span class='ml-2'>CESP</span></form>";
	return $html;
}

/** Retourne le paramètre d'URL à ajouter pour conserver le mode CESP.
 * Le mode principal est le défaut et ne nécessite pas de paramètre.
 * @param string $mode Mode actif.
 * @return array Paramètre de query string, ou tableau vide.
 */
function getRangModeQueryParam($mode) {
	return ['modeRang' => ($mode === 'cesp') ? 'cesp' : 'principal'];
}

/** Fournit le texte partagé expliquant les modes Principal et CESP dans les tooltips.
 * @return string Texte HTML du tooltip.
 */
function getRangModeTooltipDescription() {
	return "Le rang affiché dépend du sélecteur : <strong>Principal</strong> indique le rang limite de la liste principale, <strong>CESP</strong> celui de la liste CESP.<br/>Les rangs CESP distincts sont disponibles pour 2024 et 2025.";
}

/** Regroupe les lignes Rang en une ligne par spécialité/CHU avec des clés annuelles.
 * Les rangs principaux absents d'une année héritent du dernier rang principal connu ;
 * les rangs CESP restent propres à leur année de publication.
 * @param PDO $db Connexion à la base ECN.
 * @param string|null $codeSpecialite Filtre optionnel par code de spécialité.
 * @param string|null $chu Filtre optionnel par CHU.
 * @return array Lignes larges indexées par spécialité et CHU.
 */
function getRangRowsWide($db, $codeSpecialite = null, $chu = null) {
	$sql = "SELECT CodeSpecialite, CHU, Annee, Poste, Dernier, DernierCESP, CESP
			FROM Rang";
	$where = [];
	$params = [];

	if ($codeSpecialite !== null) {
		$where[] = "CodeSpecialite = :codeSpecialite";
		$params[':codeSpecialite'] = $codeSpecialite;
	}
	if ($chu !== null) {
		$where[] = "CHU = :chu";
		$params[':chu'] = $chu;
	}
	if (!empty($where)) {
		$sql .= " WHERE " . implode(" AND ", $where);
	}
	$sql .= " ORDER BY CodeSpecialite, CHU, Annee";

	$stmt = $db->prepare($sql);
	$stmt->execute($params);
	$rows = [];

	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$key = $row['CodeSpecialite'] . "\n" . $row['CHU'];
		if (!isset($rows[$key])) {
			$rows[$key] = [
				'CodeSpecialite' => $row['CodeSpecialite'],
				'CHU' => $row['CHU'],
			];
		}

		$annee = intval($row['Annee']);
		$rows[$key]['Poste' . $annee] = $row['Poste'];
		$rows[$key]['Dernier' . $annee] = $row['Dernier'];
		$rows[$key]['DernierCESP' . $annee] = $row['DernierCESP'];
		$rows[$key]['CESP' . $annee] = $row['CESP'];
	}

	// Une année peut déjà contenir des postes/CESP alors que le dernier rang
	// n'est pas encore publié. Dans ce cas, conserver le dernier rang connu.
	foreach ($rows as &$wideRow) {
		$dernierConnu = null;
		$annees = array_keys($wideRow);
		sort($annees, SORT_STRING);
		foreach ($annees as $cle) {
			if (preg_match('/^Dernier(\\d{4})$/', $cle, $matches)) {
				if ($wideRow[$cle] !== null) {
					$dernierConnu = $wideRow[$cle];
				} elseif ($dernierConnu !== null) {
					$wideRow[$cle] = $dernierConnu;
				}
			}
		}
	}
	unset($wideRow);

	return array_values($rows);
}

/** Charge en une requête les rangs, postes et CESP des tableaux pour deux années données.
 * @param PDO $db Connexion à la base ECN.
 * @param int $anneePoste Année source des postes et contrats CESP.
 * @param int $anneeReference Année source des rangs.
 * @return array Lignes regroupées par code de spécialité.
 */
function getRangRowsBySpecialiteForYears($db, $anneePoste, $anneeReference) {
	$sql = "SELECT rangPoste.CodeSpecialite,
				rangPoste.CHU,
				COALESCE(rangDernier.Dernier, 0) AS Dernier,
				COALESCE(rangDernier.DernierCESP, 0) AS DernierCESP,
				COALESCE(rangPoste.Poste, 0) AS Poste,
				COALESCE(rangPoste.CESP, 0) AS CESP
			FROM Rang rangPoste
			LEFT JOIN Rang rangDernier
				ON rangDernier.CodeSpecialite = rangPoste.CodeSpecialite
				AND rangDernier.CHU = rangPoste.CHU
				AND rangDernier.Annee = :anneeReference
			WHERE rangPoste.Annee = :anneePoste
			ORDER BY rangPoste.CodeSpecialite, rangPoste.CHU";

	$stmt = $db->prepare($sql);
	$stmt->execute([
		':anneePoste' => intval($anneePoste),
		':anneeReference' => intval($anneeReference)
	]);
	$rowsBySpecialite = [];

	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$rowsBySpecialite[$row['CodeSpecialite']][] = $row;
	}

	return $rowsBySpecialite;
}

/** Résout une année source selon la priorité exact, précédente la plus récente, puis disponible.
 * @param int[] $years Années où la source existe.
 * @param mixed $referenceYear Année demandée.
 * @return array Année source, ou `null` si aucune source n'existe.
 */
function resolveYearSource($years, $referenceYear) {
	$referenceYear = intval($referenceYear);
	if (empty($years)) {
		return [
			'year' => null
		];
	}

	if (in_array($referenceYear, $years, true)) {
		return [
			'year' => $referenceYear
		];
	}

	$yearsInferieures = array_filter($years, function($annee) use ($referenceYear) {
		return $annee < $referenceYear;
	});

	if (!empty($yearsInferieures)) {
		$anneeFallback = max($yearsInferieures);
		return [
			'year' => $anneeFallback
		];
	}

	$anneeFallback = max($years);
	return [
		'year' => $anneeFallback
	];
}

/** Résout séparément les années source des rangs, des postes et des contrats CESP.
 * @param array $rangYearColumns Résultat de getRangYearColumns().
 * @param mixed $referenceYear Année demandée.
 * @return array Années source résolues sous les clés `dernier`, `poste` et `cesp`.
 */
function resolveAnnualRangSources($rangYearColumns, $referenceYear) {
	return [
		'dernier' => resolveYearSource($rangYearColumns['Dernier'], $referenceYear),
		'poste' => resolveYearSource($rangYearColumns['Poste'], $referenceYear),
		'cesp' => resolveYearSource($rangYearColumns['CESP'], $referenceYear)
	];
}

/** Traduit le code de type de spécialité en libellé utilisateur.
 * @param string $type Code de type.
 * @return string Libellé français.
 */
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

/** Traduit l'état du filtre CESP en libellé utilisateur.
 * @param string $cesp Valeur du filtre (`on` ou autre).
 * @return string « oui » ou « non ».
 */
function getLibelleCesp ($cesp) {
	$libelle = "non";
	if ($cesp == "on") {
		$libelle = "oui";
	}
	return $libelle;
}

/** Déduit le libellé utilisateur depuis le type et la nature stockés en base.
 * @param string $type Type de spécialité en base.
 * @param string $nature Nature de spécialité en base.
 * @return string Libellé français.
 */
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

/** Traduit le code du lieu d'exercice en libellé utilisateur.
 * @param string $lieu Code du lieu.
 * @return string Libellé français.
 */
function getLibelleLieu ($lieu) {
	$libelle = "indifférent";
	switch ($lieu) {
		case "hopital" :	$libelle = "à l'hôpital (ou en clinique)"; break;
		case "ville" :		$libelle = "en cabinet (en ville)"; break;
		case "autre" :		$libelle = "autre"; break;
	}
	return $libelle;
}

/** Formate la durée d'internat ou indique qu'elle est indifférente.
 * @param mixed $internat Durée demandée ou valeur indifférente.
 * @return string Libellé formaté.
 */
function getLibelleInternat ($internat) {
	$libelle = "indifférente";
	$montant = new NumberFormatter("fr-FR", NumberFormatter::DECIMAL);
	if (($internat <> "") and ($internat <> "internatIndifferent") and ($internat > 0)) {
		$libelle = $montant->format($internat) . " ans";
	}
	return $libelle;
}

/** Traduit la tranche de bénéfice sélectionnée en libellé utilisateur.
 * @param string $benefice Code de tranche.
 * @return string Libellé HTML de la tranche.
 */
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

/** Traduit un code de spécialité en nom complet.
 * @param string $codeSpecialite Code de spécialité.
 * @return string Nom complet, ou « spécialité inconnue » si le code n'est pas répertorié.
 */
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