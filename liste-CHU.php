<!doctype html>

	<?php

	// récupération-contrôle des paramètres
		include "php/controleParametre.php";
	
	?>

<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Simulateur en ligne pour choisir une spécialité d'internat - Liste des Villes, CHU ou Subdivision">

	<?php
		// favicons générés par https://realfavicongenerator.net
		include "php/favicon.php";
	
        // Google Analytics
		include "php/GoogleAnalytics.php";
	?>

    <title>Liste des CHU correspondants aux critères saisis</title>

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
				<li class="breadcrumb-item active" aria-current="page">CHU</li>
			  </ol>
			</div>
			<div class="col-sm">
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

		// fonctions communes
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

		$libelleCesp = "CESP2024";					// les postes et cesp sont absents en base pour les années avant 2020 (on prend ceux de 2024 par défaut)
		if ($reference == "2023") {
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
			$whereSpecialite = $where . " AND Rang.Dernier" . $reference . " >= '" . $rang ."'";
		} else {
			$whereSpecialite = $where;
		}

		// requête pour compter le nombres de postes et de CESP
		$nbPoste = 0;
		$nbCESP = 0;
		$libellePoste = "Poste2024";					// les postes et cesp sont absents en base pour les années avant 2020 (on prend ceux de 2024 par défaut)
		if ($reference == "2023") {
			$libellePoste = "Poste2023";
		} elseif ($reference == "2022") {
			$libellePoste = "Poste2022";
		} elseif ($reference == "2021") {
			$libellePoste = "Poste2021";
		} elseif ($reference == "2020") {
			$libellePoste = "Poste2020";
		}
		$sql = "SELECT Rang.CHU, SUM(Rang." . $libellePoste . ") AS totalPoste, SUM(Rang." . $libelleCesp . ") AS totalCESP FROM Specialite inner join Rang on Specialite.CodeSpecialite = Rang.CodeSpecialite " . $whereSpecialite . " GROUP BY Rang.CHU;";
		if ($debug) echo "SQL POSTE = " . $sql ."<br/>";
		try {
			$result = $db->query($sql);
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
		$sql = "SELECT Rang.CHU, max(Rang.Dernier2024) as Dernier2024,
								 max(Rang.Dernier2023) as Dernier2023,
								 max(Rang.Dernier2022) as Dernier2022,
								 max(Rang.Dernier2021) as Dernier2021,
								 max(Rang.Dernier2020) as Dernier2020,
								 max(Rang.Dernier2019) as Dernier2019,
								 max(Rang.Dernier2018) as Dernier2018,
								 max(Rang.Dernier2017) as Dernier2017,
								 sum(Rang.Poste2024) as Poste2024,
								 sum(Rang.CESP2024) as CESP2024,
								 sum(Rang.Poste2023) as Poste2023,
								 sum(Rang.CESP2023) as CESP2023,
								 sum(Rang.Poste2022) as Poste2022,
								 sum(Rang.CESP2022) as CESP2022,
								 sum(Rang.Poste2021) as Poste2021,
								 sum(Rang.CESP2021) as CESP2021,
								 sum(Rang.Poste2020) as Poste2020,
								 sum(Rang.CESP2020) as CESP2020
				FROM `Specialite` inner join Rang on Specialite.CodeSpecialite = Rang.CodeSpecialite " . $whereSpecialite . " GROUP BY Rang.CHU;";
		if ($debug) echo "SQL = " . $sql ."<br/>";

		// exécution de la requête
		try {
			$result = $db->query($sql);

			// titre
			echo "<br/><h2 class='h5' style='text-align:left'>" . $result->rowCount() ." CHU correspondent à vos critères ";
			if ($cesp != "off") {
				echo " en CESP ";
			}
			if (($rang != 0) and ($rang != null) and ($rang != "rangIndifferent")) {
				echo "pour le rang " . getLibelleRang($rang) . " en " . $reference;
			}
			echo "</h2><br/>";

			// liste
			echo "<table class='table-hover' style='width:100%;'>";
			echo "<caption>Cliquer &nbsp;<i class='bi bi-cursor-fill'></i>&nbsp; sur un CHU pour voir ses spécialités.</caption>";
			echo "<thead class='text-center'>";
			echo "<tr><th style='width:50%'>" . $result->rowCount() ." CHU<br/><i class='bi bi-info-circle-fill' data-toggle='tooltip' data-html='true' title='Cliquer sur un CHU pour voir les spécialités pour ce CHU.'></i></th>";
			if ($reference < 2020) {
				$libelleReference = "2024";
			} else {	
				$libelleReference = $reference;
			}
			echo "<th style='width:20%;'> ".$montant->format($nbPoste)." postes " . $libelleReference . "<br/><i class='bi bi-info-circle-fill' data-toggle='tooltip' data-html='true' title='Le nombre de postes est issu de l&apos;arrêté publié par le Journal Officiel. Ce nombre de postes exclut les CESP.'></i></th>";
			echo "<th style='width:20%'> Rang dernier " . $reference . "<br/><i class='bi bi-info-circle-fill' data-toggle='tooltip' data-html='true' title='A partir de 2024, il s&apos;agit du rang limite par groupe de spécialités.<br>Auparavant c&apos;était le rang limite national par spécialité.'></i></th>";
			echo "<th style='width:10%;'> ".$montant->format($nbCESP)." CESP " . $libelleReference . " <br/><i class='bi bi-info-circle-fill' data-toggle='tooltip' data-html='true' title='Le nombre de postes réservés aux CESP est issu de l&apos;arrêté publié par le Journal Officiel.<br/>Une cellule vide signifie qu&apos;il n&apos;y a pas de poste CESP pour cette spécialité.'></i></th>";
			echo "</tr></thead>";
			echo "<tbody>";

			// récupération des données à afficher
			while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
				extract($row);
				echo "<tr onclick='zoom(&apos;".$CHU."&apos;)'>";
				echo "<td class='text-center'>" . $CHU . "</td>";
				$dernier = "0";
				$poste = "0";
				$libelleCesp = "0";
				if ($reference == 2024) {
					$dernier = $montant->format($Dernier2024);
					$poste = $Poste2024;
					$libelleCesp = $CESP2024;
				} elseif ($reference == 2023) {
					$dernier = $montant->format($Dernier2023);
					$poste = $Poste2023;
					$libelleCesp = $CESP2023;
				} elseif ($reference == 2022) {
					$dernier = $montant->format($Dernier2022);
					$poste = $Poste2022;
					$libelleCesp = $CESP2022;
				} elseif ($reference == 2021) {
					$dernier = $montant->format($Dernier2021);
					$poste = $Poste2021;
					$libelleCesp = $CESP2021;
				} elseif ($reference == 2020) {
					$dernier = $montant->format($Dernier2020);
					$poste = $Poste2020;
					$libelleCesp = $CESP2020;
				} elseif ($reference == 2019) {
					$dernier = $montant->format($Dernier2019);
					$poste = $Poste2024;
					$libelleCesp = $CESP2024;
				} elseif ($reference == 2018) {
					$dernier = $montant->format($Dernier2018);
					$poste = $Poste2024;
					$libelleCesp = $CESP2024;
				} elseif ($reference == 2017) {
					$dernier = $montant->format($Dernier2017);
					$poste = $Poste2024;
					$libelleCesp = $CESP2024;
				}
				$poste = $montant->format($poste);
				echo "<td class='text-center'>" . $poste . "</td>";
				echo "<td class='text-center'>" . $dernier . "</td>";
				if ($libelleCesp <> 0) {
					$nbCesp = $montant->format($libelleCesp);
				} else {
					$nbCesp = '';
				}
				echo "<td class='derniereColonne text-center'>" . $nbCesp . "</td>";
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
				echo "window.location.href='tableau-specialite.php?code=" . $code . "&rang=" . $rang . "&reference=" . $reference . "&type=" . $type . "&cesp=" . $cesp . "&lieu=" . $lieu . "&internat=" . $internat . "&benefice=" . $benefice . "&depuis=tableau';";
			?>
		}

		// pour retourner en arrière dans l'historique du navigateur
		function questionnaire() {
			<?php
				echo "window.location.href='questionnaire-choix-specialite.php?code=" . $code . "&rang=" . $rang . "&reference=" . $reference . "&type=" . $type . "&cesp=" . $cesp . "&lieu=" . $lieu . "&internat=" . $internat . "&benefice=" . $benefice . "';";
			?>
		}

		// pour zoomer sur une spécialité
		function zoom(chu) {
			<?php
				echo "window.location.href='detail-CHU.php?rang=" . $rang . "&reference=" . $reference . "&chu=' + chu + '" . "&type=" . $type . "&cesp=" . $cesp . "&lieu=" . $lieu . "&internat=" . $internat . "&benefice=" . $benefice . "&depuis=liste';";
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