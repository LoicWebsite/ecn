<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Simulateur en ligne pour choisir une spécialité d'internat - tableau des spécialités et CHU accessibles en fonction du rang">

	<?php
		// favicons générés par https://realfavicongenerator.net
		include "php/favicon.php";
	
        // Google Analytics
		include "php/GoogleAnalytics.php";
	?>

    <title>tableau des spécialités correspondantes aux critères saisis</title>

	<?php
		// styles nécessaires à l'application (bootstrap + fontawasome + ECN)
		include "php/style.php";

		// Balise canonique pour éviter les doublons dus aux paramètres d'URL
		include "php/canonical.php";
	?>
	
	<style>
		table {
			border-collapse: separate;
			border-spacing: 0;
			margin:0;
		}		
		th {
			position:sticky;
			top: 50px;
			text-align:center;
			z-index:1;
			cursor: default;
		}
		td {
			text-align:right;
			padding-right:6px;
			cursor: default;
		}
		th:first-child {
			position:sticky;
			left:0px;
			z-index:2;
		}
		td:first-child {
			position:sticky;
			left:0px;
 			background-color:white;
 			text-align:center;
		}
		.critere {
			color:navy;
		}
		hr {
			background-color: white;
			margin: 6px;
		}

	</style>

  </head>
  <body id="hautdepage">

	<?php
		include "php/menu-questionnaire.php";
	?>
	
	<nav id="chemin">
		<div class="row" style='margin-top:80px;'>
			<div class="col-sm" aria-label="breadcrumb">
			  <ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="choix-specialite-chu-celine-ecn.php"><i class="bi bi-house-door-fill"></i></a></li>
				<li class="breadcrumb-item"><a href="#" onclick="questionnaire()">Critère</a></li>
				<li class="breadcrumb-item active" aria-current="page">Spécialité</li>
			  </ol>
			</div>
			<div class="col-sm">
				<p style='padding:10px;'>
					<button class="btn btn-primary btn-sm" onclick="liste()" title="Affichage des spécialités en liste"> en liste &nbsp; <i class="bi bi-list-ul" aria-hidden="true"></i></button>
					&nbsp;&nbsp;&nbsp;<button class="btn btn-secondary btn-sm" onclick="" title="Affichage des spécialités en table" disabled> en tableau &nbsp; <i class="bi bi-table" aria-hidden="true"></i></button>
				</p>
			</div>
			<div class="col-xl">
			</div>
		</div>
	</nav>

	<div id="reponse" class="container">
    	<h1 class="h5" style="text-align:left; margin-top:20px;">
    		<a class="h5" data-toggle="collapse" aria-expanded="false" aria-controls="critere" href="#critere"><i id="symbole" class="bi bi-plus-circle-fill" aria-hidden="true"></i>&nbsp; Vos critères de choix...</a>
		</h1>
		
	<?php

		// fonctions communes et récupération-contrôle des paramètres
		require_once "php/controleParametre.php";
		require_once "php/fonctionECN.php";

		// ouverture de la base de données
		$db = openDatabase();

		// affichage des critères
		echo "<div id='critere' class='collapse'>";
		echo "<div class='row'>";
		echo "<div class='col-md-5 offset-md-1'>";
			echo "<ul>";
			echo "<li>rang visé ou obtenu = <span class='critere'>" . getLibelleRang($rang) . "</span></li>";
			echo "<li>année de référence = <span class='critere'>" . getLibelleReference($reference) . "</span></li>";
			echo "<li>type de spécialité = <span class='critere'>" . getLibelleType($type) . "</span></li>";
			echo "<li>CESP uniquement = <span class='critere'>" . getLibelleCESP($cesp) . "</span></li>";
			echo "</ul>";
		echo "</div>";
		echo "<div class='col-md-5'>"; 
			echo "<ul>";
			echo "<li>durée de l'internat = <span class='critere'>" . getLibelleInternat($internat). "</span></li>";
			echo "<li>lieu d'exercice = <span class='critere'>" . getLibelleLieu($lieu) . "</span></li>";
			echo "<li>bénéfice net en libéral = <span class='critere'>" . getLibelleBenefice($benefice) . "</span></li>";
			echo "</ul>";
		echo "</div>";
		echo "</div>";
		echo "</div>\n";

		// affichage du table des rangs
		echo "<br/><div id='table'>";

		// initialisation des variables
		$numeroRecord = 0;
		$rangDernier = 0;
		$debutString = 0;
		$longueurString = 0;
		$nextIsCHU = false;
		$nextIsSpecialite = false;
		$CHU = "";
		$lastCHU = "";
		$specialite ="";
		$enteteLu = false;
		$heure = "00h00";
		$date = "01/01/1111";
		$tooltip = "auncune donnée";
		$lien = "#";
		$aucuneOffre = false;
		$texteRangDernier = "";
		$premiereLigne = false;
		$listeSpecialite = array();
		$listePoste = array();
		$listeCESP = array();
		$tablePoste = array(array());
		$tableCESP = array(array());
		$tableDernier = array(array());
		$tableDernierPrincipal = array(array());
		$tableDernierCESP = array(array());
		$tableRangCompare = array(array());
		$libelleCESP = 0;

		// préparation de la clause where pour sélectionner les spécialités en fonction des critères
		$where = " WHERE Type <> ''";

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

		$rangMode = getRangDisplayMode($db, $reference);
		$anneeReference = $rangMode['year'];
		$anneePoste = getRangDisplayPosteYear($db, $reference);
		$rangCespDisponible = $rangMode['available'];
		$afficherDernierCesp = ($rangMode['mode'] === 'cesp');
		$rangFilterExpression = getRangFilterExpression($cesp);
		if ($cesp == "on") {
			$where = $where . " AND COALESCE(rangPoste.CESP, 0) <> 0";
		}

		if (($rang <> "") and ($rang > 0) and ($rang <> "rangIndifferent")) {
			$where = $where . " AND COALESCE(" . $rangFilterExpression . ", 0) >= '" . $rang ."'";
		}

		if (($lieu <> "") and ($lieu <> "lieuIndifferent")) {
//			$where = $where . " AND Lieu = '" . utf8_decode($lieu) . "'";
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

		// Agrégation par spécialité à l'année Poste/CESP choisie ; le dernier
		// rang utilise séparément l'année résolue par getRangDisplayYear().

		$sql = "SELECT rangPoste.CodeSpecialite AS CodeSpecialite,
					SUM(COALESCE(rangPoste.Poste, 0)) AS Poste,
					SUM(COALESCE(rangPoste.CESP, 0)) AS CESP
				FROM Specialite
				INNER JOIN Rang rangPoste
					ON Specialite.CodeSpecialite = rangPoste.CodeSpecialite
					AND rangPoste.Annee = :anneePoste
				LEFT JOIN Rang rangDernier
					ON Specialite.CodeSpecialite = rangDernier.CodeSpecialite
					AND rangDernier.CHU = rangPoste.CHU
					AND rangDernier.Annee = :anneeReference
				" . $where . " GROUP BY rangPoste.CodeSpecialite;";

		if ($debug) echo "SQL = " . $sql ."<br/>";
 		
		// exécution de la requête
		try {
			$stmt = $db->prepare($sql);
			$stmt->execute([':anneePoste' => $anneePoste, ':anneeReference' => $anneeReference]);
			$result = $stmt;
			// récupération des données à afficher
			while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
				extract($row);
				$listeSpecialite[] = $CodeSpecialite;
				$listePoste[] = $Poste;
				$listeCESP[] = $CESP;
			}
		}
		catch(PDOException $erreur)	{
			echo "Erreur SELECT Specialite : " . $erreur->getMessage();
		}

		// titre et entête de table
		echo "<h2 class='h5' style='text-align:left'>" . count($listeSpecialite) ." spécialités correspondent à vos critères ";
		if (($rang != 0) and ($rang != null)) {
 			echo "pour le rang " . getLibelleRang($rang) . " en " . $reference;
 		} elseif ($cesp == "on") {
 			echo " en CESP ";
  		}
		echo "</h2><br/>";		
  		
		if ($rangCespDisponible) {
			$paramsModeRang = [
				'code' => $code,
				'specialite' => $specialite,
				'chu' => $chu,
				'rang' => $rang,
				'reference' => $reference,
				'type' => $type,
				'cesp' => $cesp,
				'lieu' => $lieu,
				'internat' => $internat,
				'benefice' => $benefice,
				'depuis' => $depuis,
			];
			echo renderRangModeSelector($paramsModeRang, $rangMode['mode']);
		}

		// tableau
		echo "<table class='table-hover table-bordered' style='width:100%;'>";
		echo "<thead><tr>";
		$libelleEnteteRang = $afficherDernierCesp ? "Rang dernier CESP " : "Rang dernier ";
		echo "<th>" . $libelleEnteteRang . escapeHtml($anneeReference) . "<br/><i class='bi bi-info-circle-fill' data-toggle='tooltip' data-html='true' title='Cliquer sur une <strong>spécialité</strong> dans l&apos;entête du tableau pour voir le détail des CHU pour cette spécialité.<br>À partir de 2024, le rang limite est publié par groupe de spécialités.";
		if ($rangCespDisponible) {
			echo "<br/>" . getRangModeTooltipDescription();
		}
		if ($afficherDernierCesp) {
			echo "<br>Un tiret signifie qu&apos;aucun rang CESP n&apos;a été publié pour ce CHU.";
		}
		echo "'></i></th>";
		$i = 0;
		foreach ($listeSpecialite as $specialite) {
			$libelleSpecialite = getLibelleSpecialite($specialite);
			if ($listeCESP[$i] == "") {
				$libelleCESP = "0";
			} else {
				$libelleCESP = $listeCESP[$i];
			}
			$libelle = $anneePoste;
			$tooltip = " data-toggle='tooltip' data-html='true' title='" . escapeHtml($libelleSpecialite) . "<hr>poste <small>en " . escapeHtml($libelle) . "</small> : " . escapeHtml($listePoste[$i]) . "<br/>CESP <small>en " . escapeHtml($libelle) . "</small> : " . escapeHtml($libelleCESP) . "' ";
			$href = "onclick='zoom(" . json_encode($specialite) . ")' ";
			echo "<th " . $tooltip . $href . " >&nbsp;" . $specialite . "&nbsp;</th>";
			$i += 1;
		}
		echo "<th style='padding-right:600px; background-color:white; border-style:hidden;'>&nbsp;</th></tr></thead><tbody>\n";

		// Charge une fois les valeurs annuelles de Rang pour toutes les spécialités.
		try {
			$rangRowsBySpecialite = getRangRowsBySpecialiteForYears($db, $anneePoste, $anneeReference);
		}
		catch(PDOException $erreur)	{
			echo "Erreur SELECT Rang: " . $erreur->getMessage() . "<br/>";
			$rangRowsBySpecialite = [];
		}

		$i = 1;
		foreach ($listeSpecialite as $specialite) {
			$rangRows = $rangRowsBySpecialite[$specialite] ?? [];
			$j = 0;
			foreach ($rangRows as $row) {
				extract($row);
				$rangCellule = $afficherDernierCesp ? $DernierCESP : $Dernier;
				$tableDernier[$j][0] = $CHU; 
				$tableDernierPrincipal[$j][0] = $CHU;
				$tableDernierCESP[$j][0] = $CHU;
				$tablePoste[$j][0] = $CHU;
				$tableCESP[$j][0] = $CHU; 
				$tableDernier[$j][$i] = $rangCellule;
				$tableDernierPrincipal[$j][$i] = $Dernier;
				$tableDernierCESP[$j][$i] = $DernierCESP;
				$tableRangCompare[$j][$i] = getRangComparaison($Dernier, $DernierCESP, $cesp);
				$tablePoste[$j][$i] = $Poste;
				$tableCESP[$j][$i] = $CESP;
				$j += 1;
			}
			$i += 1;
		}
		if ($debug) {
			var_dump($tableDernier);
			var_dump($tablePoste);
			var_dump($tableCESP);
		}

		$montant = new NumberFormatter("fr-FR", NumberFormatter::DECIMAL);
		$j = 0;
		foreach ($tableDernier as $CHU) {
			echo "<tr>";
			$i = 0;
			foreach ($CHU as $dernier) {

				// est-ce que la spécilaité dans ce CHU est accessible ?
				if ($cesp == "on") {
					if (($tableCESP[$j][$i] != null) and ($tableCESP[$j][$i] > 0 )) {
						$cespOk = true;
					} else {
						$cespOk = false;
					}
				} else {
					$cespOk = true;
				}

				if (($rang != "rangIndifferent") and ($rang != null) and ($rang != 0)) {
					if (($tableRangCompare[$j][$i] ?? 0) >= $rang) {
						$rangOk = true;
					} else {
						$rangOk = false;
					}
				} else {
					$rangOk = true;
				}

				if ((($rang == "rangIndifferent") or ($rang == null) or ($rang == 0)) and ($cesp != "on")) {
					$cespOk = false;
				}

				// affichage de chaque cellule
				if ($i > 0) {
					$code = $listeSpecialite[$i-1];
					$libelleSpecialite = getLibelleSpecialite($code);
				} else {
					$code = "";
					$libelleSpecialite = "";
				}
				if ($tableCESP[$j][$i] == "") {
					$libelleCESP = "0";
				} else {
					$libelleCESP = $tableCESP[$j][$i];
				}

				// cellule CHU
				if ($i == 0) {
					echo "<td>" . $dernier . "</td>";
				
				// cellule rang
				} else {
					$libelle = $anneePoste;
					$dernierPrincipalTooltip = intval($tableDernierPrincipal[$j][$i]) > 0
						? $montant->format($tableDernierPrincipal[$j][$i])
						: "0";
					$dernierCespTooltip = intval($tableDernierCESP[$j][$i]) > 0
						? $montant->format($tableDernierCESP[$j][$i])
						: "0";
					$tooltip = " data-toggle='tooltip' data-html='true' data-trigger='hover focus' title='" . escapeHtml($CHU[0]) . "<br/>" . escapeHtml($libelleSpecialite) . "<hr/>Dernier <small>en " . escapeHtml($anneeReference) . "</small> : " . escapeHtml($dernierPrincipalTooltip) . "<br/>Dernier CESP <small>en " . escapeHtml($anneeReference) . "</small> : " . escapeHtml($dernierCespTooltip) . "<br/>poste <small>en " . escapeHtml($libelle) . "</small> : " . escapeHtml($tablePoste[$j][$i]) . "<br/>postes CESP <small>en " . escapeHtml($libelle) . "</small> : " . escapeHtml($libelleCESP) . "' ";
					$zoom = "";
					$valeurCellule = intval($dernier) > 0
						? escapeHtml($montant->format($dernier))
						: "-";

					if (($cespOk) and ($rangOk)) {
						echo "<td style='background-color:pink;' " . $tooltip . $zoom . ">" . $valeurCellule . "</td>";					
					} else {
						echo "<td" . $tooltip . $zoom . ">" . $valeurCellule . "</td>";
					}
				}
				$i += 1;
			}
			echo "</tr>";
			$j += 1;
		}

		if ($debug) {
			var_dump($listeSpecialite);
		}
		echo "</tbody></table><br/>";
		echo "<p>";
  		if ((($rang != "rangIndifferent") and ($rang != null) and ($rang != 0)) or ($cesp == "on")) {
  			echo "En <span style='background-color:pink;'> &nbsp;rose </span>&nbsp; les Spécialités-CHU accessibles avec vos critères.<br/>";	
  		}
 		echo "Cliquer &nbsp;<i class='bi bi-cursor-fill' aria-hidden='true'></i>&nbsp; sur une <strong>spécialité</strong> dans l&apos;entête du tableau pour voir le détail des CHU pour cette spécialité.";

		echo "</p>";
	?>
	
	</div>
	</div>

	<!-- retour en arrière vers le formulaire -->
	<footer style='margin-top:40px; margin-bottom:80px;'>
		<br/>
		<p class=text-center>
			<button class="btn btn-primary" onclick="questionnaire()">&larr; Retour aux critères</button>
		</p>
	</footer>

	<?php
		// librairies javascript nécessaires à l'application (jquery + popper + bootstrap)
		include "php/librairie.php";
	?>
	
	<!-- activation tooltip bootstrap -->
	<script>
		$(function () {
			$('[data-toggle="tooltip"]').tooltip()
		})
	</script>
	
	<!-- navigation -->
	<script>
		
		// pour basculer sur l'affichage par liste
		function liste() {
			<?php
				if ($depuis == 'detail') {
					echo "window.location.href=" . json_encode(buildSafeUrl('detail-specialite-questionnaire.php', array_merge(['code' => $code, 'rang' => $rang, 'reference' => $reference, 'type' => $type, 'cesp' => $cesp, 'lieu' => $lieu, 'internat' => $internat, 'benefice' => $benefice, 'depuis' => 'detail'], getRangModeQueryParam($rangMode['mode']))) ) . ";";
				} else {
					echo "window.location.href=" . json_encode(buildSafeUrl('liste-specialite.php', array_merge(['code' => $code, 'rang' => $rang, 'reference' => $reference, 'type' => $type, 'cesp' => $cesp, 'lieu' => $lieu, 'internat' => $internat, 'benefice' => $benefice, 'depuis' => 'detail'], getRangModeQueryParam($rangMode['mode']))) ) . ";";
				}
			?>
		}
		
		// pour retourner en arrière sur le questionnaire
		function questionnaire() {
			<?php
				echo "window.location.href=" . json_encode(buildSafeUrl('questionnaire-choix-specialite.php', ['code' => $code, 'rang' => $rang, 'reference' => $reference, 'type' => $type, 'cesp' => $cesp, 'lieu' => $lieu, 'internat' => $internat, 'benefice' => $benefice])) . ";";
			?>
		}

		// pour zoomer sur une spécialité depuis une cellule d'entête
		function zoom(code) {
			$('[data-toggle="tooltip"]').tooltip('hide')
			<?php
				$baseZoomSpecialite = buildSafeUrl('detail-specialite-questionnaire.php', array_merge(['code' => $code, 'rang' => $rang, 'reference' => $reference, 'type' => $type, 'cesp' => $cesp, 'lieu' => $lieu, 'internat' => $internat, 'benefice' => $benefice, 'depuis' => 'tableau'], getRangModeQueryParam($rangMode['mode'])));
				echo "window.location.href=" . json_encode($baseZoomSpecialite) . " + '&code=' + encodeURIComponent(code);";
			?>
		}
	</script>
	
	<!-- gestion du symbole + et - -->
	<script>		
		$('#critere').on('show.bs.collapse', function () {
			$("#symbole").toggleClass('fa-plus-circle fa-minus-circle');
		})
		$('#critere').on('hide.bs.collapse', function () {
			$("#symbole").toggleClass('fa-minus-circle fa-plus-circle');
		})
	</script>
  </body>
</html>