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
	$libelle = "2024";
	if (($reference <> "") and ($reference <> 0)) {
		$libelle = $reference;
	}
	return $libelle;
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

// Convertit une chaîne en UTF-8 si nécessaire (fonctionnon utilisée)
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