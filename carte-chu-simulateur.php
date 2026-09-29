<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Simulateur en ligne pour choisir une spécialité d'internat - carte de France des CHU pour une spécialité médicale ou chirurgicale">
	<link rel="canonical" href="https://loic.website/ECN/carte-chu.php" />

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
		// menu de l'application, contrôle des paramètres et fonctions communes
		include "php/menu-questionnaire.php";
		include_once "php/controleParametre.php";
		require_once "php/fonctionECN.php";
		include_once "php/fonctionCarte.php";
	?>

	<!-- chemin de navigation -->
	<nav id="chemin">
		<div class="row" style='margin-top:80px;'>
			<div class="col-sm" aria-label="breadcrumb">
			  <ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="choix-specialite-chu-celine-ecn.php#specialite"><i class="bi bi-house-door-fill"></i></a></li>
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
		// connexion à la base de données
		$db = openDatabase();
		$anneeRang = getRangDisplayYear($db, $reference);
		$rangYearColumns = getRangYearColumns($db);
		$rangSources = resolveAnnualRangSources($rangYearColumns, intval($reference));
		$anneePoste = $rangSources['poste']['year'] ?? intval($reference);
		$anneeCesp = $rangSources['cesp']['year'] ?? intval($reference);

		// affichage du résumé de la spécialité
		include "php/resume-specialite-dynamique.php";
		$CodeSpecialite = isset($CodeSpecialite) ? $CodeSpecialite : $code;
	?>

	<!-- titre -->	
	<div id='titre' class='container'>

	<?php

		$listeCHU = array();
		$listeDernier = array();
		$listeDernierPrincipal = array();
		$listeDernierCESP = array();
		$listeRangCompare = array();
		$listePoste = array();
		$listeCesp = array();

		// exécution de la requête
		try {
			$rangRows = getRangRowsWide($db, $CodeSpecialite);
			$montant = new NumberFormatter("fr-FR", NumberFormatter::DECIMAL);
			$nbCHU = 0;
			$i = 0;
			
			// récupération des rangs à mémoriser dans un tableau
			foreach ($rangRows as $row) {
				extract($row);
				$anneeReference = $rangSources['dernier']['year'] ?? intval($reference);
				$dernierPrincipal = $row['Dernier' . $anneeReference] ?? 0;
				$dernierCesp = $row['DernierCESP' . $anneeReference] ?? 0;
				$dernierReference = $dernierPrincipal;
				$posteReference = $row['Poste' . $anneePoste] ?? 0;
				$cespReference = $row['CESP' . $anneeCesp] ?? 0;
				$listeCHU[] = $CHU;
				$listePoste[] = $posteReference;
				$listeCesp[] = $cespReference;
				$listeDernier[] = $dernierReference;
				$listeDernierPrincipal[] = $dernierPrincipal;
				$listeDernierCESP[] = $dernierCesp;
				$listeRangCompare[] = getRangComparaison($dernierPrincipal, $dernierCesp, $cesp);
	
				// comptage des chu accessibles selon le critère cesp et rang s'il y a au moins 1 poste
				if ($cesp == "on") {
					if (($cespReference != null) and ($cespReference > 0 )) {
						$cespOk = true;
					} else {
						$cespOk = false;
					}
				} else {
					$cespOk = true;
				}

				if (($rang != "rangIndifferent") and ($rang != null) and ($rang != 0)) {
					if ($listeRangCompare[$i] >= $rang) {
						$rangOk = true;
					} else {
						$rangOk = false;
					}
				} else {
					$rangOk = true;
				}

				if (($rangOk) and ($cespOk) and ($posteReference > 0)) {
					$nbCHU += 1;
				}

				$i += 1;
			}
			
			// titre de la page
			echo "<h2 class='h5' style='text-align:left;'>". $nbCHU . " CHU possibles en " . $libelleSpecialite;
			if (($rang != "rangIndifferent") and ($rang <> 0)) {
				echo " pour un rang de " . $montant->format($rang) . " en " . $anneeRang;
			}
			if ($cesp == "on") {
				echo " en CESP";
			}
			echo "</h2><br/>";

			if ($debug) {
				var_dump($listeCHU);
				var_dump($listePoste);
				var_dump($listeCesp);
				var_dump($listeDernier);
			}
		}
		catch(PDOException $erreur)	{
			echo "Erreur : " . $erreur->getMessage();
		}

		// fermeture de la base
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
					$page = "poste";
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
		</p>
	</div>
	
	<!-- retour en arrière vers le formulaire -->
	<footer style='margin-top:40px; margin-bottom:80px;'>
		<br/>
		<p class=text-center>
			<button class="btn btn-primary" onclick="home()">&#10072;&larr;&nbsp; Retour</button>
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

		//pour basculer sur l'affichage en liste
		function detail() {
			<?php
				echo "window.location.href=" . json_encode(buildSafeUrl('detail-specialite-simulateur.php', ['specialite' => $specialite, 'rang' => $rang, 'reference' => $reference, 'type' => $type, 'cesp' => $cesp, 'lieu' => $lieu, 'internat' => $internat, 'benefice' => $benefice])) . ";";
			?>
		}

		// pour retourner à la page principale
		function home() {
			<?php
				echo "window.location.href='choix-specialite-chu-celine-ecn.php#specialite';";
			?>
		}
	</script>

	<script>
		// rend le menu Spécialité actif
		document.addEventListener("DOMContentLoaded", function() {
			// Sélectionne l'élément par son ID
			const specialiteElement = document.getElementById("specialites");
			// Vérifie si l'élément existe avant d'ajouter la classe
			if (specialiteElement) {
				specialiteElement.classList.add("active");
			}
		});
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