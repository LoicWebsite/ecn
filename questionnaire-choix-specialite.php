<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Simulateur en ligne pour choisir une spécialité d'internat - saisie des critères de sélection">

	<?php
		// favicons générés par https://realfavicongenerator.net
		include "php/favicon.php";
	?>

	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=GOOGLE-ID"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());
	  gtag('config', 'G-4NC0K56D5R');
	</script>
    
    <title>Saisie des critères de sélection des spécialités</title>
    
	<?php
		// styles nécessaires à l'application (bootstrap + fontawasome + ECN)
		include "php/style.php";
	?>
	
  </head>
  <body id="hautdepage" data-spy="scroll" data-target="#navigation" data-offset="0">

	<?php
		include "php/menu-questionnaire.php";
	?>

	<nav style='margin-top:80px;' aria-label="breadcrumb">
	  <ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="choix-specialite-chu-celine-ecn.php"><i class="fas fa-home"></i></a></li>
		<li class="breadcrumb-item active" aria-current="page">Critère</li>
	  </ol>
	</nav>

	<?php
		// récupération-contrôle des paramètres
		include "php/controleParametre.php";
	?>

	<div id="questionnaire" class="container">

		<!-- formulaire -->
		<form action="liste-specialite.php" method="GET">

			<!-- critères -->
			<div class="border p-2">
				<h1 class="h4" style="text-align:left;">1 - Saisissez vos critères de choix</h1>
				<br/>
		
				<!-- rang visé -->
				<div class="row">
					<div class="col-md-1">
					</div>
					<div class="col">
						Rang ECN visé ou obtenu :
					</div>
					<div class="col">
						<input id="rang" name="rang" type="number" min="0" max="10000" class="form-control" placeholder="indifférent">
					</div>
					<div class="col-md-1">
					</div>
				</div>

				<!-- année de référence pour le rang visé -->
				<br/>
				<div class="row">
					<div class="col-md-1">
					</div>
					<div class="col">
						Année de référence :
					</div>
					<div class="col">
						<div class="custom-control custom-radio custom-control-inline">
							<input type="radio" id="an2024" name="reference" class="custom-control-input" value="2024" checked>
							<label class="custom-control-label" for="an2024">2024</label>
						</div>
						<div class="custom-control custom-radio custom-control-inline">
							<input type="radio" id="an2023" name="reference" class="custom-control-input" value="2023">
							<label class="custom-control-label" for="an2023">2023</label>
						</div>
						<div class="custom-control custom-radio custom-control-inline">
							<input type="radio" id="an2022" name="reference" class="custom-control-input" value="2022">
							<label class="custom-control-label" for="an2022">2022</label>
						</div>
						<div class="custom-control custom-radio custom-control-inline">
							<input type="radio" id="an2021" name="reference" class="custom-control-input" value="2021">
							<label class="custom-control-label" for="an2021">2021</label>
						</div>
						<div class="custom-control custom-radio custom-control-inline">
							<input type="radio" id="an2020" name="reference" class="custom-control-input" value="2020">
							<label class="custom-control-label" for="an2020">2020</label>
						</div>
						<div class="custom-control custom-radio custom-control-inline">
							<input type="radio" id="an2019" name="reference" class="custom-control-input" value="2019">
							<label class="custom-control-label" for="an2019">2019</label>
						</div>
						<div class="custom-control custom-radio custom-control-inline">
							<input type="radio" id="an2018" name="reference" class="custom-control-input" value="2018">
							<label class="custom-control-label" for="an2018">2018</label>
						</div>
						<div class="custom-control custom-radio custom-control-inline">
							<input type="radio" id="an2017" name="reference" class="custom-control-input" value="2017">
							<label class="custom-control-label" for="an2017">2017</label>
						</div>
					</div>
					<div class="col-md-1">
						<i class="fas fa-info-circle" data-toggle="tooltip" data-html="true" title="L'année de référence sert à afficher le rang du dernier de cette année là (c'est le rang limite publié par le CNG Santé). Elle correspond à l'année de publication au Journal Officiel.<br/>L'année sert également à filtrer les spécialités et CHU accessibles lorsque le &apos;Rang ECN&apos; est saisi."></i>
					</div>
				</div>

				<!-- type de spécialité -->
				<br/>
				<div class="row">
					<div class="col-md-1">
					</div>
					<div class="col">
						Type de spécialité :
					</div>
					<div class="col">
						<select class="custom-select" id="type" name="type">
							<option id="typeIndifferent" value="typeIndifferent" selected>indifférent</option>
							<option id="chirurgie" value="chirurgie">chirurgie</option>
							<option id="medico-chirurgical" value="medico-chirurgical">médico-chirurgical</option>
							<option id="organe" value="organe">médecine d'organe</option>
							<option id="transversal" value="transversal">médecine transversale</option>
						</select>
					</div>
					<div class="col-md-1">
						<i class="fas fa-info-circle" data-toggle="tooltip" data-html="true" title="Le type de spécialité ici est donné pour simplifier la réflexion. Il ne décrit pas complètement les spécialités.<br/>Il existe en effet de nombreuses spécialités médicales avec des gestes ou de la 'petite' chirurgie.<br/>Et inversement, dans les spécialités chirurgicales, la médecine est présente du diagnostic jusqu'au au suivi du patient."></i>
					</div>
				</div>

				<!-- CESP uniquement -->
				<br/>
				<div class="row">
					<div class="col-md-1">
					</div>
					<div class="col">
						Spécialité avec CESP :
					</div>
					<div class="col">
						<div class="custom-control custom-switch">
						  <input type="checkbox" class="custom-control-input" id="cesp" name="cesp">
						  <label class="custom-control-label" for="cesp">CESP uniquement</label>
						</div>
					</div>
					<div class="col-md-1">
						<i class="fas fa-info-circle" data-toggle="tooltip" data-html="true" title="En sélectionnant ce critère, les spécialités affichées seront uniquement celles ayant des postes CESP (Contrat d&apos;Engagement de Service Public) existants pour l'année de référence sélectionnée (pour les années à partir de 2020)."></i>
					</div>
				</div>

				<!-- lieu d'exercice -->
				<br/>
				<div class="row">
					<div class="col-md-1">
					</div>
					<div class="col">
						Lieu d'exercice :
					</div>
					<div class="col">
						<select class="custom-select" id="lieu" name="lieu">
							<option id="lieuIndifferent" value="lieuIndifferent" selected>indifférent</option>
							<option id="hopital" value="hopital">à l'hôpital (ou en clinique)</option>
							<option id="ville" value="ville">en cabinet (en ville)</option>
							<option id="autre" value="autre">autre</option>
						</select>
					</div>
					<div class="col-md-1">
						<i class="fas fa-info-circle" data-toggle="tooltip" data-html="true" title="Toutes les spécialités peuvent s'exercer en libéral ou à l'hôpital, à quelques rares exceptions.<br/>Ici la distinction est faite entre celles nécessitant un plateau technique (à l'hôpital) et celles pouvant s'exercer dans un cabinet autonome (en ville)."></i>
					</div>
				</div>

				<!-- durée de l'internat -->
				<br/>
				<div class="row">
					<div class="col-md-1">
					</div>
					<div class="col">
						Durée de l'internat :
					</div>
					<div class="col">
						<div class="custom-control custom-radio custom-control-inline">
							<input type="radio" id="internatIndifferent" name="internat" class="custom-control-input" value="internatIndifferent" checked>
							<label class="custom-control-label" for="internatIndifferent">indifférent</label>
						</div>
<!-- médecine générale : 4 ans d'internat maintenant
						<div class="custom-control custom-radio custom-control-inline">
							<input type="radio" id="3ans" name="internat" class="custom-control-input" value="3">
							<label class="custom-control-label" for="3ans">3 ans</label>
						</div>
 -->
						<div class="custom-control custom-radio custom-control-inline">
							<input type="radio" id="4ans" name="internat" class="custom-control-input" value="4">
							<label class="custom-control-label" for="4ans">4 ans</label>
						</div>
						<div class="custom-control custom-radio custom-control-inline">
							<input type="radio" id="5ans" name="internat" class="custom-control-input" value="5">
							<label class="custom-control-label" for="5ans">5 ans</label>
						</div>
						<div class="custom-control custom-radio custom-control-inline">
							<input type="radio" id="6ans" name="internat" class="custom-control-input" value="6">
							<label class="custom-control-label" for="6ans">6 ans</label>
						</div>
					</div>
					<div class="col-md-1">
					</div>
				</div>

				<!-- bénéfice -->
				<br/>
				<div class="row mb-4">
					<div class="col-md-1">
					</div>
					<div class="col">
						Bénéfice net :
					</div>
					<div class="col">
						<select class="custom-select" id="benefice" name="benefice">
							<option id="beneficeIndifferent" value="beneficeIndifferent" selected>indifférent</option>
							<option id="benefice60" value="benefice60">&le; 60 k€</option>
							<option id="benefice100" value="benefice100">60 - 100 k€</option>
							<option id="benefice140" value="benefice140">100 - 140 k€</option>
							<option id="benefice500" value="benefice500">&ge; 140 k€</option>
						</select>
					</div>
					<div class="col-md-1">
						<i class="fas fa-info-circle" data-toggle="tooltip" data-html="true" title="Il s'agit du bénéfice net comptable moyen des secteurs 1 et 2 confondus, pour les spécialités pouvant s'exercer en libéral.<br/>Les chiffres datent de 2021 et 2020 (sources UNASA et CARMF)."></i>
					</div>
				</div>

			</div>

			<!-- validation du formulaire -->
			<br/><br/>
			<nav class="border p-2">
				<h2 class="h4" style="text-align:left;">2 - Visualiser les spécialités</h2>

				<div class="text-center">
					<div>
						<em>Saisissez (éventuellement) des critères pour voir les <strong>spécialités</strong> ou <strong>CHU</strong> accessibles</em>
						<br/><button name="specialite" type="submit" class="btn btn-primary mt-1 mb-5" value="specialite">Voir les spécialités</button>
						&nbsp; &nbsp; &nbsp; <button name="CHU" type="submit" class="btn btn-primary mt-1 mb-5" value="CHU">Voir les CHU</button>
					</div>
				</div>
	<!-- A ACTIVER PENDANT LA PHASE DE CHOIX DE POSTE -->
<!-- 
				<div class="text-center">
					<em>Saisissez le rang ECN visé ou obtenu pour mieux voir les <strong>rangs limites</strong> accessibles</em>
					&nbsp;&nbsp;<i class="fas fa-info-circle" data-toggle="tooltip" data-html="true" title="Les rangs limites 2023 sont issus des simulations et affectations CELINE."></i>
					<br/><button name="rangLimite" type="submit" class="btn btn-primary mt-1 mb-5" value="rangLimite">Voir la simulation 2023</button>
				</div>
 -->
	<!-- FIN CODE A ACTIVER --> 
				<div class="text-center">
					<em>Effacer tous les critères pour recommencer une nouvelle recherche</em>
					<br/><button name="reset" type="reset" class="btn btn-info mt-1 mb-5">Effacer les critères</button>
				</div>
			</nav>
		</form>

	</div>

	<footer style='margin-top:80px;'>
	</footer>

	<?php
		// librairies javascript nécessaires à l'application (jquery + popper + bootstrap)
		include "php/librairie.php";
	?>
	
	<script>

		window.onload = function () {
		
		// positionnement des select et radio à partir des paramètres passés à l'URL
		<?php
			if (($rang <> "") and ($rang <> "0")) {echo "document.getElementById('rang').value = '" . $rang . "';";}
			if ($reference <> "") {echo "document.getElementById('an".$reference."').checked = true;";}
			if ($type <> "") {echo "document.getElementById('".$type."').selected = true;";}
			if ($cesp == "on") {echo "document.getElementById('cesp').checked = true;";}
			if ($lieu <> "") {echo "document.getElementById('".$lieu."').selected = true;";}
			if ($internat <> 0) {
				if ($internat <> "internatIndifferent") {
					echo "document.getElementById('".$internat."ans').checked = true;";
				} else {
					echo "document.getElementById('internatIndifferent').checked = true;";
				}
			}
			if ($benefice <> "") {echo "document.getElementById('".$benefice."').selected = true;";}
		?>
		}
	</script>
	
	<!-- activation tooltip bootstrap -->
	<script>
		$(function () {
			$('[data-toggle="tooltip"]').tooltip()
		})
	</script>

  </body>
</html>