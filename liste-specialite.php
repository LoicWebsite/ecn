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

//		$where = $where . ";";

		// requête pour compter les nombres de postes
		$nbPoste = 0;
		$nbCESP = 0;
		$libellePoste = "Poste2024";
		$libelleCesp = "CESP2024";			// avant 2020 le nombre de postes et de CESP n'est pas en base (on prend 2024 par défaut)
		if ($reference == 2023) {
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

		$sql = "SELECT Rang.CodeSpecialite, SUM(Rang." . $libellePoste . ") AS totalPoste, SUM(Rang." . $libelleCesp . ") AS totalCESP  FROM Specialite
				inner join Rang on Specialite.CodeSpecialite = Rang.CodeSpecialite " . $whereSpecialite . " GROUP BY Rang.CodeSpecialite;";
		if ($debug) echo "SQL = " . $sql ."<br/>";

		// exécution de la requête
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
		
		// préparation de la requête pour afficher les spécialités
		$sql = "SELECT	Rang.CodeSpecialite as Specialite,
						max(Rang.Dernier2024) as Dernier2024,
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
				FROM `Specialite` inner join Rang on Specialite.CodeSpecialite = Rang.CodeSpecialite " . $whereSpecialite . " GROUP BY Rang.CodeSpecialite;";
		if ($debug) echo "SQL = " . $sql ."<br/>";

		// exécution de la requête
		try {
			$result = $db->query($sql);
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

			// liste
			echo "<table class='table-hover' style='width:100%;'>";
			echo "<caption>Cliquer &nbsp;<i class='bi bi-cursor-fill'></i>&nbsp; sur une spécialité pour voir les CHU pour cette spécialité.</caption>";
			echo "<thead class='text-center'>";
			echo "<tr><th colspan=2 style='width:50%'>" . $result->rowCount() ." spécialités d'internat<br/><i class='bi bi-info-circle-fill' data-toggle='tooltip' data-html='true' title='Cliquer sur une spécialité pour voir les CHU pour cette spécialité.'></i></th>";
			if ($reference < 2020) {
				$libelle = "2024";
			} else {	
				$libelle = $reference;
			}
			echo "<th style='width:20%;'> ".$montant->format($nbPoste)." postes " . $libelle . "<br/><i class='bi bi-info-circle-fill' data-toggle='tooltip' data-html='true' title='Le nombre de postes est issu de l&apos;arrêté publié par le Journal Officiel. Ce nombre de postes exclut les CESP.<br/>L&apos;année correspond à l&apos;année de publication au Journal Officiel.'></i></th>";
			echo "<th style='width:20%'> Rang dernier " . $reference . "<br/><i class='bi bi-info-circle-fill' data-toggle='tooltip' data-html='true' title='A partir de 2024, il s&apos;agit du rang limite par groupe de spécialités.<br>Auparavant c&apos;était le rang limite national par spécialité.'></i></th>";
			echo "<th style='width:10%;'> ".$montant->format($nbCESP)." CESP " . $libelle . " <br/><i class='bi bi-info-circle-fill' data-toggle='tooltip' data-html='true' title='Le nombre de postes réservés aux CESP est issu de l&apos;arrêté publié par le Journal Officiel.<br/>Une cellule vide signifie qu&apos;il n&apos;y a pas de poste CESP pour cette spécialité.'></i></th>";
			echo "</tr></thead>";
			echo "<tbody>";
			// récupération des données à afficher
			while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
				extract($row);
				echo "<tr onclick='zoom(&apos;".$Specialite."&apos;)'>";
				$libelleSpecialite = getLibelleSpecialite($Specialite);

				// en local les caractères accentués passent, mais pas sur le serveur Gandi
				// Vérifie si l'encodage est UTF-8, sinon convertis depuis ISO-8859-1 (Latin-1)
				if (!mb_detect_encoding($libelleSpecialite, 'UTF-8', true)) {
					$libelleSpecialite = mb_convert_encoding($libelleSpecialite, 'UTF-8', 'ISO-8859-1');
				}

				echo "<td class='acronyme'>".$Specialite . "</td><td>" . $libelleSpecialite . "</td>";
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
				echo "<td class='text-center'>".$montant->format($poste)."</td>";
				echo "<td class='text-center'>" . $dernier . "</td>";
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
		function zoom(code) {
			<?php
				echo "window.location.href='detail-specialite-questionnaire.php?rang=" . $rang . "&reference=" . $reference . "&code=' + code + '" . "&type=" . $type . "&cesp=" . $cesp . "&lieu=" . $lieu . "&internat=" . $internat . "&benefice=" . $benefice . "&depuis=liste';";
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