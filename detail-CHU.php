<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Simulateur en ligne pour choisir une spécialité d'internat - Litse des spécialités pour un CHU">

	<?php
		// favicons générés par https://realfavicongenerator.net
		include "php/favicon.php";
	
        // Google Analytics
		include "php/GoogleAnalytics.php";
	?>
    
	<title>Description de la spécialité sélectionnée</title>

	<?php
		// styles nécessaires à l'application (bootstrap + fontawasome + ECN)
		include "php/style.php";

		// Balise canonique pour éviter les doublons dus aux paramètres d'URL
		include "php/canonical.php";
	?>
	
	<style>
		th {
			position:sticky;
			top: 50px;
			text-align:center;
			z-index:1;
		}
		td {
			border-left-style:dotted;
			border-right-style:dotted;
			cursor: default;
		}
	</style>

  </head>
  <body id="hautdepage" data-spy="scroll" data-target="#navigation" data-offset="0">

	<?php
		include "php/menu-questionnaire.php";
		require_once "php/controleParametre.php";
		require_once "php/fonctionECN.php";
	
		// ouverture de la base de données
		$db = openDatabase();
	?>
	
	<!-- chemin de navigation -->
	<nav id="chemin">
		<div class="row" style='margin-top:80px;'>
			<div class="col-sm" aria-label="breadcrumb">
			  <ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="choix-specialite-chu-celine-ecn.php"><i class="bi bi-house-door-fill"></i></a></li>
				<li class="breadcrumb-item"><a href="#" onclick="questionnaire()">Critère</a></li>
				<li class='breadcrumb-item'><a href='#' onclick='liste()'>CHU</a></li>
				<li class="breadcrumb-item active" aria-current="page">Spécialité</li>
			  </ol>
			</div>
			<div class="col-sm">
				 
			</div>
			<div class="col-xl">
			</div>
		</div>
	</nav>
		
	<!-- affichage du détail d'un CHU -->		
	<div id="CHU" class="container">
    	<h1 class="h5" style="text-align:left; margin-top:20px;">
    		<a class="h5" data-toggle="collapse" aria-expanded="false" aria-controls="critere" href="#critere"><i id="symbole" class="bi bi-plus-circle-fill"></i>&nbsp; Vos critères de choix...</a>
		</h1>
		
	<?php

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

		// préparation de la clause where pour sélectionner les spécialités en fonction des critères

		$rangMode = getRangDisplayMode($db, $reference);
		$anneeReference = $rangMode['year'];
		$anneePoste = getRangDisplayPosteYear($db, $reference);
		$rangCespDisponible = $rangMode['available'];
		$afficherDernierCesp = ($rangMode['mode'] === 'cesp');
		$rangFilterExpression = getRangFilterExpression($cesp);
		$where = " WHERE Specialite.Type <> '' AND COALESCE(rangPoste.Poste, 0) > 0 ";

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
			$where = $where . " AND COALESCE(rangPoste.CESP, 0) <> 0";
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

		if (($rang <> "") and ($rang > 0) and ($rang <> "rangIndifferent")) {
			$whereSpecialite = $where . " AND COALESCE(" . $rangFilterExpression . ", 0) >= '" . $rang ."'";
		} else {
			$whereSpecialite = $where;
		}

		// requête pour compter le nombres de postes et de CESP
		$nbPoste = 0;
		$nbCESP = 0;

		$sql = "SELECT rangPoste.CHU,
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
			" . $whereSpecialite . " AND rangPoste.CHU=:chu;";
		if ($debug) echo "SQL POSTE = " . $sql ."<br/>";
		try {
			$stmt = $db->prepare($sql);
			$stmt->execute([':anneePoste' => $anneePoste, ':anneeReference' => $anneeReference, ':chu' => $chu]);
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


		// préparation de la requête pour recherche les CHU correspondant aux spécialités
		$montant = new NumberFormatter("fr-FR", NumberFormatter::DECIMAL);
		$sql = "SELECT rangPoste.CodeSpecialite AS CodeSpecialite,
					COALESCE(rangDernier.Dernier, 0) AS DernierRef,
					COALESCE(rangDernier.DernierCESP, 0) AS DernierCESPRef,
					COALESCE(rangPoste.Poste, 0) AS PosteRef,
					COALESCE(rangPoste.CESP, 0) AS CESPRef
				FROM Rang rangPoste
				INNER JOIN Specialite ON Specialite.CodeSpecialite = rangPoste.CodeSpecialite
				LEFT JOIN Rang rangDernier
					ON rangDernier.CodeSpecialite = rangPoste.CodeSpecialite
					AND rangDernier.CHU = rangPoste.CHU
					AND rangDernier.Annee = :anneeReference
				WHERE rangPoste.Annee = :anneePoste
					AND rangPoste.CHU = :chu
					AND COALESCE(rangPoste.Poste, 0) > 0
					" . preg_replace('/^ WHERE /', ' AND ', $whereSpecialite) . ";";
		if ($debug) echo "SQL = " . $sql ."<br/>";

		// exécution de la requête
		try {
			$stmt = $db->prepare($sql);
			$stmt->execute([':anneePoste' => $anneePoste, ':anneeReference' => $anneeReference, ':chu' => $chu]);
			$result = $stmt;

			// titre
			echo "<br/><h2 class='h5' style='text-align:left'>" . $result->rowCount() ." spécialités correspondent à vos critères ";
			if ($cesp != "off") {
				echo " en CESP ";
			}
			if (($rang != 0) and ($rang != null) and ($rang != "rangIndifferent")) {
				echo "pour le rang " . getLibelleRang($rang) . " en " . $reference;
			}
			echo " à " . $chu . "</h2><br/>";
			if ($rangCespDisponible) {
				$paramsModeRang = ['code' => $code, 'chu' => $chu, 'rang' => $rang, 'reference' => $reference, 'type' => $type, 'cesp' => $cesp, 'lieu' => $lieu, 'internat' => $internat, 'benefice' => $benefice, 'depuis' => $depuis];
				echo renderRangModeSelector($paramsModeRang, $rangMode['mode']);
			}

			// liste
			echo "<table class='table-hover' style='width:100%;'>";
			echo "<thead class='text-center'>";
			echo "<tr><th colspan=2 style='width:50%'>" . $result->rowCount() ." Spécialités à " . $chu . "</th>";
			$libelle = $anneePoste;
			echo "<th style='width:20%;'> ".$montant->format($nbPoste)." postes " . $libelle . "<br/><i class='bi bi-info-circle-fill' data-toggle='tooltip' data-html='true' title='Le nombre de postes est issu de l&apos;arrêté publié par le Journal Officiel. Ce nombre de postes exclut les CESP. <br/>L&apos;année correspond à l&apos;année de publication au Journal Officiel.'></i></th>";
				$infoTooltipRang = "À partir de 2024, il s&apos;agit du rang limite par groupe de spécialités.<br>Auparavant c&apos;était le rang limite national par spécialité.";
				if ($rangCespDisponible) {
					$infoTooltipRang .= "<br/>" . getRangModeTooltipDescription();
				}
				$libelleEnteteRang = $afficherDernierCesp ? "Rang dernier CESP " : "Rang dernier ";
				echo "<th style='width:20%'>" . $libelleEnteteRang . $anneeReference . "<br/><i class='bi bi-info-circle-fill' data-toggle='tooltip' data-html='true' title='" . $infoTooltipRang . "'></i></th>";
			echo "<th style='width:10%;'> ".$montant->format($nbCESP)." CESP " . $libelle . " <br/><i class='bi bi-info-circle-fill' data-toggle='tooltip' data-html='true' title='Le nombre de postes réservés aux CESP est issu de l&apos;arrêté publié par le Journal Officiel.<br/>Une cellule vide signifie qu&apos;il n&apos;y a pas de poste CESP pour cette spécialité.'></i></th>";
			echo "</tr></thead>";
			echo "<tbody>";

			// récupération des données à afficher
			while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
				extract($row);
				echo "<tr>";
				echo "<td class='acronyme'>" . $CodeSpecialite . "</td>";
				echo "<td>" . getLibelleSpecialite($CodeSpecialite) . "</td>";
				$dernierPrincipal = intval($DernierRef);
				$dernierCesp = intval($DernierCESPRef);
				$dernierValeurAffichee = $afficherDernierCesp ? $dernierCesp : $dernierPrincipal;
				$dernier = $dernierValeurAffichee > 0 ? $montant->format($dernierValeurAffichee) : "-";
				$tooltipRangs = " data-toggle='tooltip' data-html='true' title='Dernier " . escapeHtml($anneeReference) . " : " . escapeHtml($dernierPrincipal > 0 ? $montant->format($dernierPrincipal) : "0") . "<br/>Dernier CESP " . escapeHtml($anneeReference) . " : " . escapeHtml($dernierCesp > 0 ? $montant->format($dernierCesp) : "0") . "'";
				$libellePoste = $PosteRef;
				$libelleCesp = $CESPRef;
				$poste = $montant->format($libellePoste);
				echo "<td class='text-center'>" . $poste . "</td>";
				echo "<td class='text-center'" . $tooltipRangs . ">" . $dernier . "</td>";
				if ($libelleCesp <> 0) {
					$nbCesp = $montant->format($libelleCesp);
				} else {
					$nbCesp = '';
				}
				echo "<td class='derniereColonne text-center'>" . $nbCesp . "</td>";
				echo "<td class='milieu'></td>";
				echo "</tr>";
			}
			echo "<tr><td colspan=5 style='border-top-style:solid; border-left-style:hidden; border-right-style:hidden; border-bottom-style:hidden;'></td></tr>";
			echo "</tbody>";
			echo "</table>";
		}
		catch(PDOException $erreur)	{
			echo "Erreur SELECT Specialite : " . $erreur->getMessage();
		}		
	?>
	</div>

	<!-- retour en arrière vers le formulaire -->
	<footer style='margin-top:40px; margin-bottom:80px;'>
		<br/>
		<p class=text-center>
			<button class='btn btn-primary' onclick='liste()'>&larr;&nbsp; Retour aux CHU</button>
		</p>
		<p class=text-center>
			<button class='btn btn-primary' onclick='questionnaire()'>&#10072;&larr;&nbsp; Retour aux critères</button>
		</p>
	</footer>
	<?php
		// jQuery et Bootstrap sont nécessaires au menu hamburger, aux tooltips et aux accordéons.
		include "php/librairie.php";
	?>

	<script>
		$(function () {
			$('[data-toggle="tooltip"]').tooltip()
		})
	</script>
	
	<!-- navigation -->
	<script>

		// pour aller au détail format carte
		function carte() {
			<?php
				echo "window.location.href=" . json_encode(buildSafeUrl('carte-chu.php', array_merge(['page' => 'poste', 'code' => $code, 'rang' => $rang, 'reference' => $reference, 'type' => $type, 'cesp' => $cesp, 'lieu' => $lieu, 'internat' => $internat, 'benefice' => $benefice, 'depuis' => $depuis], getRangModeQueryParam($rangMode['mode']))) ) . ";";
			?>
		}

		// pour retourner à la liste des résultats
		function liste() {
			<?php
				echo "window.location.href=" . json_encode(buildSafeUrl('liste-CHU.php', array_merge(['code' => $code, 'rang' => $rang, 'reference' => $reference, 'type' => $type, 'cesp' => $cesp, 'lieu' => $lieu, 'internat' => $internat, 'benefice' => $benefice], getRangModeQueryParam($rangMode['mode']))) ) . ";";
			?>
		}

		// pour retourner au questionnaire
		function questionnaire() {
			<?php
				echo "window.location.href=" . json_encode(buildSafeUrl('questionnaire-choix-specialite.php', ['code' => $code, 'rang' => $rang, 'reference' => $reference, 'type' => $type, 'cesp' => $cesp, 'lieu' => $lieu, 'internat' => $internat, 'benefice' => $benefice])) . ";";
			?>
		}
		
	</script>

	<!-- gestion du symbole + et - -->
	<script>		
		$('#detail').on('show.bs.collapse', function () {
			$("#symbole").toggleClass('fa-plus-circle fa-minus-circle');
		})
		$('#detail').on('hide.bs.collapse', function () {
			$("#symbole").toggleClass('fa-minus-circle fa-plus-circle');
		})
	</script>

  </body>
</html>