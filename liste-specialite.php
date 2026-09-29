<!doctype html>

	<?php

	// récupération-contrôle des paramètres
		include "php/controleParametre.php";
	
	// UTILE UNIQUEMENT PENDANT LA PHASE DE CHOIX DE POSTE (A ACTIVER DANS LES AUTRES PAGES)
	// aiguillage vers la page des rangs limites CELINE si c'est elle qui est demandée (par le questionnaire)
		if (isset($_GET['rangLimite'])) {
			$location="rang-limite.php?rang=".$rang."&reference=".$reference."&type=".$type."&lieu=".$lieu."&internat=".$internat."&benefice=".$benefice;
			header("Location: $location");
			exit;
		} elseif (isset($_GET['CHU'])) {
			$location="liste-CHU.php?rang=".$rang."&reference=".$reference."&cesp=".$cesp."&type=".$type."&lieu=".$lieu."&internat=".$internat."&benefice=".$benefice;
			header("Location: $location");
			exit;
		} elseif (isset($_GET['poste'])) {
			$location="tableau-poste.php?rang=".$rang."&reference=".$reference."&cesp=".$cesp."&type=".$type."&lieu=".$lieu."&internat=".$internat."&benefice=".$benefice;
			header("Location: $location");
			exit;
		} elseif (isset($_GET['CESP'])) {
			$location="tableau-cesp.php?rang=".$rang."&reference=".$reference."&cesp=".$cesp."&type=".$type."&lieu=".$lieu."&internat=".$internat."&benefice=".$benefice;
			header("Location: $location");
			exit;
		}
	?>

<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Simulateur en ligne pour choisir une spécialité d'internat - liste des spécialités médicales ou chirurgicales">

	<?php
		// favicons générés par https://realfavicongenerator.net
		include "php/favicon.php";
	
        // Google Analytics
		include "php/GoogleAnalytics.php";

		// Balise canonique pour éviter les doublons dus aux paramètres d'URL
		include "php/canonical.php";
	?>

    <title>Liste des spécialités correspondantes aux critères saisis</title>

	<?php
		// styles nécessaires à l'application (bootstrap + fontawasome + ECN)
		include "php/style.php";
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
		.critere {
			color:navy;
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
					<button class="btn btn-secondary btn-sm" onclick="" title="Affichage des spécialités en liste" disabled> en liste &nbsp; <i class="bi bi-list-ul"></i></button>
					&nbsp;&nbsp;&nbsp;<button class="btn btn-primary btn-sm" onclick="tableau()" title="Affichage des spécialités en tableau"> en tableau &nbsp; <i class="bi bi-table"></i></button>
				</p>
			</div>
			<div class="col-xl">
			</div>
		</div>
	</nav>

	<div id="reponse" class="container">
    	<h1 class="h5" style="text-align:left; margin-top:20px;">
    		<a class="h5" data-toggle="collapse" aria-expanded="false" aria-controls="critere" href="#critere"><i id="symbole" class="bi bi-plus-circle-fill"></i>&nbsp; Vos critères de choix...</a>
		</h1>
		
	<?php

		// fonctions communes
		require_once "php/fonctionECN.php";

		// connexion à la base de données
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
	
		// préparation de la clause where pour sélectionner les spécialités en fonction des critères
		$rangMode = getRangDisplayMode($db, $reference);
		$anneeReference = $rangMode['year'];
		$anneePoste = getRangDisplayPosteYear($db, $reference);
		$rangCespDisponible = $rangMode['available'];
		$afficherDernierCesp = ($rangMode['mode'] === 'cesp');
		$rangFilterExpression = getRangFilterExpression($cesp);
		$where = " WHERE Specialite.Type <> '' AND COALESCE(rangPoste.Poste, 0) > 0";

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

//		$where = $where . ";";

		// Agrégation par spécialité : la jointure conserve la correspondance
		// CodeSpecialite/CHU entre les postes, CESP et le dernier rang.
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
			" . $whereSpecialite . " GROUP BY Specialite.CodeSpecialite;";
		if ($debug) echo "SQL = " . $sql ."<br/>";

		// exécution de la requête
		try {
			$stmt = $db->prepare($sql);
			$stmt->execute([':anneePoste' => $anneePoste, ':anneeReference' => $anneeReference]);
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
		
		// préparation de la requête pour afficher les spécialités
		$sql = "SELECT rangPoste.CodeSpecialite AS Specialite,
					MAX(COALESCE(rangDernier.Dernier, 0)) AS DernierRef,
					MAX(COALESCE(rangDernier.DernierCESP, 0)) AS DernierCESPRef,
					SUM(COALESCE(rangPoste.Poste, 0)) AS PosteRef,
					SUM(COALESCE(rangPoste.CESP, 0)) AS CESPRef
				FROM Rang rangPoste
				INNER JOIN Specialite ON Specialite.CodeSpecialite = rangPoste.CodeSpecialite
				LEFT JOIN Rang rangDernier
					ON rangDernier.CodeSpecialite = rangPoste.CodeSpecialite
					AND rangDernier.CHU = rangPoste.CHU
					AND rangDernier.Annee = :anneeReference
				WHERE rangPoste.Annee = :anneePoste
					AND COALESCE(rangPoste.Poste, 0) > 0
					" . preg_replace('/^ WHERE /', ' AND ', $whereSpecialite) . "
				GROUP BY rangPoste.CodeSpecialite;";
		if ($debug) echo "SQL = " . $sql ."<br/>";

		// exécution de la requête
		try {
			$stmt = $db->prepare($sql);
			$stmt->execute([':anneePoste' => $anneePoste, ':anneeReference' => $anneeReference]);
			$result = $stmt;
			$montant = new NumberFormatter("fr-FR", NumberFormatter::DECIMAL);

			// titre
			echo "<br/><h2 class='h5' style='text-align:left'>" . $result->rowCount() ." spécialités correspondent à vos critères ";
			if ($cesp != "off") {
				echo " en CESP ";
			}
			if (($rang != 0) and ($rang != null) and ($rang != "rangIndifferent")) {
				echo "pour le rang " . getLibelleRang($rang) . " en " . $reference;
			}
			echo "</h2><br/>";

			if ($rangCespDisponible) {
				$paramsModeRang = [
					'code' => $code,
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

			// liste
			echo "<table class='table-hover' style='width:100%;'>";
			echo "<caption>Cliquer &nbsp;<i class='bi bi-cursor-fill'></i>&nbsp; sur une spécialité pour voir les CHU pour cette spécialité.</caption>";
			echo "<thead class='text-center'>";
			echo "<tr><th colspan=2 style='width:50%'>" . $result->rowCount() ." spécialités d'internat<br/><i class='bi bi-info-circle-fill' data-toggle='tooltip' data-html='true' title='Cliquer sur une spécialité pour voir les CHU pour cette spécialité.'></i></th>";
			$libelle = $anneePoste;
			echo "<th style='width:20%;'> ".$montant->format($nbPoste)." postes " . $libelle . "<br/><i class='bi bi-info-circle-fill' data-toggle='tooltip' data-html='true' title='Le nombre de postes est issu de l&apos;arrêté publié par le Journal Officiel. Ce nombre de postes exclut les CESP.<br/>L&apos;année correspond à l&apos;année de publication au Journal Officiel.'></i></th>";
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
				echo "<tr onclick='zoom(" . json_encode($Specialite) . ")'>";
				$libelleSpecialite = getLibelleSpecialite($Specialite);

				// en local les caractères accentués passent, mais pas sur le serveur Gandi
				// Vérifie si l'encodage est UTF-8, sinon convertis depuis ISO-8859-1 (Latin-1)
				if (!mb_detect_encoding($libelleSpecialite, 'UTF-8', true)) {
					$libelleSpecialite = mb_convert_encoding($libelleSpecialite, 'UTF-8', 'ISO-8859-1');
				}

				echo "<td class='acronyme'>" . escapeHtml($Specialite) . "</td><td>" . escapeHtml($libelleSpecialite) . "</td>";
				$rangAffiche = $afficherDernierCesp ? $DernierCESPRef : $DernierRef;
				$dernier = intval($rangAffiche) > 0 ? $montant->format($rangAffiche) : "-";
				$poste = $PosteRef;
				$libelleCesp = $CESPRef;
				echo "<td class='text-center'>".$montant->format($poste)."</td>";
				$dernierPrincipalTooltip = intval($DernierRef) > 0 ? $montant->format($DernierRef) : "0";
				$dernierCespTooltip = intval($DernierCESPRef) > 0 ? $montant->format($DernierCESPRef) : "0";
				$tooltipRangs = " data-toggle='tooltip' data-html='true' title='Dernier " . escapeHtml($anneeReference) . " : " . escapeHtml($dernierPrincipalTooltip) . "<br/>Dernier CESP " . escapeHtml($anneeReference) . " : " . escapeHtml($dernierCespTooltip) . "<br/>Postes " . escapeHtml($anneePoste) . " : " . escapeHtml($montant->format($poste)) . "<br/>Postes CESP " . escapeHtml($anneePoste) . " : " . escapeHtml($montant->format($libelleCesp)) . "'";
				echo "<td class='text-center'" . $tooltipRangs . ">" . $dernier . "</td>";
				if ($libelleCesp <> 0) {
					$nbCesp = $montant->format($libelleCesp);
				} else {
					$nbCesp = '';
				}
				echo "<td class='derniereColonne text-center'>".$nbCesp."</td>";
				echo "<td class='milieu'></td>";
				echo "</tr>\n";
			}
			echo "<tr><td colspan=5 style='border-top-style:solid; border-left-style:hidden; border-right-style:hidden; border-bottom-style:hidden;'></td></tr>";
			echo "</tbody>";
			echo "</table>";
		}
		catch(PDOException $erreur)	{
			echo "Erreur SELECT Specialite : " . $erreur->getMessage();
		}

		// fermeture de la base
		if (isset($result)) {$result->closeCursor();}
		$db = null;
	
	?>

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
	
	<!-- activation tooltip -->
	<script>
		$(function () {
			$('[data-toggle="tooltip"]').tooltip()
		})
	</script>
	
	<!-- navigation -->
	<script>
		//pour basculer sur l'affichage en tableau
		function tableau() {
			<?php
				echo "window.location.href=" . json_encode(buildSafeUrl('tableau-specialite.php', array_merge(['code' => $code, 'rang' => $rang, 'reference' => $reference, 'type' => $type, 'cesp' => $cesp, 'lieu' => $lieu, 'internat' => $internat, 'benefice' => $benefice, 'depuis' => 'tableau'], getRangModeQueryParam($rangMode['mode'])))) . ";";
			?>
		}

		// pour retourner en arrière dans l'historique du navigateur
		function questionnaire() {
			<?php
				echo "window.location.href=" . json_encode(buildSafeUrl('questionnaire-choix-specialite.php', ['code' => $code, 'rang' => $rang, 'reference' => $reference, 'type' => $type, 'cesp' => $cesp, 'lieu' => $lieu, 'internat' => $internat, 'benefice' => $benefice])) . ";";
			?>
		}

		// pour zoomer sur une spécialité
		function zoom(code) {
			<?php
				$baseDetailSpecialite = buildSafeUrl('detail-specialite-questionnaire.php', array_merge(['rang' => $rang, 'reference' => $reference, 'type' => $type, 'cesp' => $cesp, 'lieu' => $lieu, 'internat' => $internat, 'benefice' => $benefice, 'depuis' => 'liste'], getRangModeQueryParam($rangMode['mode'])));
				echo "window.location.href=" . json_encode($baseDetailSpecialite) . " + '&code=' + encodeURIComponent(code);";
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