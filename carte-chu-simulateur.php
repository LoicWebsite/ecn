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
	?>

	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=ID GOOGLE"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());
	  gtag('config', 'ID GOOGLE');
	</script>

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
		include "php/menu-simulateur.php";
		include "php/controleParametre.php";
		include "php/fonctionECN.php";
		include "php/fonctionCarte.php";
	?>

	<!-- chemin de navigation -->
	<nav id="chemin">
		<div class="row" style='margin-top:80px;'>
			<div class="col-sm" aria-label="breadcrumb">
			  <ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="choix-specialite-chu-celine-ecn.php#specialite"><i class="fas fa-home"></i></a></li>
				<li class="breadcrumb-item active" aria-current="page">CHU</li>
			  </ol>
			</div>
			<div class="col-sm">
				<p style='padding:10px;'>
					<button class="btn btn-primary btn-sm" onclick="detail()" title="Affichage des CHU en liste"> en liste &nbsp; <i class="fa fa-list" aria-hidden="true"></i></button>
					&nbsp;&nbsp;&nbsp;<button class="btn btn-secondary btn-sm" onclick="" title="Affichage des CHU sur une carte de France" disabled> en carte &nbsp; <i class="fas fa-map-marked-alt" aria-hidden="true"></i></button>
				</p>
			</div>
			<div class="col-xl">
			</div>
		</div>
	</nav>
	
	<!-- résumé de la spécialité -->
	<?php 				
		include "php/resume-specialite.php";	
	?>

	<!-- titre -->	
	<div id='titre' class='container'>

	<?php

		$listeCHU = array();
		$listeDernier = array();
		$listePoste = array();
		$listeCesp = array();
		$listeUrl = array();

		// construction clause where		
		$where = " WHERE Rang.CodeSpecialite = '" . $CodeSpecialite ."'";
// 		if (($rang > "") and ($rang <> 0)) {
// 			$where = $where . " AND Dernier" . $reference . " >='" . $rang . "'";
// 		}
// 		if ($cesp == "on") {
// 			$where = $where . " AND CESP.CESP2023 > '0'";
// 		}
		$where = $where . ";";

		// préparation de la requête pour la table Rang
		$sql = "
			SELECT
					Rang.CodeSpecialite,
					Rang.CHU,
					Rang.Dernier2023,
					Rang.Dernier2022,
					Rang.Dernier2021,
					Rang.Dernier2020,
					Rang.Dernier2019,
					Rang.Dernier2018,
					Rang.Dernier2017,
					Rang.Poste2023,
					Rang.Poste2022,
					Rang.Poste2021,
					Rang.Poste2020,
					Rang.URLCeline,
					Rang.CESP2023,
					Rang.CESP2022,
					Rang.CESP2021,
					Rang.CESP2020
				FROM Rang" 
				. $where;
		if ($debug) echo "SQL = " . $sql ."<br/>";

		// exécution de la requête
		try {
			$result = $db->query($sql);
			$montant = new NumberFormatter("fr-FR", NumberFormatter::DECIMAL);
			$nbCHU = 0;
			$i = 0;
			
			// récupération des rangs à mémoriser dans un tableau
			while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
				extract($row);
				$listeCHU[] = $CHU;
				$listePoste[] = $Poste2023;
				$listeCesp[] = $CESP2023;
				if ($reference == "2023") {
					$listeDernier[] = $Dernier2023;
				} elseif ($reference == "2022") {
					$listeDernier[] = $Dernier2022;
				} elseif ($reference == "2021") {
					$listeDernier[] = $Dernier2021;
				} elseif ($reference == "2020") {
					$listeDernier[] = $Dernier2020;
				} elseif ($reference == "2019") {
					$listeDernier[] = $Dernier2019;
				} elseif ($reference == "2018") {
					$listeDernier[] = $Dernier2018;
				} elseif ($reference == "2017") {
					$listeDernier[] = $Dernier2017;
				} else {
					$listeDernier[] = 0;
				}
				$listeUrl[] = $URLCeline;
	
				// comptage des chu accessibles selon le critère cesp et rang s'il y a au moins 1 poste
				if ($cesp == "on") {
					if (($CESP2023 != null) and ($CESP2023 > 0 )) {
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

				if (($rangOk) and ($cespOk) and ($Poste2023 > 0)) {
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
 		<p class="text-center">Cliquer &nbsp;<i class='fa fa-mouse-pointer' aria-hidden='true'></i>&nbsp; sur un CHU pour voir le détail.<br/>
<!-- A REACTIVER quand Celine actif -->
		<?php
			if ($reference == "2023") {
 				echo "Double cliquer &nbsp;<i class='far fa-hand-pointer' aria-hidden='true'></i>&nbsp; sur un CHU pour voir le détail des rangs dans Celine (uniquement pour 2023).";
 			}	
		?>

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
				echo "window.location.href='detail-specialite-simulateur.php?specialite=" . $specialite . "';";
			?>
		}

// A REACTIVER quand Celine actif
		// pour voir le détail Celine d'un CHU
		$( "g a" ).dblclick(function() {
			$('g a').tooltip('hide');
			<?php
				if ($reference == "2023") {
					echo "if ($(this).data('url') != '') {";
					echo "window.open($(this).data('url'),'Détail Céline');";
					echo "}";
 				}
			?>
		});

		// pour retourner à la page principale
		function home() {
			<?php
				echo "window.location.href='choix-specialite-chu-celine-ecn.php#specialite';";
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