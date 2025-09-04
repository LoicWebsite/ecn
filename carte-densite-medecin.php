<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Simulateur en ligne pour choisir une spécialité d'internat - carte de France de la densité médicale">

	<?php
		// favicons générés par https://realfavicongenerator.net
		include "php/favicon.php";
	
        // Google Analytics
		include "php/GoogleAnalytics.php";
	?>

    <title>Densité médicale</title>
    
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
        <h1 class="h3" style="text-align:center; margin-top:60px;">Densité médicale</h1>
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
                        <button class="btn btn-secondary btn-sm mr-2 btn-same-width" disabled onclick="" title="Affichage de la densité médicale">
                            <i class="bi bi-geo-alt-fill"></i>&nbsp; &nbsp; Densité
                        </button>
                        <button class="btn btn-primary btn-sm btn-same-width" onclick="effectif()" title="Affichage du nombre de médecins">
                            <i class="bi bi-people-fill"></i>&nbsp; &nbsp; Effectifs
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container mt-5">
        <div id="critere" class="p-2 mt-4">
            <div class="border row p-3 align-items-center">
                <div class="col h5">Sélectionner une spécialité :</div>
                <div class="col">
                    <select class="custom-select" id="specialite" name="specialite">
                        <option value="toutes">Toutes</option>
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
                        <span id="scale-title" class="bi bi-info-circle-fill" title="Nombre de médecins pour 100 000 habitants"></span>
                    </h2>
                </div>
                <div class="row text-center mt-2">
                    <div class="col-lg">
                        <span class="legend-color-box" style="background-color: #e8f5e9;"></span>
                        <span class="legend-text" id="legend-label-1"></span>
                    </div>
                    <div class="col-lg">
                        <span class="legend-color-box" style="background-color: #a5d6a7;"></span>
                        <span class="legend-text" id="legend-label-2"></span>
                    </div>
                    <div class="col-lg">
                        <span class="legend-color-box" style="background-color: #66bb6a;"></span>
                        <span class="legend-text" id="legend-label-3"></span>
                    </div>
                    <div class="col-lg">
                        <span class="legend-color-box" style="background-color: #2e7d32;"></span>
                        <span class="legend-text" id="legend-label-4"></span>
                    </div>
                    <div class="col-lg">
                        <span class="legend-color-box" style="background-color: #1b5e20;"></span>
                        <span class="legend-text" id="legend-label-5"></span>
                    </div>
                </div>

                <?php
                    $page = "densite";
                    include "php/carte-france-svg.php";
                ?>
                <p class="text-center">Survoler &nbsp;<i class='bi bi-cursor-fill'></i>&nbsp; un département pour voir la densité médicale.<br/>
	            <p class="text-muted p-2">
                    <br>
                    <strong>Source</strong> : Les données sont issues de l'étude <i>Approche Territoriale des Spécialités Médicales et Chirurgicales</i> au 1er janvier 2025 publiée par le CNOM (Conseil National de l'Ordre des Médecins).
					<br>
                    La densité médicale est le nombre de médecins en activité pour 100 000 habitants, quelque soit le mode d'exercice (hospitalier, salarié, libéral ou mixte).
				</p>
                <p class="text-muted p-2 mb-1"><strong>Notes :</strong>
                    <br>&bull; Les points blancs sur la carte représentent les CHU. On peut remarquer que, généralement, la densité médicale est plus élevée dans les départements où se trouvent les CHU.
                    <br>&bull; Les couleurs de l'échelle sont recalculées par la méthode des quantiles pour chaque spécialité, elles ne reflètent pas une échelle commune à toutes les spécialités.
                    <br>&bull; Dans cette étude du CNOM 6 diplômes récents d’études spécialisées (DES) Médecine d’urgence, Médecine légale et expertises médicales, Allergologie, Médecine intensive-réanimation, Maladies infectieuses et tropicales et Médecine vasculaire ne sont pas listées.
                    <br>&bull; Lorsqu'aucune spécialité n'est sélectionnée ('Toutes' par défaut), ce sont les moyennes arithmétiques de toutes les spécialités par département qui sont affichées.
                </p>
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 mt-4 mt-lg-0">
                <div class="table-responsive">
                    <table class="table table-striped table-sm" >
                        <thead>
                            <tr>
                                <th scope="col">Département</th>
                                <th scope="col">Densité</th>
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
        // Fonction pour charger la liste des spécialités dans le dropdown
        function loadSpecialties() {
            fetch('php/lireDensiteSpecialite.php')
                .then(response => {
                    // Vérifier si la réponse est OK (statut 200)
                    if (!response.ok) {
                        throw new Error('Erreur réseau lors du chargement des spécialités.');
                    }
                    // Parser la réponse JSON
                    return response.json();
                })
                .then(data => {
                    // Sélectionner l'élément dropdown
                    const select = document.getElementById('specialite');
                    
                    // Parcourir les données et ajouter une option pour chaque spécialité
                    data.forEach(specialite => {
                        const option = document.createElement('option');
                        // La valeur et le texte de l'option sont les mêmes
                        option.value = specialite;
                        option.textContent = specialite;
                        select.appendChild(option);
                    });
                })
                .catch(error => {
                    // Gérer les erreurs et les afficher dans la console
                    console.error('Erreur lors du chargement des spécialités:', error);
                    // Vous pouvez également afficher un message d'erreur à l'utilisateur
                    const select = document.getElementById('specialite');
                    const errorOption = document.createElement('option');
                    errorOption.textContent = 'Erreur de chargement';
                    errorOption.disabled = true;
                    select.appendChild(errorOption);
                });
        }

        // Appeler la fonction au chargement de la page
        document.addEventListener('DOMContentLoaded', loadSpecialties);
    </script>

    <script>
        $(function () {
          $('g a').tooltip()
        })
    </script>

    <script>
        // Fonction pour rediriger vers les pages (des boutons)
        function demographie() {
            <?php
                echo "window.location.href='demographie-medecin.php';";
            ?>
        }
        function effectif() {
            <?php
                echo "window.location.href='carte-effectif-medecin.php';";
            ?>
        }
    </script>

    <script>
        function getColorForDensity(density, thresholds) {
            if (density > thresholds.t4) {
                return '#1b5e20';
            } else if (density > thresholds.t3) {
                return '#2e7d32';
            } else if (density > thresholds.t2) {
                return '#66bb6a';
            } else if (density > thresholds.t1) {
                return '#a5d6a7';
            } else {
                return '#e8f5e9';
            }
        }
        
        async function fetchData(specialite) {
            try {
                const url = `php/lireDensite.php?specialite=${encodeURIComponent(specialite)}`;
                const response = await fetch(url);
                const data = await response.json();
                return data;
            } catch (e) {
                console.error("Erreur fetchData:", e.message);
                return {};
            }
        }

        async function updateMap() {
            const specialiteSelect = document.getElementById('specialite');
            const specialite = specialiteSelect.value;
            const densities = await fetchData(specialite);
            
            // --- MISE À JOUR DU TITRE DE L'ÉCHELLE ---
            const specialiteText = specialiteSelect.options[specialiteSelect.selectedIndex].text;
            const scaleTitle = document.getElementById('scale-title');
            scaleTitle.textContent = `  Densité médicale ${specialiteText}`;
            $(function () {
                $('#scale-title').tooltip();
            });

            // --- Calcul dynamique des seuils de couleur par quantiles ---
            const densityValues = Object.values(densities).map(d => d.Densite_2025);
            
            // Triez les valeurs pour calculer les quantiles
            densityValues.sort((a, b) => a - b);
            
            // Calculez les seuils pour 5 niveaux de couleur
            const thresholds = {
                t1: densityValues[Math.floor(densityValues.length * 0.2)],
                t2: densityValues[Math.floor(densityValues.length * 0.4)],
                t3: densityValues[Math.floor(densityValues.length * 0.6)],
                t4: densityValues[Math.floor(densityValues.length * 0.8)]
            };
            
			// --- PARTIE LÉGENDE ---
			const legendLabel1 = document.getElementById('legend-label-1');
			const legendLabel2 = document.getElementById('legend-label-2');
			const legendLabel3 = document.getElementById('legend-label-3');
			const legendLabel4 = document.getElementById('legend-label-4');
			const legendLabel5 = document.getElementById('legend-label-5');
			const legendLabels = [legendLabel1, legendLabel2, legendLabel3, legendLabel4, legendLabel5];

			const minDensity = densityValues.length > 0 ? densityValues[0] : 0;
			const maxDensity = densityValues.length > 0 ? densityValues[densityValues.length - 1] : 0;

			if (minDensity === maxDensity) {
				legendLabels[0].textContent = `Densité: ${Math.round(minDensity)}`;
				legendLabels[1].textContent = '';
				legendLabels[2].textContent = '';
				legendLabels[3].textContent = '';
				legendLabels[4].textContent = '';
			} else {
				// Affiche uniquement les bornes supérieures pour alléger la légende
				legendLabels[0].textContent = `≤ ${Math.round(thresholds.t1)}`;
				legendLabels[1].textContent = `≤ ${Math.round(thresholds.t2)}`;
				legendLabels[2].textContent = `≤ ${Math.round(thresholds.t3)}`;
				legendLabels[3].textContent = `≤ ${Math.round(thresholds.t4)}`;
				legendLabels[4].textContent = `> ${Math.round(thresholds.t4)}`;
			}			

			// --- PARTIE CARTE ---
            const paths = document.querySelectorAll('path[data-numerodepartement]');
            paths.forEach(path => {
                const departmentNumber = path.getAttribute('data-numerodepartement');
                path.setAttribute('data-html', 'true');
                if (departmentNumber) {
                    const data = densities[departmentNumber];
                    if (data) {
                        path.style.fill = getColorForDensity(data.Densite_2025, thresholds);
                        path.setAttribute('title', `Département : ${data.Departement}<br>Densité : ${data.Densite_2025.toFixed(1)}`);
                    } else {
                        path.style.fill = '#e0e0e0';
                        path.setAttribute('title', `Données non disponibles pour ce département`);
                    }
                } else {
                    path.style.fill = '#e0e0e0';
                    path.setAttribute('title', `Département non identifié`);
                }
            });
            
            $(function () {
                $('path[data-numerodepartement]').tooltip('dispose');
                $('path[data-numerodepartement]').tooltip();
            });

            // --- PARTIE TABLEAU ---
            const tableBody = document.getElementById('densite-table-body');
            tableBody.innerHTML = '';
            // On re-trie côté client pour garantir l'ordre
            const sortedDensities = Object.entries(densities).sort(([keyA], [keyB]) => {
                // Utiliser un tri de chaîne de caractères standard pour un ordre correct
                return keyA.localeCompare(keyB);
            });

            sortedDensities.forEach(([key, data]) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${data.Departement}</td>
                    <td class="text-right">${data.Densite_2025.toFixed(1)}</td>
                `;
                tableBody.appendChild(row);
            });
        }

        document.getElementById('specialite').addEventListener('change', updateMap);
        
        updateMap();
    </script>
  
  </body>
</html>