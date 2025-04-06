<!doctype html>
<html lang="fr" prefix="og: http://ogp.me/ns#">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Simulateur pour choisir sa spécialité et son CHU pour l'internat après l'ECN et saisir ses voeux sous CNG - CELINE">

	<?php
		// favicons générés par https://realfavicongenerator.net
		include "php/favicon.php";
	?>

	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=ID-GOOGLE"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());

	  gtag('config', 'G-4NC0K56D5R');
	</script>
    
    <title>Simulateur Excel pour choisir sa spécialité et son CHU à l'ECN</title>

	<meta property="og:title" content="Comment choisir sa spécialité d'internat post ECN ?" />
	<meta property="og:description" content="Le simulateur peut vous aider à choisir votre spécialité médicale ou chirurgicale. A partir de votre rang espéré, le simulateur met en évidence toutes les spécialités et CHU accessibles." />
	<meta property="og:type" content="article" />
	<meta property="og:url" content="https://loic.website/ECN/choix-specialite-chu-celine-ecn.php" />
	<meta property="og:site_name" content="Simulateur choix spécialités ECN" />
	<meta property="og:image" content="https://loic.website/ECN/image/interne.jpg" />
	<meta property="og:image:type" content="image/jpg" />
	<meta property="og:image:alt" content="Choix de spécialité d'internat" />
    
	<?php
		// styles nécessaires à l'application (bootstrap + fontawasome + ECN)
		include "php/style.php";
	?>
	
	<style>
		td {
			cursor: default;
		}
	</style>
	
  </head>
  <body id="hautdepage" data-spy="scroll" data-target="#navigation" data-offset="0">
	
	<nav id="navigation" class="navbar fixed-top navbar-expand-lg navbar-dark bg-secondary">
	  <a class="navbar-brand" href="#"><img src="image/stethoscopeBlanc.svg" width="30" height="30" alt="logo stéthoscope blanc pour choix de spécialités d'internat'" loading="lazy"> &nbsp;Simulateur spécialités</a>
	  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#menu" aria-controls="menu" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	  </button>

	  <div id="menu" class="collapse navbar-collapse" data-toggle="collapse" data-target=".show">
		<ul class="navbar-nav mr-auto">
		  <li class="nav-item">
			<a class="nav-link" href="#telecharger">Télécharger</a>
		  </li>
		  <li class="nav-item">
			<a class="nav-link" href="#apercu">Aperçu</a>
		  </li>
		   <li class="nav-item">
			<a class="nav-link" href="#qui">Pour qui</a>
		  </li>
		  <li class="nav-item">
			<a class="nav-link" href="#fonctionnement">Fonctionnement</a>
		  </li>
		  <li class="nav-item">
			<a class="nav-link" href="#specialite">Spécialités</a>
		  </li>
		  <li class="nav-item">
			<a class="nav-link" href="questionnaire-choix-specialite.php">En ligne</a>
		  </li>
		  <li class="nav-item">
			<a class="nav-link" href="#propos">A propos</a>
		  </li>
		  <li class="nav-item">
			<a class="nav-link" href="#contact">Contact</a>
		  </li>
		</ul>
	  </div>
	</nav>

  	<header id="simulateur" class="container">
		<p style="text-align:center; padding-top:88px;">
			<img src="image/interne.jpg" alt="choisir sa spécialité d'interne en médecine" class="img-fluid">
		</p>
    	<h1 style="text-align:center" class="h3">Simulateur de choix de <strong>spécialités</strong> et de <strong>CHU</strong> pour l'internat</h1>
	</header>

	<div id="actualite" class="container ancre">
		<br/>
		<p class="text-center">
			<mark style="color:#808080;"><strong>Actualités</strong> : toutes les versions du questionnaire (<a href="questionnaire-choix-specialite.php" title="aide au choix de spécialités et CHU en ligne">simulateur en ligne</a>, Excel et Open Office) intègrent désormais le rang du dernier admis par CHU et par Spécialité de l'EDN ECOS 2024.
			</mark>
		</p>
	</div>
	
	<div id="telecharger" class="container ancre">
		<br/>
    	<h2 class="h4" style="text-align:left">Télécharger le simulateur de choix</h2>
		<br/>
		<p class="text-center h5"><a href="php/download.php?file=Simulation.xlsx" title="télécharger le simulateur de choix de spécialités Excel"><i class="fas fa-file-download"></i> &nbsp; &nbsp; Télécharger la version Excel</a></p>
		<p class="text-center"><i>Le mot de passe pour ouvrir le simulateur Excel est "ecn"</i>.<br/>
		<?php
			$nomFichier = "compteurXLS.txt";
			$fichier = fopen ($nomFichier, "r");
			$record = fread($fichier, filesize($nomFichier));
			fclose ($fichier);
			echo "(" . $record . " téléchargements effectués)";
		?>
		</p>
		<br/>
		<p class="text-center h5"><a href="php/download.php?file=Simulation.ods" title="télécharger le simulateur de choix de spécialités Open Office"><i class="fas fa-file-download"></i> &nbsp; &nbsp; Télécharger la version Open Office</a></p>
		<p>Pour ceux qui n'ont pas Excel, une version Open Office est également disponible. Elle offre exactement les mêmes fonctionnalités.</p>
		<p class="text-center"><i>Le mot de passe pour ouvrir le simulateur Open Office est également "ecn"</i>.<br/>
		<?php
			$nomFichier = "compteurODS.txt";
			$fichier = fopen ($nomFichier, "r");
			$record = fread($fichier, filesize($nomFichier));
			fclose ($fichier);
			echo "(" . $record . " téléchargements effectués)";
		?>
		</p>
		<br/>
		<p class="text-center h5"><a href="questionnaire-choix-specialite.php" title="aide au choix de spécialités et CHU en ligne"><i class="fas fa-mobile-alt"></i> &nbsp; &nbsp; Simulateur en ligne</a></p>
		<p>Une version simplifiée est disponible <strong>en ligne</strong> (pour ceux qui n'ont ni Excel ni OpenOffice). Elle permet d'explorer rapidement les différentes spécialités et de connaître pour chacune d'entre elles le rang du dernier admis pour chaque CHU ainsi que le nombre de postes. Ce simulateur en ligne est particulièrement adapté aux <strong>smartphones</strong>.</p>
		<br/>
	</div>

  	<div id="paiement" class="container">
		<p><strong>Le simulateur est gratuit</strong>, ne comporte pas de publicité et ne collecte aucune donnée. Ce n’est pas une démarche commerciale. Je mets en ligne ce simulateur simplement pour aider les étudiants à réfléchir sur leur choix de spécialités.</p>
	</div>

	<div id="apercu" class="container ancre">
		<br/><br/>
		<h2 style="text-align:left;" class="h4">Aperçu du simulateur</h2>
		<br/>
		<div id="carouselExampleIndicators" style="margin:auto;" class="carousel slide text-center col-md-8 col-lg-8" data-ride="carousel">
		  <ol class="carousel-indicators">
			<li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
			<li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
			<li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
			<li data-target="#carouselExampleIndicators" data-slide-to="3"></li>
			<li data-target="#carouselExampleIndicators" data-slide-to="4"></li>
			<li data-target="#carouselExampleIndicators" data-slide-to="5"></li>
			<li data-target="#carouselExampleIndicators" data-slide-to="6"></li>
			<li data-target="#carouselExampleIndicators" data-slide-to="7"></li>
			<li data-target="#carouselExampleIndicators" data-slide-to="8"></li>
		  </ol>
		  <div class="carousel-inner">
			<div class="carousel-item active">
				<img class="img-fluid" src="image/Carte.png" alt="Carte des CHU et interRégions">
				<div class="carousel-caption">Simulateur Excel - Carte des CHU</div>
			</div>
			<div class="carousel-item">
				<img class="img-fluid" src="image/Specialite.png" alt="Sélection des spécialités d'internat médicales et chirurgicales">
				<div class="carousel-caption">Simulateur Excel - Liste des spécialités d'internat</div>
			</div>
			<div class="carousel-item">
				<img class="img-fluid" src="image/Poste.png" alt="Nombre de postes dipsonibles par spécialités d'internat et CHU">
				<div class="carousel-caption">Simulateur Excel - Nombre de postes d'internat par spécialité et CHU</div>
			</div>
			<div class="carousel-item">
				<img class="img-fluid" src="image/Rang.png" alt="Rang du dernier admis à l'ECN par spcécialité d'internat et CHU'">
				<div class="carousel-caption">Simulateur Excel - Rang derniers admis à l'ECN par spécialité et par CHU</div>
			</div>
			<div class="carousel-item">
				<img class="img-fluid" src="image/Choix.png" alt="Choix des spécialités d'internat et des CHU souhaités par le futur interne de médecine">
				<div class="carousel-caption">Simulateur Excel - Spécialités et CHU visés à l'ECN</div>
			</div>
			<div class="carousel-item">
				<img class="img-fluid" src="image/Synthese.png" alt="Synthèse des spécialités d'internat et des CHU accessibles par le futut inrene en fonction de son rang à l'ECN">
				<div class="carousel-caption">Simulateur Excel - Spécialités et CHU accessibles pour un rang à l'ECN</div>
			</div>
			<div class="carousel-item">
				<img class="img-fluid" src="image/EnLigneCritereListe.png" alt="Critères de sélection des spécialités d'internat">
				<div class="carousel-caption">Simulateur en ligne - Critères de sélection et spécialités correspondantes</div>
			</div>
			<div class="carousel-item">
				<img class="img-fluid" src="image/EnLigneListeDetail.png" alt="Liste et détail des spécialités d'internat correspondant aux critères saisis">
				<div class="carousel-caption">Simulateur en ligne - Liste et détail de la spécialité sélectionnée</div>
			</div>
			<div class="carousel-item">
				<img class="img-fluid" src="image/EnLigneRang.png" alt="Affichage des rangs limites CELINE pour le rang saisi">
				<div class="carousel-caption">Simulateur en ligne - Affichage actualisé des rangs limites CELINE</div>
			</div>
		  </div>
		  <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
			<span class="carousel-control-prev-icon" aria-hidden="true"></span>
			<span class="sr-only">Précédent</span>
		  </a>
		  <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
			<span class="carousel-control-next-icon" aria-hidden="true"></span>
			<span class="sr-only">Suivant</span>
		  </a>
		</div>
	</div>

	<div id="qui" class="container ancre">
		<br/><br/>
		<h2 style="text-align:left" class="h4">A qui s'adresse ce simulateur</h2>
		<div>
			<br/>
			<p style="margin-bottom:0;">Etudiant(e) en médecine, externe de 4ème, 5ème ou 6ème année, vous vous posez ce type de questions :</P> 
			<div class="row">
				<div class="col-md-5"> 
					<ul style="margin-left:2.5%;">
						<li><strong>Comment choisir sa spécialité</strong> ?</li>
						<li><strong>Comment choisir son CHU</strong> ?</li>
						<li>Faut-il attendre son classement pour <strong>établir ses voeux CNG</strong> ?</li>
						<li>Comment être sûr d'avoir considéré toutes les <strong>spécialités médicales et chirurgicales</strong> ?</li>
						<li>Faut-il réfléchir à ses voeux <strong>avant les EDN/ECOS</strong> ? Avant ou après son <strong>classement</strong> ?</li>
					</ul>
				</div>
				<div class="col-md-5 offset-md-1"> 
					<ul style="margin-left:2.5%;">
						<li>Quelle <strong>spécialité</strong> finalement choisir ?</li>
						<li>Quel <strong>CHU</strong> finalement choisir ?</li>
						<li>A quelle <strong>spécialité puis-je prétendre</strong> ?</li>
						<li>A quel <strong>CHU puis-je prétendre</strong> ?</li>
						<li>Comment <strong>prioriser mes voeux</strong> pour <strong> CNG</strong> ?</li>
						<li>Puis-je <strong>rêver</strong> ou seulement être <strong>réaliste</strong> dans mes voeux ?
					</ul>
				</div>
			</div>
			<br/>
			<p style="margin-bottom:0;">Ce simulateur peut vous aider à réfléchir et à y répondre. Son fonctionnement est simple :</p>
			<ol>
				<li>vous indiquez le <strong>rang</strong> que vous pensez (ou voulez) obtenir aux <strong>EDN/ECOS</strong>,</li>
				<li>et le simulateur affiche les <strong>CHUs et les spécialités accessibles</strong>.</li>
			</ol>
		</div>
	</div>

	<div id="fonctionnement" class="container ancre">
		<br/><br/>
		<h2 style="text-align:left" class="h4">Comment fonctionne ce simulateur ?</h2>
		<br/>
		<div>
			<p>Le simulateur (Excel et Open Office) contient le rang du dernier admis pour toutes les spécialités et tous les CHU sur les années 2023 à 2017 (les données sont issues de CNG - Céline).</p>
			<p>A partir de votre rang espéré, le simulateur met en évidence toutes les spécialités et CHU accessibles.</p>
			
			<p style="margin-bottom:0;">Comment l'utiliser :</p>
			<ol>
				<li>Vous indiquez le <strong>rang</strong> que vous pensez (ou voulez) obtenir aux<strong>EDN/ECOS</strong> dans l'onglet <span class="badge badge-info">2 - Rang candidat</span>.</li>
				<li>Le simulateur affiche les <strong>CHUs et les spécialités accessibles</strong> dans les onglets <span class="badge badge-info">Spécialité</span>, <span class="badge badge-info">Rang 2023</span> à <span class="badge badge-info">Rang 2017</span>, <span class="badge badge-info">Poste 2023</span> à <span class="badge badge-info">Poste 2020</span>, <span class="badge badge-info">CESP 2023</span> à <span class="badge badge-info">CESP 2020</span>. A vous de naviguer, d'analyser et de réfléchir.</li>
			</ol>
			<p><mark style="background-color:Khaki; color:#808080;">Note : seules les cellules avec fond jaune sont saisissables.</mark></p>
			<br/>
						
			<p style="margin-bottom:0;">Pour cibler vos spécialités, et notamment être sûr que vous les avez toutes passées en revue (sans a priori), vous pouvez vous aider de l’onglet <span class="badge badge-info">Spécialité</span> qui liste les <strong>44 spécialités d'internat</strong> accessibles après les EDN/ECOS. Ce tableau permet de sélectionner et de trier les spécialités en fonction :</P>
			<ul style="margin-left:2.5%;">
				<li>du type de spécialité : <strong>médicale</strong>, <strong>chirurgicale</strong> ou mixte</li>
				<li>le lieu d’exercice : en <strong>hôpital</strong>, en <strong>libéral</strong> (en ville) ou mixte</li>
				<li>la <strong>durée de l’internat</strong> : 4, 5 ou 6 ans suivant chaque maquette</li>
				<li>un <strong>niveau de rémunération</strong> pour les spécialités s’exerçantes en libéral</li>
				<li>et pour les spécialités médicales : <strong>spécialité d'organes</strong> ou <strong>spécialité transversale</strong>.</li>
			</ul>
			<p>La <a href="#specialite">liste des spécialités</a> utilisées par ce simulateur est donnée en fin de page avec leurs acronymes.</p> 
			<br/>
			
			<p style="margin-bottom:0;">Quand vous avez une petite idée de vos choix possibles, vous pouvez affiner votre simulation tout simplement :</p>
			<ol>
				<li>Vous indiquez vos <strong>spécialités souhaitées</strong> ainsi que vos <strong>CHU visés</strong> dans l'onglet <span class="badge badge-info">1 - Souhait</span>.</li>
				<li>Vous ajustez si besoin votre <strong>rang espéré ou visé</strong> dans l'onglet <span class="badge badge-info">2 - Rang Candidat</span>.</li>
				<li>Le simulateur affiche la <strong>synthèse des choix accessibles</strong> dans l'onglet <span class="badge badge-info">3 - Synthèse</span>.</li>
			</ol>
			<p>Au final vous pouvez avoir un aperçu de la liste de vos voeux dans l'onglet <span class="badge badge-info">4 - Voeux</span>. Et réitérer autant de fois que vous le voulez.</p>
		</div>
	</div>

	<div id="specialite" class="container ancre">
		<br/><br/>
		<h2 style="text-align:left" class="h4">Classification des spécialités pour ce simulateur</h2>
		<br/>		
		<table id="table1" class="table-hover" style="width:100%;">
		<caption style='caption-side:top;'>Cliquer <i class='far fa-mouse-pointer' aria-hidden='true'></i> sur une spécialité pour voir les CHU pour cette spécialité.</caption>
			<thead class="text-center">
				<tr>
					<th colspan=2  style="width:47.5%">Spécialités Chirurgicales</th>
					<th style="width:5%;" class="milieu">&nbsp;</th>
					<th colspan=2 style="width:47.5%;">Spécialités Chirurgicales et Médicales (mixtes)</th>
					<th class="milieu"></th>
				</tr>
				<tr>
					<th colspan=2 class="sousTitre">à l'hôpital</th>
					<th class="milieu sousTitre" style="background-color:transparent;">&nbsp;</th>
					<th colspan=2 class="sousTitre">en libéral / ville (mixte)</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="acronyme">CMF</td>
					<td>Chirurgie maxillo-faciale</td>
					<td class="milieu"></td>
					<td class="acronyme">GYO</td>
					<td class="derniereColonne">Gynécologie obstétrique</td>
					<td class="milieu"></td>
				</tr>
				<tr>
					<td class="acronyme">COR</td>
					<td>Chirurgie orale</td>
					<td class="milieu"></td>
					<td class="acronyme">OPH</td>
					<td class="derniereColonne">Ophtalmologie</td>
					<td class="milieu"></td>
				</tr>
				<tr>
					<td class="acronyme">COT</td>
					<td>Chirurgie orthopédique et traumatologique</td>
					<td class="milieu"></td>
					<td class="acronyme">ORL</td>
					<td class="derniereColonne">Oto-rhino-laryngologie - chirurgie cervico-faciale</td>
					<td class="milieu"></td>
				</tr>
				<tr>  
					<td class="acronyme">CPD</td>
					<td>Chirurgie pédiatrique</td>
					<td class="milieu"></td>
					<td class="acronyme bas">URO</td>
					<td class="bas derniereColonne">Urologie</td>
					<td class="milieu"></td>
				</tr>
				<tr>  
					<td class="acronyme">CPR</td>
					<td>Chirurgie plastique, reconstructrice et esthétique</td>
				</tr>
				<tr>  
					<td class="acronyme">CTC</td>
					<td>Chirurgie thoracique et cardiovasculaire</td>
				</tr>
				<tr>  
					<td class="acronyme">CVA</td>
					<td>Chirurgie vasculaire</td>
				</tr>
				<tr>  
					<td class="acronyme">CVD</td>
					<td>Chirurgie viscérale et digestive</td>
				</tr>
				<tr>  
					<td class="acronyme bas">NCU</td>
					<td class="bas">Neurochirurgie</td>
				</tr>
				<tr><td class="acronyme premiereColonne"></td></tr>
			</tbody>
		</table>
		<br/>

		<table class="table-hover" style="width:100%;">
			<thead class="text-center" style='background-color:Beige'>
				<tr>
					<th colspan=6 style="width:100%;">Spécialités Médicales</th>
					<th class="milieu"></th>
				</tr>
				<tr>
					<th colspan=2 class="sousTitre">à l'hôpital</th>
					<th colspan=2 class="sousTitre">en libéral / ville (mixte)</th>
					<th colspan=2 class="sousTitre">autre</th>
				</tr>
				<tr>
					<th colspan=2 class="type">spécialités d'organes</th>
					<th colspan=2 class="type">spécialités d'organes</th>
					<th colspan=2 class="type">spécialités transversales</th>
				</tr>
			</thead>
			<tbody>

				<tr>
					<td class="acronyme">NEP</td>
					<td>Néphrologie</td>
					<td class="acronyme">DVE</td>
					<td>Dermatologie et vénéréologie</td>
					<td class="acronyme">MTR</td>
					<td class="derniereColonne">Médecine et santé au travail</td>
					<td class="milieu"></td>
				</tr>
				<tr>
					<th colspan=2 class="type">spécialités transversales</th>
					<td class="acronyme">EDN</td>
					<td>Endocrinologie-diabétologie-nutrition</td>
					<td class="acronyme bas">SPU</td>
					<td class="bas derniereColonne">Santé publique</td>
					<td class="milieu"></td>
				</tr>

				<tr>
					<td class="acronyme">ARE</td>
					<td>Anesthésie-réanimation</td>
					<td class="acronyme">GYM</td>
					<td>Gynécologie médicale</td>
				</tr>
				<tr>
					<td class="acronyme">GEN</td>
					<td>Génétique médicale</td>
					<td class="acronyme">HGE</td>
					<td>Hépato-gastro-entérologie</td>
				</tr>
				<tr>  
					<td class="acronyme">HEM</td>
					<td>Hématologie</td>
					<td class="acronyme">MCA</td>
					<td>Médecine cardiovasculaire</td>
				</tr>
				<tr>  
					<td class="acronyme">MII</td>
					<td>Médecine interne et immunologie clinique</td>
					<td class="acronyme">MVA</td>
					<td>Médecine vasculaire</td>
				</tr>
				<tr>  
					<td class="acronyme">MIR</td>
					<td>Médecine intensive-réanimation</td>
					<td class="acronyme">NEU</td>
					<td>Neurologie</td>
				</tr>
				<tr>  
					<td class="acronyme">MIT</td>
					<td>Maladies infectieuses et tropicales</td>
					<td class="acronyme">PNE</td>
					<td>Pneumologie</td>
				</tr>
				<tr>  
					<td class="acronyme">MLE</td>
					<td>Médecine légale et expertises médicales</td>
					<td class="acronyme bas">RHU</td>
					<td class="bas">Rhumatologie</td>
				</tr>
				<tr>  
					<td class="acronyme">MPR</td>
					<td>Médecine physique et de réadaptation</td>
					<th colspan=2 class="type">spécialités transversales</th>
				</tr>
				<tr>  
					<td class="acronyme">MUR</td>
					<td>Médecine d’urgence</td>
					<td class="acronyme">ACP</td>
					<td>Anatomie et cytologie pathologiques</td>
				</tr>
				<tr>  
					<td class="acronyme bas">NUC</td>
					<td class="bas">Médecine nucléaire</td>
					<td class="acronyme">ALL</td>
					<td>Allergologie</td>
				</tr>
				<tr>  
					<td class="premiereColonne"></td>
					<td class="premiereColonne"></td>
					<td class="acronyme">BM</td>
					<td>Biologie médicale</td>
				</tr>
				<tr>  
					<td class="premiereColonne"></td>
					<td class="premiereColonne"></td>
					<td class="acronyme">GER</td>
					<td>Gériatrie</td>
				</tr>
				<tr>  
					<td class="premiereColonne"></td>
					<td class="premiereColonne"></td>
					<td class="acronyme">MGE</td>
					<td>Médecine générale</td>
				</tr>
				<tr>  
					<td class="premiereColonne"></td>
					<td class="premiereColonne"></td>
					<td class="acronyme">ONC</td>
					<td>Oncologie</td>
				</tr>
				<tr>  
					<td class="premiereColonne"></td>
					<td class="premiereColonne"></td>
					<td class="acronyme">PED</td>
					<td>Pédiatrie</td>
				</tr>
				<tr>  
					<td class="premiereColonne"></td>
					<td class="premiereColonne"></td>
					<td class="acronyme">PSY</td>
					<td>Psychiatrie</td>
				</tr>
				<tr>  
					<td class="premiereColonne"></td>
					<td class="premiereColonne"></td>
					<td class="acronyme bas">RAI</td>
					<td class="bas">Radiologie et imagerie médicale</td>
				</tr>
				<tr><td class="acronyme premiereColonne"></td></tr>
			</tbody>
		</table>
		<br/><br/>
		<p>La classification (pour le simulateur) des <strong>spécialités chirurgicales et médicales</strong> peut aussi être parcourue sous forme graphique. Cliquez sur le schéma pour le voir en grand.</p>
		<p class="text-center">
			<a href="image/choix-specialite-internat-ecn.svg" target="_blank"><img class="img-fluid" style="width:80%;" src="image/choix-specialite-internat-ecn.svg" alt="schéma de choix d'une spécialité d'internat à l'ECN"></a>
		</p>
	</div>

	<div id="propos" class="container ancre">		
		<br/><br/>
		<h2 style="text-align:left" class="h4">A propos</h2>
		<br/>
		<div>
			<p>Je suis le père d'un étudiant qui était en 6ème année de médecine (avant son internat). A force de parler avec lui de ses choix de spécialités et de CHU, j'ai écrit ce simulateur pour l'aider dans sa réflexion. Le but de ce simulateur est bien d'anticiper et de <strong>se projeter dans une spécialité et un CHU</strong> sans nécessairement connaître son rang aux EDN/ECOS, et sans attendre les simulations d'appariement. Et sans se censurer. Il faut à la fois rêver (voeux de rêve) et en même temps être réaliste (voeux réalistes et voeux de secours). Je souhaite que ce simulateur soit <strong>utile à d'autres étudiants en médecine</strong>. C'est pourquoi je le partage.</p>
			<p>Les données du simulateur sont issues du CNG Santé pour les rangs des derniers admis (les rangs limites), du guide de l'ISNI pour le détail des spécialités, du journal officiel pour le nombre de postes d'internes et de l'UNASA et de la CARMF pour les revenus des spécialités en libéral.</p> 
			<p>Ce simulateur ne décrit pas les spécialités ni les CHU. Vous pouvez vous référer au guide ISNI pour cela. Ce guide est consultable en cliquant sur le lien :  
			   <a href="https://isni.fr/wp-content/uploads/2023/07/ISNI-GUIDE-2024-web.pdf" target="_blank" download="Futur-Interne-Guide-2024.pdf"><i class="fas fa-file-download"></i> Voir le guide des villes et des spécialités ISNI</a>
			<p>Un grand merci à Léo qui m'a transmis les rangs limites 2024. Je vous conseille d'aller sur son site pour trouver plein de ressources sur les EDN/ECOS : <a href="https://picat.fr/blog.html" target="_blank">Blog Léo Picat<a> ainsi que sur son site de statistiques des simulations de 2024 : <a href="https://picat.shinyapps.io/matchingexplorer/" target="_blank">Matching Explorer</a></p>
			<p><strong>Disclaimer</strong> : et malgré tout le soin apporté au développement de ce site, il peut y avoir des bugs résiduels. Les seules données officielles sont celles du CNG Santé.</p>  
		</div>
	</div>

	<div id="contact" class="container ancre">
		<br/><br/>
		<h2 style="text-align:left" class="h4">Contact</h2>
		<br/>
		<div>
			<p>
				N'hésitez pas à me contacter par mail ou Facebook pour me faire part de vos remarques, suggestions, demandes d'évolution ou bug rencontré. Je m'efforcerai de traiter votre demande dans la mesure de mon temps disponible. Je vous remercie par avance.<br/>
			</p>
		</div>
	</div>

	<footer id="merci" class="container ancre">
		<br/>
		<br/>
		<h2 class="h4">Bonne chance pour les EDN et ECOS puis pour l'internat !</h2>
		<br/>		
		<br/>
	</footer>

	<?php
		// librairies javascript nécessaires à l'application (jquery + popper + bootstrap)
		include "php/librairie.php";
	?>
	
	<script>

		// récupération du code spécialité au click sur la table des spécialités et appel de la page détail
		$(document).ready(function(){
			$('table td').click(function(){
				var cellule = $(this).text();
				if (cellule != '') {
					window.location.href='detail-specialite-simulateur.php?specialite=' + cellule;
				}
			});
		});

	</script>

  </body>
</html>