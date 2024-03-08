<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Simulateur en ligne pour choisir une spécialité d'internat - spécialités et CHU accessibles en fonction du rang">
	<link rel="canonical" href="https://loic.website/ECN/tableau-specialite.php" />

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
    
    <title>Rangs limites CELINE actualisés</title>

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
			top: 57px;
			text-align:center;
			z-index:1;
		}
		td {
			text-align:right;
			padding-right:6px;
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
		}
	</style>

  </head>
  <body id="hautdepage" class="m-2">

	<?php
		include "php/menu-questionnaire.php";
	?>
	
	<nav style='margin-top:80px;' aria-label="breadcrumb">
	  <ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="choix-specialite-chu-celine-ecn.php"><i class="fas fa-home"></i></a></li>
		<li class="breadcrumb-item"><a href="#" onclick="questionnaire()">Questionnaire</a></li>
		<li class="breadcrumb-item active" aria-current="page">Simulation 2023</li>
	  </ol>
	</nav>
		
	<?php

		// recherche du nombre de candidats validés et ayant simulé dans la page listing intégral
		
		// initialisation des variables
		$record = 0;
		$debut = 0;
		$fin = 0;
		$valide = 0;
		$affecte = 0;
		$simule = 0;

		// préparation de la connexion SSL pour les ouverture de page Web du CNG
		// comme ils ont un problème de certificats, on désactive le contrôle du certificat
		// (voir https://stackoverflow.com/questions/26148701/file-get-contents-ssl-operation-failed-with-code-1-failed-to-enable-crypto)
		$arrContextOptions=array(
			"ssl"=>array(
				"verify_peer"=>false,
				"verify_peer_name"=>false,
			),
		);  

		// récupération de la page 'listing intégral'
		$page = fopen("https://www.cngsante.fr/chiron/celine/listing.html","r", false, stream_context_create($arrContextOptions)); //lecture du fichier

		if ($page != false) {
			while (!feof($page)) { //on parcourt toutes les lignes
				$ligne = fgets($page, 4096); // lecture du contenu de la ligne
				$record += 1;

				// recherche du nombre de candidats validés
				// exemple de ligne :
				//   <td colspan="11">8958 validés, CESP inclus = 9299 étudiants - 273 invalidations - 68 ESSA</td></tr>
				
				if (strpos($ligne,"validés")) {
					$debut = strpos($ligne, ">") + 1;
					$fin = strpos($ligne, " validés");
					$longueur = $fin - $debut;
					$valide = substr($ligne, $debut, $longueur);

				// recherche du nombre d'affectés
				// exemple de ligne :
				//  <td colspan="11">0 affectés, CESP inclus</td></tr>
				
				} elseif (strpos($ligne,"affectés")) {
					$debut = strpos($ligne, ">") + 1;
					$fin = strpos($ligne, " affectés");
					$longueur = $fin - $debut;
					$affecte = substr($ligne, $debut, $longueur);
				
				// recherche du nombre de candidats ayant simulé
				// exemple de ligne :
				//  <td colspan="11">6251 simulés, CESP inclus</td></tr>
				
				} elseif (strpos($ligne,"simulés")) {
					$debut = strpos($ligne, ">") + 1;
					$fin = strpos($ligne, " simulés");
					$longueur = $fin - $debut;
					$simule = substr($ligne, $debut, $longueur);
					break;
				}
			}
		}

		// fonctions communes et récupération-contrôle des paramètres
		include "php/controleParametre.php";
		include "php/fonctionECN.php";
		
		// affichage du rang saisi
		echo "<h1 class='h4' style='margin-left:40px;' >";
		echo "Rangs limites CELINE 2023 actualisés  <small>(phase d'affectation du 29 août au 15 septembre)</small>";
		echo "</h1>";

		// affichage du tableau des rangs
		echo "<br/><div id='rang'>";

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
		$debut = 0;
		$fin = 0;
		$dispo = 0;
		$place = 0;
		$offre = 0;

		// récupération de la page rangs limites
		$page = fopen("https://www.cngsante.fr/chiron/celine/limite.html","r",false, stream_context_create($arrContextOptions)); //lecture du fichier
//		$page = fopen("site-en-maintenance.html","r"); //lecture du fichier de test (pour simuler le site CNG en maintenance)

		// ******** format de la page lue
		//
		// ******** cellule renseignée
		// </span></a></td>  <td class="rn"><a href="https://www.cngsante.fr/chiron/celine/affect/norm/020437" target="norm020437" class="limite">3225<br>
		// 5017<span>dispo : 0<br>
		// placé : 3<br>
		// offre : 3<hr>
		// STRASBOURG<br>
		// Médecine intensive-réanimation<br>
		// 
		// ********* cellule vide
		// </span></a></td>  <td class="rn"><a href="https://www.cngsante.fr/chiron/celine/affect/norm/020438" target="norm020438" class="limite">&nbsp;&nbsp;&nbsp;&nbsp;<br>
		// &nbsp;&nbsp;&nbsp;&nbsp;<span>aucune offre<hr>
		// STRASBOURG<br>
		// Médecine légale et expertises médicales<br>
		//
		// ********* cas spécial de la 1ère colonne
		//   <td class="rg">STRASBOURG</td>  <td class="rn"><a href="https://www.cngsante.fr/chiron/celine/affect/norm/020457" target="norm020457" class="limite">&nbsp;&nbsp;&nbsp;&nbsp;<br>
		// &nbsp;&nbsp;&nbsp;&nbsp;<span>aucune offre<hr>
		// STRASBOURG<br>
		// En attente de publication<br>

// en attendant que le record avec l'heure de la simulation soit affiché
    	date_default_timezone_set('Europe/Paris');		// Définir le fuseau horaire
    	$date = date('d/m/y');							// Obtenir la date et l'heure actuelles
    	$heure = date('H:i');
		echo "<p style='margin-left:40px;'>";
		if (($rang != "") and ($rang != null) and ($rang != 0) and ($rang != "rangIndifferent")) {
			echo "<span style='background-color:pink;'>En rose</span> les Spécialités-CHU accessibles pour le rang saisi = " . getLibelleRang($rang) . "<br/>";
		}
		echo "<span class='text-primary'>En bleu</span> les Spécialités-CHU pour lesquelles il y a encore des postes disponibles.<br/>";
		echo "Rafraîchir la page <i class='fas fa-redo'></i> pour actualiser.";
		echo "<br/>Double cliquer <i class='far fa-hand-pointer'></i> sur un rang pour afficher le détail CELINE.";
		echo "<br/><br/>" . $valide . " étudiants validés. " . $affecte . " affectés. " . $simule . " simulés" . ".</p>";
		echo "\n";
		echo "<table class='table-hover table-bordered' style='width:100%;'><thead><tr>";
		echo "<th><small> données CELINE <br>" . $date . "<br>à " . $heure . "</small></th>";
// fin code temporaire

		if ($page != false) {
			while (!feof($page)) { //on parcourt toutes les lignes
				$ligne = fgets($page, 4096); // lecture du contenu de la ligne

				// récupération de la date et heure du listing
				if (strstr($ligne,"font-size : 12px ; font-weight : bold ; color : blue")) {
// a activer quand le 1er record indiquer l'heure
// 					$debutString = strpos($ligne,"</span>") - 5;
// 					$heure = substr($ligne,$debutString,5);		
// 					$date = substr($ligne,$debutString +12,8);
// 					echo "<p style='margin-left:40px;'>";
// 					if (($rang != "") and ($rang != null) and ($rang != 0) and ($rang != "rangIndifferent")) {
// 						echo "<span style='background-color:pink;'>En rose</span> les Spécialités-CHU accessibles pour le rang saisi = " . getLibelleRang($rang) . "<br/>";
// 					}
// 					echo "<span class='text-primary'>En bleu</span> les Spécialités-CHU pour lesquelles il y a encore des postes disponibles.<br/>";
// 					echo "Rafraîchir la page <i class='fas fa-redo'></i> pour actualiser.";
//// A ACTIVER PENDANT LA PHASE DE CHOIX DE POSTE
//					echo "<br/>Double cliquer <i class='far fa-hand-pointer'></i> sur un rang pour afficher le détail CELINE.";

////					echo "<br/><br/>" . $simule . " étudiants ayant simulé sur " . $valide . " étudiants validés. " . $affecte . " affectés.</p>";
// 					echo "<br/><br/>" . $valide . " étudiants validés. " . $affecte . " affectés. " . $simule . " simulés" . ".</p>";
// 					echo "\n";
// 					echo "<table class='table-hover table-bordered' style='width:100%;'><thead><tr>";
// 					echo "<th><small> données CELINE " . str_replace("?","û", mb_convert_encoding($date, "UTF-8")) . " à " . $heure . "</small></th>"; // contournement des caractères accentués du mois d'août qui s'affichent mal
// fin
				}

				// ligne d'entête : les code spécialités sont juste avant une balise <span> et sur la 1ère ligne qui a class="bleu". Boucle sur toute la ligne.
				elseif (strstr($ligne,'class="bleu"') and ($enteteLu == false)) {
					$enteteLu = true;
					$nbSpecialite = substr_count($ligne,"<span>");
					while ($nbSpecialite > 0) {
						$debutString = strpos($ligne,"<span>") - 3;
						$codeSpecialite = substr($ligne,$debutString,3);
						if ($codeSpecialite == ">BM") {
							$codeSpecialite = "&nbsp;&nbsp;BM&nbsp;";
							$libelleSpecialite = getLibelleSpecialite("BM");
						} else {
							$libelleSpecialite = getLibelleSpecialite($codeSpecialite);
						}
						$tooltip = " data-toggle='tooltip' data-delay='20' title='" . $libelleSpecialite . "'";
						echo "<th " . $tooltip . ">&nbsp;" . $codeSpecialite . "&nbsp;</th>";
						$ligne = substr($ligne,$debutString + 8);
						$nbSpecialite = $nbSpecialite - 1;	
					}
					echo "<th style='padding-right:600px; background-color:white; border-style:hidden;'>&nbsp;</th></tr></thead><tbody>\n";
					$premiereLigne = true;
				}

				// 1ère ligne d'une cellule
				elseif (strstr($ligne,'<td class="rn">')) {
					$numeroRecord = 1;
					$debutString = strpos($ligne,"href=");
					$longueurString = strpos($ligne,"target") - $debutString;
					$lien = substr($ligne, $debutString, $longueurString);
				}

				// 2ème ligne d'une cellule vide
				elseif (strstr($ligne,"aucune offre")) {
					$numeroRecord = 2;
					$rangDernier = 0;
					$texteRangDernier = "";
					$aucuneOffre = true;
					$nextIsCHU = true;
				}

				// 2ème ligne d'une cellule non vide
				elseif (strstr($ligne,"<span>dispo")) {
					$numeroRecord = 2;
					$longueurString = strpos($ligne,"<span>dispo");
					$rangDernier = substr($ligne, 0, $longueurString);
					$texteRangDernier = $rangDernier;
					$debut = strpos($ligne, " : ") +3;
					$fin = strpos($ligne, "<br>");
					$dispo = substr($ligne, $debut, $fin - $debut);
				}

				// 3ème ligne d'une cellule non vide
				elseif (substr($ligne,0,4) == "plac") {
					$numeroRecord = 3;
					$debut = strpos($ligne, "placé : ") +8;
					$fin = strpos($ligne, "<br>");
					$place = substr($ligne, $debut, $fin - $debut);
				}

				// 4ème ligne d'une cellule non vide
				elseif (substr($ligne,0,7) == "offre :") {
					$numeroRecord = 4;
					$debut = strpos($ligne, "offre : ") +8;
					$fin = strpos($ligne, "<hr>");
					$offre = substr($ligne, $debut, $fin - $debut);
					$nextIsCHU = true;
				}

				// 5ème ligne d'une cellule non vide ou 3ème ligne d'une cellule vide
				elseif ($nextIsCHU) {
					$numeroRecord = 5; // ou 3 si aucune offre
					$nextIsCHU = false;
					$longueurString = strpos($ligne,"<br>");
					$CHU = substr($ligne, 0, $longueurString);
					$nextIsSpecialite = true;
				}

				// 6ème et dernière ligne d'une cellule non vide ou 4ème et dernière ligne d'une cellule vide
				elseif ($nextIsSpecialite) {
					$numeroRecord = 6; // ou 4 si aucune offre
					$nextIsSpecialite = false;
					$longueurString = strpos($ligne,"<br>");
					$specialite = substr($ligne, 0, $longueurString);

				// affichage de la cellule
					$href = "";
// A ACTIVER PENDANT LA PHASE DE CHOIX DE POSTE
					$href = " onclick='return false' ondblclick='window.open(" . $lien . ", &apos;Fenêtre CELINE&apos;)' ";

					if ($aucuneOffre) {
						$aucuneOffre = false;
						$rangDernier = "";	
						$tooltip = " data-toggle='tooltip' data-html='true' title='".$CHU."<br/>".$specialite."<br/>Aucune offre'";
					} else {
						$tooltip = " data-toggle='tooltip' data-delay='20' data-html='true' title='".$CHU."<br/>".$specialite."<br/>Dernier admis : ".$rangDernier."<br/>Postes : ".$offre."<br/>Placés : ".$place."<br/>Disponibles : ".$dispo."'";
					}
					
					// les spécialité-CHU encore disponible en texte bleu
					if ($dispo > 0) {
						$texteRangDernier = "<span class='text-primary'>" . $texteRangDernier . "</span>";
					}
					
					// les spécialités-CHU accessibles sur fond rose
					if (($rangDernier >= $rang) and ($rangDernier != 0)) {
						if  ($CHU == $lastCHU) {
							echo "<td style='background-color:pink;'" . $tooltip . $href . ">". $texteRangDernier . "</td>";
						} else {
							$lastCHU = $CHU;
							if (!$premiereLigne) {
								echo "</tr>\n";
							} else {
								$premiereLigne = false;
							}
							echo "<tr><td style='text-align:center;'>" . $CHU . "</td><td style='background-color:pink;'" . $tooltip . $href . ">" . $texteRangDernier . "</td>";
						}
					} else {
						if  ($CHU == $lastCHU) {
							echo "<td " . $tooltip . $href . ">" . $texteRangDernier . "</td>";
						} else {
							$lastCHU = $CHU;
							if (!$premiereLigne) {
								echo "</tr>\n";
							} else {
								$premiereLigne = false;
							}
							echo "<tr><td style='text-align:center;'>" . $CHU . "</td><td " . $tooltip . $href . ">" . $texteRangDernier . "</td>";		
						}
					}
				}
			}
		}

// en attendant que le record avec l'heure de la simulation soit affiché
//		if ($heure != "00h00") {
			echo "</tbody></table>";
// 		} else {
// 			echo "<div class='container'><p class='bg-info text-white h5 text-center' style='padding:10px;'>Site CELINE CNG en maintenance pour l'instant.<br/><small>Revenez plus tard...</small></p>";
// 			echo "<br/><br/><p>Vous pouvez aussi consulter les dernières données enregistrées en choisissant <a href='liste-specialite.php?reference=2023'>Voir les spécialités</a> dans le <a href='questionnaire-choix-specialite.php'>questionnaire</a>.</p></div>";
// 		}
// fin mise en commentaire temporaire

	?>
		
		<!-- retour en arrière vers le formulaire -->
		<br/><br/>
		<p class=text-center>
			<button class="btn btn-primary" onclick="questionnaire()">&larr; Retour au questionnaire</button>
		</p>

	</div>

	<footer style='margin-top:80px;'>
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
		
		// pour retourner en arrière dans l'historique du navigateur
		function questionnaire() {
			<?php
				echo "window.location.href='questionnaire-choix-specialite.php?rang=" . $rang . "&reference=" . $reference . "&type=" . $type . "&lieu=" . $lieu . "&internat=" . $internat . "&benefice=" . $benefice . "';";
			?>
		}

		// pour zoomer sur une spécialité
		function zoom(code) {
			<?php
				echo "window.location.href='detail-specialite-questionnaire.php?rang=" . $rang . "&reference=" . $reference . "&code=' + code + '" . "&type=" . $type . "&lieu=" . $lieu . "&internat=" . $internat . "&benefice=" . $benefice . "';";
			?>
		}
	</script>
  </body>
</html>