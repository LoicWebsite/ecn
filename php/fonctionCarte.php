<?php
	function afficherVilleCHU ($CHU, $libelleCHU, $libelleVille, $cx, $cy, $xTexte, $yTexte) {

		echo "<style> hr { background-color: white; margin: 6px; } </style>";
		
		$montant = new NumberFormatter("fr-FR", NumberFormatter::DECIMAL);		
		
		// est-ce que le CHU de la carte est présent dans le tableau (issu de la recherche sql en base avec les critères saisis)
		if (in_array($CHU, $GLOBALS['listeCHU'])) {
 			$i = array_search($CHU, $GLOBALS['listeCHU']);
 		} else {
			$i = -1;
 		}

		// le nombre de poste CESP est-il compatible avec le critère Cesp saisi ?
		if ($GLOBALS['cesp'] == "on") {
			if (($GLOBALS['listeCesp'][$i] != null) and ($GLOBALS['listeCesp'][$i] > 0 )) {
				$cespOk = true;
			} else {
				$cespOk = false;
			}
		} else {
			$cespOk = true;
		}

		// le rang est-il compatible avec celui saisi en critère ?
		if (($GLOBALS['rang'] != "rangIndifferent") and ($GLOBALS['rang'] != null) and ($GLOBALS['rang'] != 0)) {
			if ($GLOBALS['listeDernier'][$i] >= $GLOBALS['rang']) {
				$rangOk = true;
			} else {
				$rangOk = false;
			}
		} else {
			$rangOk = true;
		}	
	
		// préparation des libellés pour les tooltip
		if ($i >= 0) {
			$libelleDernier = $GLOBALS['listeDernier'][$i];
			$libellePoste = $GLOBALS['listePoste'][$i];
			if ($GLOBALS['listeCesp'][$i] != null) {
				$libelleCesp = $GLOBALS['listeCesp'][$i];
			} else {
				$libelleCesp = 0;
			}
		} else {
			$libelleDernier = 0;
			$libellePoste = 0;
			$libelleCesp = 0;
		}

		// récupération de l'URL Céline pour le double click
		if ($i >= 0) {
			$urlCeline = " data-url='" . $GLOBALS['listeUrl'][$i] . "' ";
		} else {
			$urlCeline = "";
		}

		// affichage du cercle et du tooltip (attribut title sur la balise a)
		if ($GLOBALS['specialite']=='') {
			$libelleSpecialite = getLibelleSpecialite($GLOBALS['code']);	// appel de la fonction depuis le questionnaire
		} else {
			$libelleSpecialite = $GLOBALS['specialite'];					// appel de la fonction depuis la page principale du site
		}
		$annee = $GLOBALS['reference'];
		if (($rangOk) and ($cespOk) and ($libellePoste > 0)) {
			echo "<a data-html='true' " . $urlCeline . " title='" . $libelleCHU . "<br/>" . $libelleSpecialite . "<br/>" . " <strong>accessible</strong><hr>dernier <small>en " . $annee . "</small> : " . $libelleDernier . "<br/>poste <small>en 2024</small> : " . $libellePoste . "<br/>CESP <small>en 2024</small> : " . $libelleCesp . "'>";
			echo "<circle cx='" . $cx . "' cy='" . $cy . "' r='5' stroke='gray' stroke-width='1' fill='white' />";
		} else {
			echo "<a data-html='true' " . $urlCeline . " title='" . $libelleCHU . "<br/>" . $libelleSpecialite . "<br/>" . " <strong>non accessible</strong><hr>dernier <small>en " . $annee . "</small> : " . $libelleDernier . "<br/>poste <small>en 2024</small> : " . $libellePoste . "<br/>CESP <small>en 2024</small> : " . $libelleCesp . "'>";
			echo "<circle cx='" . $cx . "' cy='" . $cy . "' r='5' stroke='gray' stroke-width='1' fill='white' />";
		}

		// affichage des noms de ville composé avec 1 tiret sur 2 lignes centrées, sauf pour AP-HP et AP-HM
		$positionTiret = strpos($libelleVille, "-");
		if (($positionTiret) and (strlen($libelleVille) > 5)) {
			if (($rangOk) and ($cespOk) and ($libellePoste > 0)) {
				echo "<text class='accessible' text-anchor='middle'>";
			} else {
				echo "<text text-anchor='middle'>";
			}
			echo "<tspan x='". $xTexte . "' y='" . $yTexte . "'>" . substr($libelleVille, 0, $positionTiret + 1) . "</tspan>";
			echo "<tspan x='". $xTexte . "' dy='19'>" . substr($libelleVille, $positionTiret + 1) . "</tspan>";
			echo "</text>";
		} else {
			if (($rangOk) and ($cespOk) and ($libellePoste > 0)) {
				echo "<text class='accessible' x='" . $xTexte . "' y='" . $yTexte . "'>" . $libelleVille . "</text>";
			} else {
				echo "<text x='" . $xTexte . "' y='" . $yTexte . "'>" . $libelleVille . "</text>";
			}
		}
		
		echo "</a>";		

		return "/n";
	}
?>