<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Simulateur en ligne pour choisir une spécialité d'internat - carte de France des effectifs médicaux">

	<?php
		// favicons générés par https://realfavicongenerator.net
		include "php/favicon.php";
	
        // Google Analytics
		include "php/GoogleAnalytics.php";
	?>

    <title>Nombre de médecins</title>
    
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
		text {
			font-size: 18px;
    	}
		path[data-numerodepartement]:hover {
			fill: #86cce0;
		}
		text:hover + path {
			fill: #86cce0;
		}
		@media (max-width: 576px) {
      		text {
        		font-size: 22px;
      		}
    	}
    	text {
    		fill: gray;
    	}

		.legend-color-box {
            display: inline-block;
            width: 20px;
            height: 20px;
            margin-right: 5px;
            border: 1px solid #ccc;
            vertical-align: middle;
        }

		.legend-text {
        	font-size: 0.8em; 
        	color: #707a82;    /* Un gris plus clair que le gris par défaut */
		}

		table {
			border-style:solid;
        	font-size: 0.8em; 
		}

        .btn-same-width {
            width: 130px;
            white-space: nowrap; /* évite le retour à la ligne dans le bouton */
        }

	</style>

  </head>
  <body id="hautdepage">

    <?php
        // menu de l'application, contrôle des paramètres et fonctions communes
        include "php/menu-questionnaire.php";
        require_once "php/controleParametre.php";
        require_once "php/fonctionECN.php";
        include "php/fonctionCarte.php";
    ?>
    
    <header id="chemin">
        <h1 class="h3" style="text-align:center; margin-top:60px;">Effectifs médicaux</h1>
        <div class="container" style="margin-top: 20px;">
            <div class="row align-items-center">
                <div class="col-auto flex-grow-1" aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                        <a href="choix-specialite-chu-celine-ecn.php">
                            <i class="bi bi-house-door-fill"></i>
                        </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Effectifs</li>
                    </ol>
                </div>
                <div class="col-auto">
                    <div class="d-flex flex-nowrap">
                        <button class="btn btn-primary btn-sm mr-2 btn-same-width" onclick="demographie()" title="Affichage de la pyramide des ages des médecins">
                            <i class="bi bi-bar-chart-fill"></i>&nbsp; &nbsp; Ages
                        </button>
                        <button class="btn btn-primary btn-sm mr-2 btn-same-width" onclick="densite()" title="Affichage de la densité médicale">
                            <i class="bi bi-geo-alt-fill"></i>&nbsp; &nbsp; Densité
                        </button>
                        <button class="btn btn-secondary btn-sm btn-same-width" disabled title="Affichage du nombre de médecins">
                            <i class="bi bi-people-fill"></i>&nbsp; &nbsp; Effectifs
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container">

        <div id="critere" class="p-2 mt-4">
            <div class="border row p-3 align-items-center">
                <div class="col h5">Sélectionner une spécialité :</div>
                <div class="col">
                    <select class="custom-select" id="specialite" name="specialite">
                        <option value="00-Ensemble">Toutes</option>
                        <option value="01-Médecine générale">Médecine générale</option>
                        <option value="02-Chirurgie">Chirurgie</option>
                        <option value="03-Ophtalmologie">Ophtalmologie</option>
                        <option value="04-Oto-rhino-laryngologie">Oto-rhino-laryngologie</option>
                        <option value="05-Anesthésie-Réanimation">Anesthésie-Réanimation</option>
                        <option value="06-Biologie médicale">Biologie médicale</option>
                        <option value="07-Cardiologie">Cardiologie</option>
                        <option value="08-Dermatologie">Dermatologie</option>
                        <option value="09-Gastro-entérologie">Gastro-entérologie</option>
                        <option value="10-Gynécologie">Gynécologie</option>
                        <option value="11-Pédiatrie">Pédiatrie</option>
                        <option value="12-Pneumologie">Pneumologie</option>
                        <option value="13-Psychiatrie et neuropsychiatrie">Psychiatrie et neuropsychiatrie</option>
                        <option value="14-Plateaux techniques">Plateaux techniques</option>
                        <option value="15-Autre spécialité">Autre spécialité</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="border carte col-lg-8 col-md-10 col-sm-10">
                
                <div class="row text-center">
                    <h2 class="col-12 h6">
                        <span id="scale-title" class="bi bi-info-circle-fill" title="Nombre de médecins en activité"></span>
                    </h2>
                </div>
                <div class="row text-center mt-2">
                    <div class="col-lg">
                        <span class="legend-color-box" style="background-color: #cfe2f3;"></span>
                        <span class="legend-text" id="legend-label-1"></span>
                    </div>
                    <div class="col-lg">
                        <span class="legend-color-box" style="background-color: #9fc5e8;"></span>
                        <span class="legend-text" id="legend-label-2"></span>
                    </div>
                    <div class="col-lg">
                        <span class="legend-color-box" style="background-color: #6fa8dc;"></span>
                        <span class="legend-text" id="legend-label-3"></span>
                    </div>
                    <div class="col-lg">
                        <span class="legend-color-box" style="background-color: #3d85c6;"></span>
                        <span class="legend-text" id="legend-label-4"></span>
                    </div>
                    <div class="col-lg">
                        <span class="legend-color-box" style="background-color: #0b5394;"></span>
                        <span class="legend-text" id="legend-label-5"></span>
                    </div>
                </div>
                <?php
                    $page = "effectif";
                    include "php/carte-france-svg.php";
                ?>
                <p class="text-center">Survoler &nbsp;<i class='bi bi-cursor-fill'></i>&nbsp; un département pour voir le nombre de médecins.<br/>
	            <p class="text-muted p-2">
                    <br>
                    <strong>Source</strong> : Les données sont issues de l'étude <i>La démographie des professionnels de santé</i> au 1er janvier 2025 publiée par la DREES (Direction de la recherche, des études, de l’évaluation et des statistiques) du Ministère de la Santé.
					<br>
                    Les médecins sont ceux en activité, quelque soit le mode d'exercice (hospitalier, salarié, libéral ou mixte).
				</p>
                <p class="text-muted p-2 mb-1"><strong>Notes :</strong>
                    <br>&bull; Les points blancs sur la carte représentent les CHU.
                    <br>&bull; Les couleurs de l'échelle sont recalculées par la méthode des quantiles pour chaque spécialité, elles ne reflètent pas une échelle commune à toutes les spécialités.
                    <br>&bull; Certaines spécialités ne sont pas détaillées et sont regroupées soit dans <i>Chirurgie</i>, soit dans <i>Autre spécialité</i>.
                    <br>&bull; La spécialité <i>Plateaux techniques</i> est un regroupement de spécialités "techniques" comme la Radiologie.
                </p>    
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 mt-4 mt-lg-0">
                <div class="table-responsive">
                    <table class="table table-striped table-sm" >
                        <thead>
                            <tr>
                                <th scope="col">Département</th>
                                <th scope="col">Effectifs</th>
                            </tr>
                        </thead>
                        <tbody id="densite-table-body">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <footer style='margin-top:40px; margin-bottom:80px;'>
    </footer>

    <?php
        // librairies javascript nécessaires à l'application (jquery + popper + bootstrap)
        include "php/librairie.php";
    ?>
    
    <script>
        $(function () {
          $('g a').tooltip()
        })
    </script>

    <script>
        // fonctions appelées par les boutons de navigation
        function demographie() {
            <?php
                echo "window.location.href='demographie-medecin.php';";
            ?>
        }
		function densite() {
			<?php
				echo "window.location.href='carte-densite-medecin.php';";
			?>
		}
	</script>

    <script>
        function getColorForDensity(density, thresholds) {
            if (density > thresholds.t4) {
                return '#0b5394'; // bleu foncé
            } else if (density > thresholds.t3) {
                return '#3d85c6'; // bleu soutenu
            } else if (density > thresholds.t2) {
                return '#6fa8dc'; // bleu moyen
            } else if (density > thresholds.t1) {
                return '#9fc5e8'; // bleu clair
            } else {
                return '#cfe2f3'; // bleu très clair
            }
        }
        
        async function fetchData(specialite) {
            try {
                const url = `php/lireEffectif.php?specialite=${encodeURIComponent(specialite)}`;
                const response = await fetch(url);
                const data = await response.json();
                //console.log(data);
                return data;
            } catch (e) {
                console.error("Erreur fetchData:", e.message);
                return {};
            }
        }

        async function updateMap() {

            const specialiteSelect = document.getElementById('specialite');
            const specialite = specialiteSelect.value;
            const specialites = await fetchData(specialite);

            // --- MISE À JOUR DU TITRE DE L'ÉCHELLE ---
            const specialiteText = specialiteSelect.options[specialiteSelect.selectedIndex].text;
            const scaleTitle = document.getElementById('scale-title');
            scaleTitle.textContent = `  Effectifs médicaux ${specialiteText}`;
            $(function () {
                $('#scale-title').tooltip();
            });

            // --- Récupère les valeurs d'effectif ---
            const effectifValues = Object.values(specialites).map(d => d.effectif);

            // Tri pour calcul des quantiles
            effectifValues.sort((a, b) => a - b);

            // Calcul des seuils pour 5 niveaux
            const thresholds = {
                t1: effectifValues[Math.floor(effectifValues.length * 0.2)],
                t2: effectifValues[Math.floor(effectifValues.length * 0.4)],
                t3: effectifValues[Math.floor(effectifValues.length * 0.6)],
                t4: effectifValues[Math.floor(effectifValues.length * 0.8)]
            };

            // --- PARTIE LÉGENDE ---
            const legendLabels = [
                document.getElementById('legend-label-1'),
                document.getElementById('legend-label-2'),
                document.getElementById('legend-label-3'),
                document.getElementById('legend-label-4'),
                document.getElementById('legend-label-5')
            ];

            const minEffectif = effectifValues.length > 0 ? effectifValues[0] : 0;
            const maxEffectif = effectifValues.length > 0 ? effectifValues[effectifValues.length - 1] : 0;

            if (minEffectif === maxEffectif) {
                legendLabels[0].textContent = `Effectifs : ${Math.round(minEffectif)}`;
                legendLabels.slice(1).forEach(label => label.textContent = '');
            } else {
                legendLabels[0].textContent = `≤ ${Math.round(thresholds.t1)}`;
                legendLabels[1].textContent = `≤ ${Math.round(thresholds.t2)}`;
                legendLabels[2].textContent = `≤ ${Math.round(thresholds.t3)}`;
                legendLabels[3].textContent = `≤ ${Math.round(thresholds.t4)}`;
                legendLabels[4].textContent = `> ${Math.round(thresholds.t4)}`;
            }

            // --- PARTIE CARTE ---
            const paths = document.querySelectorAll('path[data-numerodepartement]');
            paths.forEach(path => {
                // Harmonisation du format des numéros (ex: "6" -> "06")
                const departmentNumber = path.getAttribute('data-numerodepartement').padStart(2, '0');
                path.setAttribute('data-html', 'true');

                const data = specialites[departmentNumber];
                if (data) {
                    path.style.fill = getColorForDensity(data.effectif, thresholds);
                    path.setAttribute('title', `Département : ${data.nom}<br>Effectifs : ${Math.round(data.effectif)}`);
                } else {
                    path.style.fill = '#e0e0e0';
                    path.setAttribute('title', `Données non disponibles pour ce département`);
                }
            });

            $(function () {
                $('path[data-numerodepartement]').tooltip('dispose');
                $('path[data-numerodepartement]').tooltip();
            });

            // --- PARTIE TABLEAU ---
            const tableBody = document.getElementById('densite-table-body');
            tableBody.innerHTML = '';
            const sortedSpecialites = Object.entries(specialites).sort(([keyA], [keyB]) => {
                return keyA.localeCompare(keyB);
            });

            sortedSpecialites.forEach(([key, data]) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${data.nom}</td>
                    <td class="text-right">${Math.round(data.effectif).toLocaleString('fr-FR')}</td>                `;
                tableBody.appendChild(row);
            });
        }

        // Initialisation de la carte avec la spécialité par défaut
        document.getElementById('specialite').addEventListener('change', updateMap);
        
        updateMap();
    </script>
  
  </body>
</html>