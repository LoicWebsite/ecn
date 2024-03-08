<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Simulateur de choix de spécialité d'internat - détail d'une spécialité médicale ou chirurgicale">
	<link rel="canonical" href="https://loic.website/ECN/detail-specialite-questionnaire.php" />

	<?php
		// favicons générés par https://realfavicongenerator.net
		include "php/favicon.php";
	?>
	
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-4NC0K56D5R"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());
	  gtag('config', 'G-4NC0K56D5R');
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
		include "php/menu-simulateur.php";
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
					<button class="btn btn-secondary btn-sm" onclick="" title="Affichage des CHU en liste" disabled> en liste &nbsp; <i class="fa fa-list" aria-hidden="true"></i></button>
					&nbsp;&nbsp;&nbsp;<button class="btn btn-primary btn-sm" onclick="carte()" title="Affichage des CHU sur une carte de France"> en carte &nbsp; <i class="fas fa-map-marked-alt" aria-hidden="true"></i></button>
				</p>
			</div>
			<div class="col-xl">
			</div>
		</div>
	</nav>
	
	<!-- affichage du détail d'une spécialité -->		
	<?php
		include "php/detail.php";
	?>

	<!-- retour en arrière vers le formulaire -->
	<footer style='margin-top:40px; margin-bottom:80px;'>
		<br/>
		<p class=text-center>
			<button class="btn btn-primary" onclick="home()">&larr;&nbsp; Retour</button>
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
		// pour retourner à la page principale
		function home() {
			<?php
				echo "window.location.href='choix-specialite-chu-celine-ecn.php#specialite';";
			?>
		}
		
		// pour aller au détail format carte
		function carte() {
			<?php
				echo "window.location.href='carte-chu-simulateur.php?specialite=" . $specialite . "';";
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