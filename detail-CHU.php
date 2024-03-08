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
	?>

	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-57099678-5"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());
	  gtag('config', 'UA-57099678-5');
	</script>
    
	<title>Description de la spécialité sélectionnée</title>

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
	</style>

  </head>
  <body id="hautdepage" data-spy="scroll" data-target="#navigation" data-offset="0">

	<?php
		include "php/menu-questionnaire.php";
		include "php/controleParametre.php";
		include "php/fonctionECN.php";
	?>
	
	<!-- chemin de navigation -->
	<nav id="chemin">
		<div class="row" style='margin-top:80px;'>
			<div class="col-sm" aria-label="breadcrumb">
			  <ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="choix-specialite-chu-celine-ecn.php"><i class="fas fa-home"></i></a></li>
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
    		<a class="h5" data-toggle="collapse" aria-expanded="false" aria-controls="critere" href="#critere"><i id="symbole" class="fa fa-plus-circle" aria-hidden="true"></i>&nbsp; Vos critères de choix...</a>
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

		// conexion à la base ecn (user = ecn)
		try {
			$db = new PDO("mysql:host=localhost;dbname=ecn;charset=utf8", "ecn", "ecn");
		}
		catch(PDOException $erreur)	{
			die('Erreur connexion base : ' . $erreur->getMessage());
		}

		// passage au mode exception pour les erreurs
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// A ACTIVER PENDANT LA PHASE DE CHOIX DE POSTE
		// requête pour afficher la date de mise à jour des données 2023
 		$date = 0;
 		$heure = 0;
// 		if ($reference == "2023") {
// 			$sql = "
// 				SELECT
// 						DateMiseAJour,
// 						HeureMiseAJour
// 					FROM MiseAJour
// 					WHERE Id = 0;";
// 			if ($debug) echo "SQL = " . $sql ."<br/>";
// 			try {
// 				$result = $db->query($sql);
// 				while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
// 					extract($row);
// 					$date = $DateMiseAJour;
// 					$heure = $HeureMiseAJour;
// 				}
// 			}
// 			catch(PDOException $erreur)	{
// 				echo "Erreur SELECT MiseAjour : " . $erreur->getMessage();
// 			}
// 		}
		
		// préparation de la clause where pour sélectionner les spécialités en fonction des critères
		$where = " WHERE Type <> '' AND Rang.Poste2023 > 0 ";

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
			$where = $where . " AND Rang.CESP2023 <> '0' AND Rang.CESP2023 <> ''";
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
		$sql = "SELECT Rang.CHU, SUM(Rang.Poste2023) AS totalPoste, SUM(Rang.CESP2023) AS totalCESP  FROM Specialite inner join Rang on Specialite.CodeSpecialite = Rang.CodeSpecialite " . $whereSpecialite . " AND Rang.CHU='" . $chu . "';";
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
		$sql = "SELECT	Rang.CodeSpecialite as CodeSpecialite,
						Rang.Dernier2023 as Dernier2023,
						Rang.Dernier2022 as Dernier2022,
						Rang.Dernier2021 as Dernier2021,
					 	Rang.Dernier2020 as Dernier2020,
						Rang.Dernier2019 as Dernier2019,
						Rang.Dernier2018 as Dernier2018,
						Rang.Dernier2017 as Dernier2017,
						Rang.URLCeline,
						Rang.Poste2023 as Poste2023,
						Rang.CESP2023 as CESP2023
				FROM `Specialite` inner join Rang on Specialite.CodeSpecialite = Rang.CodeSpecialite " . $whereSpecialite . " AND Rang.CHU='" . $chu . "';";
		if ($debug) echo "SQL = " . $sql ."<br/>";

		// exécution de la requête
		try {
			$result = $db->query($sql);

			// titre
			echo "<br/><h2 class='h5' style='text-align:left'>" . $result->rowCount() ." spécialités correspondent à vos critères ";
			if ($cesp != "off") {
				echo " en CESP ";
			}
			if (($rang != 0) and ($rang != null) and ($rang != "rangIndifferent")) {
				echo "pour le rang " . getLibelleRang($rang) . " en " . $reference;
			}
			echo " à " . $chu . "</h2><br/>";

			// liste
			echo "<table class='table-hover' style='width:100%;'>";
// A ACTIVER PENDANT LA PHASE DE CHOIX DE POSTE	
			if ($reference == "2023") {
				echo "<caption>Double cliquer <i class='far fa-hand-pointer'></i> sur une <strong>spécialité</strong> pour afficher le détail <strong>CELINE</strong>.</caption>";
			}
			echo "<thead class='text-center'>";
// A ACTIVER PENDANT LA PHASE DE CHOIX DE POSTE	
			echo "<tr><th colspan=2 style='width:50%'>" . $result->rowCount() ." Spécialités à " . $chu . "<br/><i class='fas fa-info-circle' data-toggle='tooltip' data-html='true' title='Cliquer sur une spécialité pour voir le détail dans CELINE.'></i></th>";
//			echo "<tr><th colspan=2 style='width:50%'>" . $result->rowCount() ." Spécialités à " . $chu . "</th>";
			echo "<th style='width:20%;'> ".$montant->format($nbPoste)." postes 2023 <br/><i class='fas fa-info-circle' data-toggle='tooltip' data-html='true' title='Le nombre de postes 2023 est issu de l&apos;arrêté publié par le Journal Officiel du 4 août 2023. Ce nombre de postes exclut les CESP (pour mémoire : 252 en 2023 dont 213 en Médecine Générale).'></i></th>";
			$libelleReference = "2020";
// A ACTIVER PENDANT LA PHASE DE CHOIX DE POSTE
// 			if ($reference == "2023") {
// 				$libelleReference = $reference . "<br/><small>" . $date . " à " . $heure . "</small>";
// 				echo "<th style='width:20%'> Rang dernier " . $libelleReference . "<br/><i class='fas fa-info-circle' data-toggle='tooltip' data-html='true' title='Le rang du dernier admis en " . $reference . " est issu de la simulation CELINE et est actualisé une fois par heure. <br/>Un rang à zéro signifie qu&apos;il n&apos;y avait pas de poste cette année là dans ce CHU pour cette spécialité, ou pas encore de voeux exprimé.'></i></th>";
// 			} else {
				$libelleReference = $reference;
				echo "<th style='width:20%'> Rang dernier " . $libelleReference . "<br/><i class='fas fa-info-circle' data-toggle='tooltip' data-html='true' title='Le rang du dernier admis en " . $reference . " est issu du fichier &apos;Rangs limites " . $reference . "&apos; du site CNG Santé.'></i></th>";
//			}
			echo "<th style='width:10%;'> ".$montant->format($nbCESP)." CESP 2023 <br/><i class='fas fa-info-circle' data-toggle='tooltip' data-html='true' title='Le nombre de postes 2023 réservés aux CESP est issu de l&apos;arrêté publié par le Journal Officiel du 4 août 2023.<br/>Une cellule vide signifie qu&apos;il n&apos;y a pas de poste CESP pour cette spécialité.'></i></th>";
			echo "</tr></thead>";
			echo "<tbody>";

			// récupération des données à afficher
			while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
				extract($row);
// A ACTIVER PENDANT LA PHASE DE CHOIX DE POSTE
				echo "<tr ondblclick='celine(&apos;".$URLCeline."&apos;)'>";
// A DESACTIVER PENDANT LA PHASE DE CHOIX DE POSTE
//				echo "<tr>";

				echo "<td class='acronyme'>" . $CodeSpecialite . "</td>";
				echo "<td>" . getLibelleSpecialite($CodeSpecialite) . "</td>";
				$poste = $montant->format($Poste2023);
				echo "<td class='text-center'>" . $poste . "</td>";
				$dernier = "0";
				if ($reference == 2023) {
					$dernier = $montant->format($Dernier2023);
				} elseif ($reference == 2022) {
					$dernier = $montant->format($Dernier2022);
				} elseif ($reference == 2021) {
					$dernier = $montant->format($Dernier2021);
				} elseif ($reference == 2020) {
					$dernier = $montant->format($Dernier2020);
				} elseif ($reference == 2019) {
					$dernier = $montant->format($Dernier2019);
				} elseif ($reference == 2018) {
					$dernier = $montant->format($Dernier2018);
				} elseif ($reference == 2017) {
					$dernier = $montant->format($Dernier2017);
				}
				echo "<td class='text-center'>" . $dernier . "</td>";
				if ($CESP2023 <> 0) {
					$nbCesp = $montant->format($CESP2023);
				} else {
					$nbCesp = '';
				}
				echo "<td class='derniereColonne text-center'>" . $nbCesp . "</td>";
				echo "<td class='milieu'></td>";
				echo "</tr>";
			}
			echo "<tr><td colspan=5 style='border-top-style:solid; border-left-style:hidden; border-right-style:hidden; border-bottom-style:hidden;'></td></tr>";
			echo "</body>";
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
			<button class="btn btn-primary" onclick="questionnaire()">&#10072;&larr;&nbsp; Retour aux critères</button>
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

		// pour aller au détail format carte
		function carte() {
			<?php
				echo "window.location.href='carte-chu.php?code=" . $code . "&rang=" . $rang . "&reference=" . $reference . "&type=" . $type . "&cesp=" . $cesp . "&lieu=" . $lieu . "&internat=" . $internat . "&benefice=" . $benefice . "&depuis=" . $depuis . "';";
			?>
		}

		// pour retourner à la liste des résultats
		function liste() {
			<?php
				echo "window.location.href='liste-CHU.php?code=" . $code . "&rang=" . $rang . "&reference=" . $reference . "&type=" . $type . "&cesp=" . $cesp . "&lieu=" . $lieu . "&internat=" . $internat . "&benefice=" . $benefice . "';";
			?>
		}

		// pour retourner au questionnaire
		function questionnaire() {
			<?php
				echo "window.location.href='questionnaire-choix-specialite.php?code=" . $code . "&rang=" . $rang . "&reference=" . $reference . "&type=" . $type . "&cesp=" . $cesp . "&lieu=" . $lieu . "&internat=" . $internat . "&benefice=" . $benefice . "';";
			?>
		}
		
		// pour voir le détail Celine d'une cellule si l'année de référence est 2023
// A ACTIVER PENDANT LA PHASE DE CHOIX DE POSTE
		function celine(urlCeline) {
			<?php
				if ($reference == "2023") {
					echo "if (urlCeline != '') {";
					echo "	window.open(urlCeline,'Détail Céline');";
					echo "}";
				}
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