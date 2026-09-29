<?php
	/** Route l'affichage vers la carte des CHU ou celle des départements.
	 * @param string $page Mode de carte (`poste`, `densite` ou `effectif`).
	 * @param string $CHU Identifiant utilisé par la carte des CHU.
	 * @param string $libelleCHU Libellé affiché dans le tooltip.
	 * @param string $libelleVille Libellé affiché sur la carte.
	 * @param string $cx Coordonnée horizontale du marqueur.
	 * @param string $cy Coordonnée verticale du marqueur.
	 * @param string $xTexte Coordonnée horizontale du libellé.
	 * @param string $yTexte Coordonnée verticale du libellé.
	 * @return mixed Résultat du rendu, selon le type de carte.
	 */
	function afficherVille ($page, $CHU, $libelleCHU, $libelleVille, $cx, $cy, $xTexte, $yTexte) {
		if ($page == 'poste') {
			return afficherVilleCHU ($CHU, $libelleCHU, $libelleVille, $cx, $cy, $xTexte, $yTexte);
		} else {
			return afficherDepartement($CHU, $libelleCHU, $libelleVille, $cx, $cy, $xTexte, $yTexte);
		}
	}
	
	/** Affiche un marqueur départemental sans calcul de rang.
	 * @param string $CHU Identifiant du département ou élément de carte.
	 * @param string $libelleCHU Libellé du tooltip.
	 * @param string $libelleVille Libellé associé au marqueur.
	 * @param string $cx Coordonnée horizontale du marqueur.
	 * @param string $cy Coordonnée verticale du marqueur.
	 * @param string $xTexte Coordonnée horizontale du texte.
	 * @param string $yTexte Coordonnée verticale du texte.
	 * @return void
	 */
	function afficherDepartement ($CHU, $libelleCHU, $libelleVille, $cx, $cy, $xTexte, $yTexte) {
		$libelleCHUSafe = htmlspecialchars((string)$libelleCHU, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
		
		// affichage du CHU
		echo "<a data-html='true' title='" . $libelleCHUSafe . "'>";
		echo "<circle cx='" . $cx . "' cy='" . $cy . "' r='5' stroke='gray' stroke-width='1' fill='white'>";
		echo "</a>";

		return;
	}

	/** Affiche un CHU et colore son libellé selon l'accessibilité des critères.
	 * Le rang sélectionné pilote la couleur ; le tooltip présente toujours les deux rangs.
	 * @param string $CHU Identifiant du CHU utilisé dans les listes globales.
	 * @param string $libelleCHU Libellé du tooltip.
	 * @param string $libelleVille Libellé affiché sur la carte.
	 * @param string $cx Coordonnée horizontale du marqueur.
	 * @param string $cy Coordonnée verticale du marqueur.
	 * @param string $xTexte Coordonnée horizontale du texte.
	 * @param string $yTexte Coordonnée verticale du texte.
	 * @return string Saut de ligne historique utilisé par l'appelant.
	 */
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
		if (($GLOBALS['cesp'] == "on") and ($i >=0)) {
			if (($GLOBALS['listeCesp'][$i] != null) and ($GLOBALS['listeCesp'][$i] > 0 )) {
				$cespOk = true;
			} else {
				$cespOk = false;
			}
		} else {
			$cespOk = true;
		}

		// le rang est-il compatible avec celui saisi en critère ?
		if (($GLOBALS['rang'] != "rangIndifferent") and ($GLOBALS['rang'] != null) and ($GLOBALS['rang'] != 0) and ($i >=0)) {
			if (($GLOBALS['listeRangCompare'][$i] ?? $GLOBALS['listeDernier'][$i]) >= $GLOBALS['rang']) {
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
			$libelleDernierPrincipal = $GLOBALS['listeDernierPrincipal'][$i] ?? $libelleDernier;
			$libelleDernierCESP = $GLOBALS['listeDernierCESP'][$i] ?? 0;
			$libellePoste = $GLOBALS['listePoste'][$i];
			if ($GLOBALS['listeCesp'][$i] != null) {
				$libelleCesp = $GLOBALS['listeCesp'][$i];
			} else {
				$libelleCesp = 0;
			}
		} else {
			$libelleDernier = 0;
			$libelleDernierPrincipal = 0;
			$libelleDernierCESP = 0;
			$libellePoste = 0;
			$libelleCesp = 0;
		}

		// affichage du cercle et du tooltip (attribut title sur la balise a)
		if ($GLOBALS['specialite']=='') {
			$libelleSpecialite = getLibelleSpecialite($GLOBALS['code']);	// appel de la fonction depuis le questionnaire
		} else {
			$libelleSpecialite = $GLOBALS['specialite'];					// appel de la fonction depuis la page principale du site
		}

		// Libellés d'années du tooltip basés sur les sources réellement utilisées.
		$libellesTooltip = getLibellesTooltipPosteCesp(
			intval($GLOBALS['reference']),
			isset($GLOBALS['rangSources']) ? $GLOBALS['rangSources'] : null
		);
		$anneeDernier = isset($libellesTooltip['dernier']) ? $libellesTooltip['dernier'] : strval($GLOBALS['reference']);
		$anneePoste = isset($libellesTooltip['poste']) ? $libellesTooltip['poste'] : strval($GLOBALS['reference']);
		$anneeCesp = isset($libellesTooltip['cesp']) ? $libellesTooltip['cesp'] : $anneePoste;

		$libelleCHUSafe = htmlspecialchars((string)$libelleCHU, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
		$libelleSpecialiteSafe = htmlspecialchars((string)$libelleSpecialite, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
		$anneeDernierSafe = htmlspecialchars((string)$anneeDernier, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
		$anneePosteSafe = htmlspecialchars((string)$anneePoste, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
		$anneeCespSafe = htmlspecialchars((string)$anneeCesp, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
		$libelleDernierSafe = htmlspecialchars((string)$libelleDernier, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
		$libelleDernierPrincipalSafe = htmlspecialchars((string)$libelleDernierPrincipal, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
		$libelleDernierCESPSafe = htmlspecialchars((string)$libelleDernierCESP, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
		$libellePosteSafe = htmlspecialchars((string)$libellePoste, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
		$libelleCespSafe = htmlspecialchars((string)$libelleCesp, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
		if (($rangOk) and ($cespOk) and ($libellePoste > 0)) {
			 echo "<a data-html='true' title='" . $libelleCHUSafe . "<br/>" . $libelleSpecialiteSafe . "<br/>" . " <strong>accessible</strong><hr>Dernier <small>en " . $anneeDernierSafe . "</small> : " . $libelleDernierPrincipalSafe . "<br/>Dernier CESP <small>en " . $anneeDernierSafe . "</small> : " . $libelleDernierCESPSafe . "<br/>poste <small>en " . $anneePosteSafe . "</small> : " . $libellePosteSafe . "<br/>CESP <small>en " . $anneeCespSafe . "</small> : " . $libelleCespSafe . "'>";
			echo "<circle cx='" . $cx . "' cy='" . $cy . "' r='5' stroke='gray' stroke-width='1' fill='white' />";
		} else {
			echo "<a data-html='true' title='" . $libelleCHUSafe . "<br/>" . $libelleSpecialiteSafe . "<br/>" . " <strong>non accessible</strong><hr>Dernier <small>en " . $anneeDernierSafe . "</small> : " . $libelleDernierPrincipalSafe . "<br/>Dernier CESP <small>en " . $anneeDernierSafe . "</small> : " . $libelleDernierCESPSafe . "<br/>poste <small>en " . $anneePosteSafe . "</small> : " . $libellePosteSafe . "<br/>CESP <small>en " . $anneeCespSafe . "</small> : " . $libelleCespSafe . "'>";
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