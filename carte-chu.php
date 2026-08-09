<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Simulateur en ligne pour choisir une spécialité d'internat - carte de France des CHU pour une spécialité médicale ou chirurgicale">

	<?php
		// favicons générés par https://realfavicongenerator.net
		include "php/favicon.php";
	
		// Google Analytics
		include "php/GoogleAnalytics.php";
	?>

    <title>Carte des CHU</title>
    
	<?php
		// styles nécessaires à l'application (bootstrap + fontawasome + ECN)
		include "php/style.php";

		// Balise canonique pour éviter les doublons dus aux paramètres d'URL
		include "php/canonical.php";
	?>
	
	<style>
		.carte {
			width: 100%;
			margin: 0 auto;
			padding: 0;
		}
		path {
			stroke: gray;
			stroke-width: 1px;
			stroke-linecap: round;
			stroke-linejoin: round;
			stroke-opacity: .25;
			fill: lightblue;
		}
		g a:hover {
		  text-decoration: none;
		  cursor: pointer;
		}
		g:hover path {
			fill: #86cce0;
		}
		text {
			font-size: 18px;
    	}
    	@media (max-width: 576px) {
      		text {
        		font-size: 22px;
      		}
    	}
    	.accessible {
    		fill: blue;
    	}
    	text {
    		fill: gray;
    	}
	</style>

  </head>
  <body id="hautdepage">

	<?php
		/*
		 * Gestion annuelle des données Rang/Poste/CESP (3 phases)
		 *
		 * Phase 1 - début d'année:
		 *   Les colonnes de l'année peuvent être absentes (ou incomplètes).
		 *
		 * Phase 2 - milieu d'année:
		 *   PosteAAAA et CESPAAAA existent, mais DernierAAAA n'existe pas encore.
		 *
		 * Phase 3 - fin d'année:
		 *   Toutes les colonnes de l'année existent.
		 *
		 * Cas particulier avant 2020:
		 *   Les colonnes Poste/CESP n'existent pas historiquement pour ces années.
		 *   Le script prend alors les colonnes les plus récentes disponibles.
		 *
		 * Implémentation:
		 *   - Détection des colonnes présentes via INFORMATION_SCHEMA.
		 *   - Résolution des sources avec fallback
		 *     (année exacte > année précédente > dernière disponible).
		 *   - Application de ces sources aux filtres, aux valeurs affichées et aux tooltips.
		 */

		// menu de l'application, contrôle des paramètres et fonctions communes
		include "php/menu-questionnaire.php";
		require_once "php/controleParametre.php";
		require_once "php/fonctionECN.php";
		include "php/fonctionCarte.php";
	
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
				<?php 
					if ($depuis == "tableau") {
						echo "<li class='breadcrumb-item'><a href='#' onclick='tableau()'>Spécialité</a></li>";
					} else {
						echo "<li class='breadcrumb-item'><a href='#' onclick='liste()'>Spécialité</a></li>";
					}
				?>
				<li class="breadcrumb-item active" aria-current="page">CHU</li>
			  </ol>
			</div>
			<div class="col-sm">
				<p style='padding:10px;'>
					<button class="btn btn-primary btn-sm" onclick="detail()" title="Affichage des CHU en liste"> en liste &nbsp; <i class="bi bi-list-ul"></i></button>
					&nbsp;&nbsp;&nbsp;<button class="btn btn-secondary btn-sm" onclick="" title="Affichage des CHU sur une carte de France" disabled> en carte &nbsp; <i class="bi bi-geo-alt-fill"></i></button>
				</p>
			</div>
			<div class="col-xl">
			</div>
		</div>
	</nav>
	
	<!-- résumé de la spécialité -->
	<?php 				
		include "php/resume-specialite.php";	
		$CodeSpecialite = isset($CodeSpecialite) ? $CodeSpecialite : $code;
	?>

	<!-- titre -->	
	<div id='titre' class='container'>

	<?php

		$listeCHU = array();
		$listeDernier = array();
		$listePoste = array();
		$listeCesp = array();
		$listeUrl = array();

		// Détection de la phase annuelle et résolution des colonnes disponibles.
		$referenceAnnee = intval($reference);
		$rangYearColumns = getRangYearColumns($db);
		$phaseAnnuelle = getAnnualDataPhase($rangYearColumns, $referenceAnnee);
		$rangSources = resolveAnnualRangSources($rangYearColumns, $referenceAnnee);
		$colonneDernier = $rangSources['dernier']['column'];
		$colonnePoste = $rangSources['poste']['column'];
		$colonneCesp = $rangSources['cesp']['column'];

		// construction clause where		
		$where = " WHERE Rang.CodeSpecialite = :codeSpecialite";
		if (($rang > "") and ($rang != 0) and ($rang != "rangIndifferent")) {
			if ($colonneDernier !== null) {
				$where = $where . " AND COALESCE(Rang." . $colonneDernier . ", 0) >= " . intval($rang);
			}
		}
		if ($cesp == "on") {
			if ($colonneCesp !== null) {
				$where = $where . " AND COALESCE(Rang." . $colonneCesp . ", 0) > 0";
			} else {
				$where = $where . " AND 1 = 0";
			}
		}
		if ($colonnePoste !== null) {
			$where = $where . " AND COALESCE(Rang." . $colonnePoste . ", 0) > 0";
		}
		$where = $where . ";";

		// préparation de la requête pour la table Rang
		$dernierExpr = ($colonneDernier !== null) ? "COALESCE(Rang." . $colonneDernier . ",0)" : "0";
		$posteExpr = ($colonnePoste !== null) ? "COALESCE(Rang." . $colonnePoste . ",0)" : "0";
		$cespExpr = ($colonneCesp !== null) ? "COALESCE(Rang." . $colonneCesp . ",0)" : "0";
		$sql = "
			SELECT
					Rang.CodeSpecialite,
					Rang.CHU,
					" . $dernierExpr . " AS DernierRef,
					" . $posteExpr . " AS PosteRef,
					" . $cespExpr . " AS CESPRef,
					Rang.URLCeline,
					Rang.CodeSpecialite
				FROM Rang" 
				. $where;
		if ($debug) echo "SQL = " . $sql ."<br/>";

		// exécution de la requête
		try {
			$stmt = $db->prepare($sql);
			$stmt->execute([':codeSpecialite' => $CodeSpecialite]);
			$result = $stmt;
			$montant = new NumberFormatter("fr-FR", NumberFormatter::DECIMAL);
			$nbCHU = 0;
			$i = 0;
			
			// récupération des rangs à mémoriser dans un tableau
			while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
				extract($row);
				$dernier = intval($DernierRef);
				$poste = intval($PosteRef);
				$libelleCesp = intval($CESPRef);

				$listeCHU[] = $CHU;
				$listeDernier[] = $dernier;
				$listePoste[] = $poste;
				$listeCesp[] = $libelleCesp;
				$listeUrl[] = $URLCeline;
	
				// comptage des chu accessibles selon le critère cesp et rang s'il y a au moins 1 poste
				if ($cesp == "on") {
					if (($libelleCesp != null) and ($libelleCesp > 0 )) {
						$cespOk = true;
					} else {
						$cespOk = false;
					}
				} else {
					$cespOk = true;
				}

				if (($rang != "rangIndifferent") and ($rang != null) and ($rang != 0)) {
					if ($listeDernier[$i] >= $rang) {
						$rangOk = true;
					} else {
						$rangOk = false;
					}
				} else {
					$rangOk = true;
				}

				if (($rangOk) and ($cespOk) and ($poste > 0)) {
					$nbCHU += 1;
				}

				$i += 1;
			}
			
			// titre de la page
			echo "<h2 class='h5' style='text-align:left;'>". $nbCHU . " CHU possibles en " . $libelleSpecialite;
			if (($rang != "rangIndifferent") and ($rang <> 0)) {
				echo " pour un rang de " . $montant->format($rang) . " en " . $reference;
			}
			if ($cesp == "on") {
				echo " en CESP";
			}
			echo "</h2><br/>";
 			echo "<p style='margin-bottom:0'>En <span style='color:blue;'>bleu</span> les CHU accessibles avec vos critères.</p>";

			if ($debug) {
				var_dump($listeCHU);
				var_dump($listePoste);
				var_dump($listeCesp);
				var_dump($listeDernier);
				var_dump($listeUrl);
			}
		}
		catch(PDOException $erreur)	{
			echo "Erreur : " . $erreur->getMessage();
		}

		// fermeture de la base
		if (isset($result)) {$result->closeCursor();}
		$db = null;

	?>
	</div>

	<!-- carte -->
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-2 col-md-1 col-sm-1">
			</div>
			<div class="carte col-lg-8 col-md-10 col-sm-10">
				<?php
					include "php/carte-france-svg.php";
				?>
			</div>
			<div class="col-lg-2 col-md-1 col-sm-1">
			</div>
		</div>
	</div>
 	<div>
 		<br/>
 		<p class="text-center">Cliquer &nbsp;<i class='bi bi-cursor-fill'></i>&nbsp; sur un CHU pour voir le détail.<br/>
<!-- A REACTIVER quand Celine actif -->
<!-- 
		<?php
			if ($reference == "2023") {
 				echo "Double cliquer &nbsp;<i class='bi bi-hand-index-thumb'></i>&nbsp; sur un CHU pour voir le détail des rangs dans Celine (uniquement pour 2023).";
 			}	
		?>
 -->
		</p>
	</div>
	
	<!-- retour en arrière vers le formulaire -->
	<footer style='margin-top:40px; margin-bottom:80px;'>
		<br/>
		<p class=text-center>
		<?php
			if ($depuis == "tableau") {
				echo "<button class='btn btn-primary' onclick='tableau()'>&larr;&nbsp; Retour aux spécialités</button>";
			} else {
				echo "<button class='btn btn-primary' onclick='liste()'>&larr;&nbsp; Retour aux spécialités</button>";
			}
		?>
		</p>
		<p class=text-center>
			<button class="btn btn-primary" onclick="questionnaire()">&#10072;&larr;&nbsp; Retour aux critères</button>
		</p>
	</footer>

	<?php
		// librairies javascript nécessaires à l'application (jquery + popper + bootstrap)
		include "php/librairie.php";
	?>
	
	<!-- tooltip bootstrap -->
	<script>
		$(function () {
		  $('g a').tooltip()
		})
	</script>

	<!-- navigation -->
	<script>

		//pour basculer sur l'affichage en tableau
		function tableau() {
			<?php
				echo "window.location.href=" . json_encode(buildSafeUrl('tableau-specialite.php', ['code' => $code, 'rang' => $rang, 'reference' => $reference, 'type' => $type, 'cesp' => $cesp, 'lieu' => $lieu, 'internat' => $internat, 'benefice' => $benefice])) . ";";
			?>
		}

		// pour voir le détail Celine d'un CHU
// A REACTIVER quand CELINE actif
// 		$( "g a" ).dblclick(function() {
// 			$('g a').tooltip('hide');
// 			<?php
// 				if ($reference == "2023") {
// 					echo "if ($(this).data('url') != '') {";
// 					echo "window.open($(this).data('url'),'Détail Céline');";
// 					echo "}";
//  				}
// 			?>
// 		});

		// pour retourner au détail format liste
		function detail() {
			<?php
				echo "window.location.href=" . json_encode(buildSafeUrl('detail-specialite-questionnaire.php', ['code' => $code, 'rang' => $rang, 'reference' => $reference, 'type' => $type, 'cesp' => $cesp, 'lieu' => $lieu, 'internat' => $internat, 'benefice' => $benefice, 'depuis' => $depuis])) . ";";
			?>
		}

		// pour retourner à la liste des résultats
		function liste() {
			<?php
				echo "window.location.href=" . json_encode(buildSafeUrl('liste-specialite.php', ['code' => $code, 'rang' => $rang, 'reference' => $reference, 'type' => $type, 'cesp' => $cesp, 'lieu' => $lieu, 'internat' => $internat, 'benefice' => $benefice])) . ";";
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