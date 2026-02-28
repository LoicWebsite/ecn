<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Simulateur en ligne pour choisir une spécialité d'internat - nombre de postes par spécialité et CHU">

	<?php
		// favicons générés par https://realfavicongenerator.net
		include "php/favicon.php";
	
        // Google Analytics
		include "php/GoogleAnalytics.php";
	?>

    <title>Nombre de CESP pour l'internat</title>

	<?php
		// styles nécessaires à l'application (bootstrap + fontawasome + ECN)
		include "php/style.php";
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
				<li class="breadcrumb-item active" aria-current="page">CESP</li>
			  </ol>
			</div>
			<div class="col-sm">
			</div>
			<div class="col-xl">
			</div>
		</div>
	</nav>

	<div id="reponse" class="container-fluid">
    	<h1 class="h5" style="text-align:left; margin-top:20px;">
    		<a class="h5" data-toggle="collapse" aria-expanded="false" aria-controls="critere" href="#critere"><i id="symbole" class="bi bi-plus-circle-fill" aria-hidden="true"></i>&nbsp; Vos critères de choix...</a>
		</h1>
		
	<?php

		// fonctions communes et récupération-contrôle des paramètres
		require_once "php/controleParametre.php";
		require_once "php/fonctionECN.php";
	
		// ouverture de la base de données
		$db = openDatabase();

		// pour ce tableau CESP onforce la pramètre ceps à "on"
		$saveCESP = $cesp;
		$cesp = "on";

		// affichage des critères
		echo "<div id='critere' class='collapse'>";
		echo "<div class='row'>";
		echo "<div class='col-md-5 offset-md-1'>";
			echo "<ul>";
			echo "<li>rang visé ou obtenu = <span class='critere'>" . getLibelleRang($rang) . "</span></li>";
			echo "<li>année de référence = <span class='critere'>" . getLibelleReference($reference) . "</span> ";
			echo "<li>type de spécialité = <span class='critere'>" . getLibelleType($type) . "</span></li>";
			echo "<li>CESP uniquement = <span class='critere'>" . getLibelleCESP($cesp) . "</span>";
			echo " <i class='bi bi-info-circle-fill' data-toggle='tooltip' data-html='true' title='Seules les spécialités avec CESP sont affichées dans ce tableau.'> </i></li>";
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
		$tableUrl = array(array());
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

		$libelleCesp = "CESP2025";					// les postes et cesp sont absents en base pour les années avant 2020 (on prend ceux de 2025 par défaut)
		if ($reference == "2024") {
			$libelleCesp = "CESP2024";
		} elseif ($reference == "2023") {
			$libelleCesp = "CESP2023";
		} elseif ($reference == "2022") {
			$libelleCesp = "CESP2022";
		} elseif ($reference == "2021") {
			$libelleCesp = "CESP2021";
		} elseif ($reference == "2020") {
			$libelleCesp = "CESP2020";
		}
		if ($cesp == "on") {
			$where = $where . " AND Rang." . $libelleCesp . " <> '0' AND Rang." . $libelleCesp . " <> ''";
		}

		// on prend le rang de l'année en référence
		if (($rang <> "") and ($rang > 0) and ($rang <> "rangIndifferent")) {
			$where = $where . " AND Rang.Dernier" . $reference . " >= '" . $rang ."'";
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

		// préparation de la requête pour afficher les spécialités

		$libellePoste = "Poste2025";
		$libelleCesp = "CESP2025";			// avant 2020 le nombre de postes et de CESP n'est pas en base (on prend 2025 par défaut)
		if ($reference == "2024") {
			$libellePoste = "Poste2024";
			$libelleCesp = "CESP2024";
		} elseif ($reference == 2023) {
			$libellePoste = "Poste2023";
			$libelleCesp = "CESP2023";
		} elseif ($reference == 2022) {
			$libellePoste = "Poste2022";
			$libelleCesp = "CESP2022";
		} elseif ($reference == 2021) {
			$libellePoste = "Poste2021";
			$libelleCesp = "CESP2021";
		} elseif ($reference == 2020) {
			$libellePoste = "Poste2020";
			$libelleCesp = "CESP2020";
		}
		
		$sql = "SELECT	Rang.CodeSpecialite as CodeSpecialite,
						sum(Rang." . $libellePoste . ") as Poste,
						sum(Rang." . $libelleCesp . ") as CESP
				FROM `Specialite` inner join Rang on Specialite.CodeSpecialite = Rang.CodeSpecialite " . $where . " GROUP BY Rang.CodeSpecialite;";

		if ($debug) echo "SQL = " . $sql ."<br/>";
 		
		// exécution de la requête
		try {
			$result = $db->query($sql);
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
 			if ($rang <> "rangIndifferent") {
				echo "pour le rang " . getLibelleRang($rang) . " en " . $reference;
			} 
 		} elseif ($cesp == "on") {
 			echo " en CESP ";
  		}
		echo "</h2><br/>";		
  		
 		// tableau
		echo "<table class='table-hover table-bordered' style='width:100%;'>";
		echo "<thead><tr>";
		echo "<th>Nombre de CESP " . $reference . "<br/><i class='fbi bi-info-circle-fill' data-toggle='tooltip' data-html='true' title='Le nombre de CESP pour l&apos;internat est issu de l&apos;arrêté publié par le Journal Officiel.<br/>L&apos;année correspond à l&apos;année de publication au Journal Officiel.'></i></th>";

		$i = 0;
		foreach ($listeSpecialite as $specialite) {
			$libelleSpecialite = getLibelleSpecialite($specialite);
			if ($listeCESP[$i] == "") {
				$libelleCESP = "0";
			} else {
				$libelleCESP = $listeCESP[$i];
			}
			if ($reference < 2020) {
				$libelle = "2025";
			} else {	
				$libelle = $reference;
			}
			$tooltip = " data-toggle='tooltip' data-html='true' title='" . $libelleSpecialite . "<hr>poste <small>en " . $libelle . "</small> : " . $listePoste[$i] . "<br/>CESP <small>en " . $libelle . "</small> : " . $libelleCESP . "' ";
			$href = "";
			echo "<th " . $tooltip . $href . " >&nbsp;" . $specialite . "&nbsp;</th>";
			$i += 1;
		}
		echo "<th>&nbsp;Total&nbsp; CHU</th>";
		echo "<th style='padding-right:600px; background-color:white; border-style:hidden;'>&nbsp;</th></tr></thead><tbody>\n";

		// parcours de la table des spécialités pour recherche les CHU dans la table Rang
		$i = 1;
		foreach ($listeSpecialite as $specialite) {

			// préparation de la requête pour la table Rang
			$sql = "SELECT
						Rang.CodeSpecialite,
						Rang.CHU,
						Rang.Dernier2025,
						Rang.Dernier2024,
						Rang.Dernier2023,
						Rang.Dernier2022,
						Rang.Dernier2021,
						Rang.Dernier2020,
						Rang.Dernier2019,
						Rang.Dernier2018,
						Rang.Dernier2017,
						Rang.Poste2025,
						Rang.Poste2024,
						Rang.Poste2023,
						Rang.Poste2022,
						Rang.Poste2021,
						Rang.Poste2020,
						Rang.URLCeline,
						Rang.CESP2025,
						Rang.CESP2024,
						Rang.CESP2023,
						Rang.CESP2022,
						Rang.CESP2021,
						Rang.CESP2020
					FROM Rang
					WHERE Rang.CodeSpecialite = '" . $specialite . "';";
			if ($debug) echo "SQL = " . $sql ."<br/>";

			// execution de la requête sur Rang
			try {
				$result = $db->query($sql);
				$j = 0;
				while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
					extract($row);
					$tableDernier[$j][0] = $CHU; 
					$tablePoste[$j][0] = $CHU;
					$tableCESP[$j][0] = $CHU; 
					if ($reference == "2025") {
						$tableDernier[$j][$i] = $Dernier2025;
						$tablePoste[$j][$i] = $Poste2025;
						$tableCESP[$j][$i] = $CESP2025;
					} elseif ($reference == "2024") {
						$tableDernier[$j][$i] = $Dernier2024;
						$tablePoste[$j][$i] = $Poste2024;
						$tableCESP[$j][$i] = $CESP2024;
					} elseif ($reference == "2023") {
						$tableDernier[$j][$i] = $Dernier2023;
						$tablePoste[$j][$i] = $Poste2023;
						$tableCESP[$j][$i] = $CESP2023;
					} elseif ($reference == "2022") {
						$tableDernier[$j][$i] = $Dernier2022;
						$tablePoste[$j][$i] = $Poste2022;
						$tableCESP[$j][$i] = $CESP2022;
					} elseif ($reference == "2021") {
						$tableDernier[$j][$i] = $Dernier2021;
						$tablePoste[$j][$i] = $Poste2021;
						$tableCESP[$j][$i] = $CESP2021;
					} elseif ($reference == "2020") {
						$tableDernier[$j][$i] = $Dernier2020;
						$tablePoste[$j][$i] = $Poste2020;
						$tableCESP[$j][$i] = $CESP2020;
					} elseif ($reference == "2019") {
						$tableDernier[$j][$i] = $Dernier2019;
						$tablePoste[$j][$i] = $Poste2025;
						$tableCESP[$j][$i] = $CESP2025;
					} elseif ($reference == "2018") {
						$tableDernier[$j][$i] = $Dernier2018;
						$tablePoste[$j][$i] = $Poste2025;
						$tableCESP[$j][$i] = $CESP2025;
					} elseif ($reference == "2017") {
						$tableDernier[$j][$i] = $Dernier2017;
						$tablePoste[$j][$i] = $Poste2025;
						$tableCESP[$j][$i] = $CESP2025;
					}
					$tableUrl[$j][$i] = $URLCeline;
					$j += 1;
				}
			}
			catch(PDOException $erreur)	{
				echo "Erreur SELECT Rang: " . $erreur->getMessage() . "<br/>";
			}
			$i += 1;
		}
		if ($debug) {
			var_dump($tableDernier);
			var_dump($tablePoste);
			var_dump($tableCESP);
			var_dump($tableUrl);
		}

		$montant = new NumberFormatter("fr-FR", NumberFormatter::DECIMAL);
		$j = 0;

		// tableau pour stocker les totaux par spécialité
		$totauxColonnes = [];
		$grandTotal = 0; // total général

		// parcours de la table des CHU
		foreach ($tableDernier as $CHU) {
			echo "<tr>";
			$i = 0;
			$totalCHU = 0;
			
			// parcours des spécialités pour chaque CHU
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
					if ($dernier >= $rang) {
						$rangOk = true;
					} else {
						$rangOk = false;
					}
				} else {
					$rangOk = false;
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
					echo "<td><strong>" . $dernier . "</strong></td>";
				
				// cellule rang
				} else {
					if ($reference < 2020) {
						$libelle = "2025";
					} else {	
						$libelle = $reference;
					}
					$tooltip = " data-toggle='tooltip' data-html='true' data-trigger='hover focus' title='".$CHU[0]."<br/>".$libelleSpecialite."<hr/>Dernier <small>en ".$reference."</small> : ".$dernier."<br>poste <small>en " . $libelle . "</small> : ".$tablePoste[$j][$i]."<br/>CESP <small>en " . $libelle . "</small> : ".$libelleCESP."' ";
					
					if ($tableCESP[$j][$i] == 0) {
						$libelleNbCesp = "";
					} else {
						$libelleNbCesp = $montant->format($tableCESP[$j][$i]);
					}
					if (($rangOk) and ($tableCESP[$j][$i]<>0) and ($tableCESP[$j][$i]<>"")) {
						echo "<td style='background-color:pink;' " . $tooltip . ">" . $libelleNbCesp . "</td>";					
					} else {
						echo "<td" . $tooltip . ">" . $libelleNbCesp . "</td>";
					}
				}
	
				// cumule les totaux par spécialité
				if (!isset($totauxColonnes[$i])) {
					$totauxColonnes[$i] = 0;
				}
				$totauxColonnes[$i] += intval($tableCESP[$j][$i]);
	
				$totalCHU += intval($tableCESP[$j][$i]);
				$grandTotal += intval($tableCESP[$j][$i]);

				$i += 1;
			}
			echo "<td style='background:#eee;'>&nbsp;<strong>" . $totalCHU . "</strong>&nbsp;</td>";
			echo "</tr>";
			$j += 1;
		}

		// ligne des totaux par spécialité
		echo "<tr style='background:#eee; font-weight:bold;'>";
		echo "<td style='background:#eee;'>Total spécialité</td>";
		for ($i = 1; $i < count($totauxColonnes); $i++) {
    		echo "<td>" . $montant->format($totauxColonnes[$i]) . "</td>";
		}
		
		// affiche le grand total général en dernière cellule
		echo "<td>" . $montant->format($grandTotal) . "</td>";
		echo "</tr>";

		if ($debug) {
			var_dump($listeSpecialite);
		}
		echo "</tbody></table><br/>";
		echo "<p>";
  		if ((($rang != "rangIndifferent") and ($rang != null) and ($rang != 0))) {
  			echo "En <span style='background-color:pink;'> &nbsp;rose </span>&nbsp; les Spécialités-CHU accessibles avec vos critères.<br/>";	
  		}

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
					echo "window.location.href='detail-specialite-questionnaire.php?code=" . $code . "&rang=" . $rang . "&reference=" . $reference . "&type=" . $type. "&cesp=" . $saveCESP . "&lieu=" . $lieu . "&internat=" . $internat . "&benefice=" . $benefice . "&depuis=detail';";
				} else {
					echo "window.location.href='liste-specialite.php?code=" . $code . "&rang=" . $rang . "&reference=" . $reference . "&type=" . $type. "&cesp=" . $saveCESP . "&lieu=" . $lieu . "&internat=" . $internat . "&benefice=" . $benefice . "&depuis=detail';";
				}
			?>
		}
		
		// pour retourner en arrière sur le questionnaire
		function questionnaire() {
			<?php
				echo "window.location.href='questionnaire-choix-specialite.php?code=" . $code . "&rang=" . $rang . "&reference=" . $reference . "&type=" . $type. "&cesp=" . $saveCESP . "&lieu=" . $lieu . "&internat=" . $internat . "&benefice=" . $benefice . "';";
			?>
		}

		// pour voir le détail Celine d'une cellule si l'année de référence est 2023
		function celine(urlCeline) {
			$('[data-toggle="tooltip"]').tooltip('hide')
			<?php
				if ($reference == "2023") {
					echo "if (urlCeline != '') {";
					echo "	window.open(urlCeline,'Détail Céline');";
					echo "}";
				}
			?>
		}

		// pour zoomer sur une spécialité depuis une cellule d'entête - pas activé pour l'instant
		function zoom(code) {
			$('[data-toggle="tooltip"]').tooltip('hide')
			<?php
				$href = "'detail-specialite-questionnaire.php?code=" . $code . "&rang=" . $rang . "&reference=" . $reference . "&code=' + code + '" . "&type=" . $type. "&cesp=" . $saveCESP . "&lieu=" . $lieu . "&internat=" . $internat . "&benefice=" . $benefice . "&depuis=tableau'";
//				echo "window.open(" . $href . ", '_self')";
				echo "window.location.href=" . $href . ";";
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