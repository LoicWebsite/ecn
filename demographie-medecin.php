<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Simulateur en ligne pour choisir une spécialité d'internat - démographie des médecins">

    <?php
		// favicons générés par https://realfavicongenerator.net
		include "php/favicon.php";
	
        // Google Analytics
		include "php/GoogleAnalytics.php";
	?>

    <title>Démographie des médecins par spécialité</title>

 	<?php
		// styles nécessaires à l'application (bootstrap + ECN)
		include "php/style.php";
	?>

    <!-- Librairie graphique -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

 	<!-- style propre à la page -->
    <style>
        .chart-container {
            margin: 0 10%;
        }
        canvas {
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            width: 100% !important;
            height: auto !important;
        }

        .btn-same-width {
            width: 130px;
            white-space: nowrap; /* évite le retour à la ligne dans le bouton */
        }
    </style>
  </head>

  <body id="hautdepage" style="color:#808080;">

	<?php
		include "php/menu-questionnaire.php";
	?>

	<header id="chemin">
		<h1 class="h3" style="text-align:center; margin-top:60px;">Ages des médecins</h1>
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
                        <button class="btn btn-secondary btn-sm mr-2 btn-same-width" disabled onclick="" title="Affichage de la pyramide des ages des médecins">
                            <i class="bi bi-bar-chart-fill"></i>&nbsp; &nbsp; Ages
                        </button>
                        <button class="btn btn-primary btn-sm mr-2 btn-same-width" onclick="densite()" title="Affichage de la densité médicale">
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

    <div class="container">

        <div id="critere" class="border p-2 mt-4">
            <h2 class="h5">Sélectionner la région, puis le département et la spécialité</h2>
            
            <!-- Région -->
            <div class="row mt-3 align-items-center">
                <div class="col">Région :</div>
                <div class="col">
                    <select class="custom-select" id="region" name="region">
						<option value="00-Ensemble">Toutes</option>
						<option value="84-Auvergne Rhône-Alpes">Auvergne Rhône-Alpes</option>
						<option value="27-Bourgogne Franche comté">Bourgogne Franche comté</option>
						<option value="53-Bretagne">Bretagne</option>
						<option value="24-Centre Val de loire">Centre Val de loire</option>
						<option value="94-Corse">Corse</option>
						<option value="44-Grand Est">Grand Est</option>
						<option value="01-Guadeloupe">Guadeloupe</option>
						<option value="03-Guyane">Guyane</option>
						<option value="32-Hauts de France">Hauts de France</option>
						<option value="04-La Reunion">La Reunion</option>
						<option value="11-Ile de France">Ile de France</option>
						<option value="02-Martinique">Martinique</option>
						<option value="06-Mayotte">Mayotte</option>
						<option value="28-Normandie">Normandie</option>
						<option value="52-Pays de la Loire">Pays de la Loire</option>
						<option value="75-Nouvelle Aquitaine">Nouvelle Aquitaine</option>
						<option value="76-Occitanie">Occitanie</option>
						<option value="93-Provence Alpes-Côte d Azur">Provence Alpes-Côte d'Azur</option>
						<option value="99-COM ou etranger">COM ou etranger</option>
                    </select>
                </div>
            </div>

            <!-- Département -->
            <div class="row mt-3 align-items-center">
                <div class="col">Département :</div>
                <div class="col">
                    <select class="custom-select" id="departement" name="departement">
                        <option value="000-Ensemble">Tous</option>
                    </select>
                </div>
            </div>

            <!-- Spécialité -->
            <div class="row mt-3 align-items-center">
                <div class="col">Spécialité :</div>
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

        <div id="graphique" class="border p-2 mt-4">
            <h2 class="h5">Répartition par tranches d'âge</h2>
            <canvas id="graphiqueMedecins" height="400"></canvas>
            <br>
            <p class="text-muted p-2">
                <br>    
                <strong>Source</strong> : Les données sont issues de l'étude <i>La démographie des professionnels de santé</i> au 1er janvier 2025 publiée par la DREES (Direction de la recherche, des études, de l’évaluation et des statistiques) du Ministère de la Santé.
    			<br>
                Les médecins sont ceux en activité, quelque soit le mode d'exercice (hospitalier, salarié, libéral ou mixte).
            </p>
            <p class="text-muted p-2 mb-1"><strong>Notes :</strong>
                <br>&bull; Certaines spécialités ne sont pas détaillées et sont regroupées soit dans <i>Chirurgie</i>, soit dans <i>Autre spécialité</i>.
                <br>&bull; La spécialité <i>Plateaux techniques</i> est un regroupement de spécialités "techniques" comme la Radiologie.
            </p>
        </div>
    </div>

	<footer style='margin-top:40px; margin-bottom:80px;'>
	</footer>

    <!-- Librairies JS et Bootstrap -->
    <?php
        include "php/librairie.php";
    ?>

   	<!-- navigation -->
	<script>
		// fonctions activées par les boutons de navigation
		function densite() {
			<?php
				echo "window.location.href='carte-densite-medecin.php';";
			?>
		}
		function effectif() {
			<?php
				echo "window.location.href='carte-effectif-medecin.php';";
			?>
		}
    </script>

    <script>
        const ctx = document.getElementById('graphiqueMedecins').getContext('2d');
        let chart;

        const axeX = ['tous', '<30', '30-34', '35-39', '40-44', '45-49', '50-54', '55-59', '60-64', '65-69', '70-74', '75+'];
        const tranches = [
            '00-Ensemble',
            '01-moins de 30 ans',
            '02-entre 30 et 34 ans',
            '03-entre 35 et 39 ans',
            '04-entre 40 et 44 ans',
            '05-entre 45 et 49 ans',
            '06-entre 50 et 54 ans',
            '07-entre 55 et 59 ans',
            '08-entre 60 et 64 ans',
            '09-entre 65 et 69 ans',
            '10-entre 70 et 74 ans',
            '11- 75 ans et plus'
        ];

        const colors = {
            '1-Hommes': '#88B0A8',
            '2-Femmes': '#F3B0A8'
        };

		const regionsDepartements = {
			"84-Auvergne Rhône-Alpes": ["01 - Ain", "03 - Allier", "07 - Ardèche", "15 - Cantal", "26 - Drôme", "38 - Isère", "42 - Loire", "43 - Haute-Loire", "63 - Puy-de-Dôme", "69 - Rhône", "73 - Savoie", "74 - Haute-Savoie"],
			"27-Bourgogne Franche comté": ["21 - Côte-d Or", "25 - Doubs", "39 - Jura", "58 - Nièvre", "70 - Haute-Saône", "71 - Saône-et-Loire", "89 - Yonne", "90 - Territoire de Belfort"],
			"53-Bretagne": ["22 - Côtes-d Armor", "29 - Finistère", "35 - Ille-et-Vilaine", "56 - Morbihan"],
			"24-Centre Val de loire": ["18 - Cher", "28 - Eure-et-Loir", "36 - Indre", "37 - Indre-et-Loire", "41 - Loir-et-Cher", "45 - Loiret"],
			"94-Corse": ["2A - Corse-du-Sud", "2B - Haute-Corse"],
			"44-Grand Est": ["08 - Ardennes", "10 - Aube", "51 - Marne", "52 - Haute-Marne", "54 - Meurthe-et-Moselle", "55 - Meuse", "57 - Moselle", "67 - Bas-Rhin", "68 - Haut-Rhin", "88 - Vosges"],
			"01-Guadeloupe": ["971 - Guadeloupe"],
			"03-Guyane": ["973 - Guyane"],
			"32-Hauts de France": ["02 - Aisne", "59 - Nord", "60 - Oise", "62 - Nord-Pas-de-Calais", "80 - Somme"],
			"04-La Reunion": ["974 - La Réunion"],
			"11-Ile de France": ["75 - Paris", "77 - Seine-et-Marne", "78 - Yvelines", "91 - Essonne", "92 - Hauts-de-Seine", "93 - Seine-Saint-Denis", "94 - Val-de-Marne", "95 - Val-d Oise"],
			"02-Martinique": ["972 - Martinique"],
			"06-Mayotte": ["976 - Mayotte"],
			"28-Normandie": ["14 - Calvados", "27 - Eure", "50 - Manche", "61 - Orne", "76 - Seine-Maritime"],
			"52-Pays de la Loire": ["44 - Loire-Atlantique", "49 - Maine-et-Loire", "53 - Mayenne", "72 - Sarthe", "85 - Vendée"],
			"75-Nouvelle Aquitaine": ["16 - Charente", "17 - Charente-Maritime", "19 - Corrèze", "23 - Creuse", "24 - Dordogne", "33 - Gironde", "40 - Landes", "47 - Lot-et-Garonne", "64 - Pyrénées-Atlantiques", "79 - Deux-Sèvres", "86 - Vienne", "87 - Haute-Vienne"],
			"76-Occitanie": ["09 - Ariège", "11 - Aude", "12 - Aveyron", "30 - Gard", "31 - Haute-Garonne", "32 - Gers", "34 - Hérault", "46 - Lot", "48 - Lozère", "65 - Hautes-Pyrénées", "66 - Pyrénées-Orientales", "81 - Tarn", "82 - Tarn-et-Garonne"],
			"93-Provence Alpes-Côte d Azur": ["04 - Alpes-de-Haute-Provence", "05 - Hautes-Alpes", "06 - Alpes-Maritimes", "13 - Bouches-du-Rhône", "83 - Var", "84 - Vaucluse"],
			"99-COM ou etranger": ["999 - COM ou étranger"]
		};

        function updateDepartements() {
            const regionSelect = document.getElementById('region');
            const departementSelect = document.getElementById('departement');
            const selectedRegion = regionSelect.value;

            departementSelect.innerHTML = '<option value="000-Ensemble">Tous</option>';
            if (selectedRegion && regionsDepartements[selectedRegion]) {
                regionsDepartements[selectedRegion].forEach(departement => {
                    const option = document.createElement('option');
                    option.value = departement;
                    option.textContent = departement;
                    departementSelect.appendChild(option);
                });
            }
        }

        function formatDepartement(departement) {
            const parts = departement.split(' - ');
            if (parts.length !== 2) return departement;
            let numericPart = parts[0];
            const textPart = parts[1];
            if (numericPart.length === 2) numericPart = '0' + numericPart;
            else if (numericPart.length === 1) numericPart = '00' + numericPart;
            console.log(`${numericPart}-${textPart}`);
            return `${numericPart}-${textPart}`;
        }

        async function fetchData(region, departement, specialite, genre) {
            try {
                const url = `php/lirePyramideAge.php?region=${encodeURIComponent(region)}&departement=${encodeURIComponent(formatDepartement(departement))}&specialite=${encodeURIComponent(specialite)}&genre=${encodeURIComponent(genre)}`;
                const response = await fetch(url);
                const text = await response.text();
                const json = JSON.parse(text);
                return tranches.map(tranche => json[tranche] ?? 0);
            } catch (e) {
                console.error("Erreur fetchData:", e.message);
                return tranches.map(() => 0);
            }
        }

        async function updateChart() {
            const region = document.getElementById('region').value;
            const departement = document.getElementById('departement').value;
            const specialite = document.getElementById('specialite').value;

            // On récupère hommes et femmes
            const dataHommes = await fetchData(region, departement, specialite, "1-Hommes");
            const dataFemmes = await fetchData(region, departement, specialite, "2-Femmes");

            // Ignorer la tranche "tous"
            const labels = axeX.slice(1).reverse();
            const maleData = dataHommes.slice(1).reverse().map(v => -v);
            const femaleData = dataFemmes.slice(1).reverse();

            if (chart) {
                chart.data.labels = labels;
                chart.data.datasets[0].data = maleData;
                chart.data.datasets[1].data = femaleData;
                chart.update();
            } else {
                chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            { label: 'Hommes', data: maleData, backgroundColor: colors["1-Hommes"] },
                            { label: 'Femmes', data: femaleData, backgroundColor: colors["2-Femmes"] }
                        ]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        scales: {
                            x: {
                                stacked: true,
                                ticks: {
                                    callback: (value) => new Intl.NumberFormat('fr-FR').format(Math.abs(value))
                                }
                            },
                            y: { stacked: true }
                        },
                        plugins: {
                            // Options du plugin datalabels, qui s'appliquent à tous les datasets
                            datalabels: {
                                color: 'black',
                                formatter: (value) => Math.abs(value)
                                    .toString()
                                    .replace(/\B(?=(\d{3})+(?!\d))/g, " "),
                                
                                // Nouvelle fonction pour cacher les labels si la valeur formatée est "0"
                                display: (context) => {
                                    const value = context.dataset.data[context.dataIndex];
                                    const formattedValue = Math.abs(value)
                                        .toString()
                                        .replace(/\B(?=(\d{3})+(?!\d))/g, " ");

                                    return formattedValue !== "0";
                                },

                                // Logique de positionnement pour l'alignement
                                align: (context) => {
                                    if (context.element) {
                                        const barWidth = context.element.width;
                                        if (context.datasetIndex === 0) { // Hommes
                                            return barWidth < 25 ? 'center' : 'end';
                                        } else { // Femmes
                                            return barWidth < 25 ? 'center' : 'start';
                                        }
                                    }
                                    return 'center';
                                },
                                
                                // Logique pour l'ancre
                                anchor: (context) => {
                                    if (context.datasetIndex === 0) {
                                        return 'start';
                                    } else {
                                        return 'end';
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: (context) => `${context.dataset.label}: ${new Intl.NumberFormat('fr-FR').format(Math.abs(context.raw))}`
                                }
                            }
                        }
                    },
                    plugins: [ChartDataLabels]
                });
            }
        }
        document.getElementById('region').addEventListener('change', () => { updateDepartements(); updateChart(); });
        document.getElementById('departement').addEventListener('change', updateChart);
        document.getElementById('specialite').addEventListener('change', updateChart);

        updateDepartements();
        updateChart();
    </script>
  </body>
</html>
