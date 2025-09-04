<style>
    .dropdown-dark .dropdown-item {
        color: #fff; /* ou n'importe quelle autre couleur claire */
    }

    .dropdown-dark .dropdown-item:hover,
    .dropdown-dark .dropdown-item:focus {
        background-color: #495057; /* Couleur de survol plus foncée */
        color: #fff;
    }
</style>

<nav id="navigation" class="navbar fixed-top navbar-expand-lg navbar-dark bg-secondary">
    <a class="navbar-brand" href="choix-specialite-chu-celine-ecn.php"><img src="image/stethoscopeBlanc.svg" width="30" height="30" alt="logo stéthoscope blanc pour choix de spécialités d'internat'" loading="lazy"> &nbsp;Simulateur spécialités</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#menu" aria-controls="menu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div id="menu" class="collapse navbar-collapse">
        <ul class="navbar-nav mr-auto">
            <li id="specialites" class="nav-item">
                <a class="nav-link" href="choix-specialite-chu-celine-ecn.php#specialite">Spécialité</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownSimulateur" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Simulateur Excel
                </a>
                <div class="dropdown-menu bg-secondary dropdown-dark" aria-labelledby="navbarDropdownSimulateur">
                    <a class="dropdown-item" href="choix-specialite-chu-celine-ecn.php#telecharger">Télécharger</a>
                    <a class="dropdown-item" href="choix-specialite-chu-celine-ecn.php#apercu">Aperçu</a>
                    <a class="dropdown-item" href="choix-specialite-chu-celine-ecn.php#qui">Pour qui</a>
                    <a class="dropdown-item" href="choix-specialite-chu-celine-ecn.php#fonctionnement">Fonctionnement</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="questionnaire-choix-specialite.php">Simulateur en Ligne</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownDemographie" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Démographie
                </a>
                <div class="dropdown-menu bg-secondary dropdown-dark" aria-labelledby="navbarDropdownDemographie">
                    <a class="dropdown-item" href="demographie-medecin.php">Pyramide des âges</a>
                    <a class="dropdown-item" href="carte-densite-medecin.php">Densité médicale</a>
                    <a class="dropdown-item" href="carte-effectif-medecin.php">Nombre de médecins</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="choix-specialite-chu-celine-ecn.php#propos">A propos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="choix-specialite-chu-celine-ecn.php#contact">Contact</a>
            </li>
        </ul>
    </div>
</nav>

<script>
    // script pour activer le lien de la page actuelle dans le menu de navigation
    // Ce script doit être exécuté après que le DOM soit complètement chargé
    document.addEventListener("DOMContentLoaded", function() {

        const navLinks = document.querySelectorAll('.navbar-nav .nav-link, .dropdown-menu .dropdown-item');
        const sections = document.querySelectorAll('main > section[id], main > div[id]'); // Sélectionnez les sections avec un ID

        function removeAllActiveClasses() {
            navLinks.forEach(link => {
                link.classList.remove('active');
                const parentDropdown = link.closest('.dropdown');
                if (parentDropdown) {
                    const parentToggle = parentDropdown.querySelector('.dropdown-toggle');
                    if (parentToggle) {
                        parentToggle.classList.remove('active');
                    }
                }
            });
        }

        function getBaseUrl(url) {
            return url.split('#')[0].split('?')[0];
        }

        const activeMap = [
            // Votre table de correspondance
            { pattern: "questionnaire-choix-specialite.php", selector: '.nav-link[href="questionnaire-choix-specialite.php"]' },
            { pattern: "liste-specialite.php", selector: '.nav-link[href="questionnaire-choix-specialite.php"]' },
            { pattern: "liste-CHU.php", selector: '.nav-link[href="questionnaire-choix-specialite.php"]' },
            { pattern: "detail-CHU.php", selector: '.nav-link[href="questionnaire-choix-specialite.php"]' },
            { pattern: "carte-chu.php", selector: '.nav-link[href="questionnaire-choix-specialite.php"]' },
            { pattern: "detail-specialite-questionnaire.php", selector: '.nav-link[href="questionnaire-choix-specialite.php"]' },
            { pattern: "tableau-specialite.php", selector: '.nav-link[href="questionnaire-choix-specialite.php"]' },
            { pattern: "tableau-poste.php", selector: '.nav-link[href="questionnaire-choix-specialite.php"]' },
            { pattern: "tableau-cesp.php", selector: '.nav-link[href="questionnaire-choix-specialite.php"]' },
            { pattern: "#specialite", selector: '.nav-link[href="choix-specialite-chu-celine-ecn.php#specialite"]' },
            { pattern: "#telecharger", selector: '.dropdown-item[href="choix-specialite-chu-celine-ecn.php#telecharger"]' },
            { pattern: "#apercu", selector: '.dropdown-item[href="choix-specialite-chu-celine-ecn.php#apercu"]' },
            { pattern: "#qui", selector: '.dropdown-item[href="choix-specialite-chu-celine-ecn.php#qui"]' },
            { pattern: "#fonctionnement", selector: '.dropdown-item[href="choix-specialite-chu-celine-ecn.php#fonctionnement"]' },
            { pattern: "#propos", selector: '.nav-link[href="choix-specialite-chu-celine-ecn.php#propos"]' },
            { pattern: "#contact", selector: '.nav-link[href="choix-specialite-chu-celine-ecn.php#contact"]' }, 
            { pattern: "detail-specialite-simulateur.php", selector: '.nav-link[href="choix-specialite-chu-celine-ecn.php#specialite"]' },
            { pattern: "carte-chu-simulateur.php", selector: '.nav-link[href="choix-specialite-chu-celine-ecn.php#specialite"]' },
            { pattern: "demographie-medecin.php", selector: '.dropdown-item[href="demographie-medecin.php"]' },
            { pattern: "carte-densite-medecin.php", selector: '.dropdown-item[href="carte-densite-medecin.php"]' },
            { pattern: "carte-effectif-medecin.php", selector: '.dropdown-item[href="carte-effectif-medecin.php"]' },
        ];
        
        function activateLink() {
            removeAllActiveClasses();
            const currentUrl = window.location.href;
            const currentBase = getBaseUrl(currentUrl);
            const currentHash = window.location.hash || '';
            let matched = false;

            // 1. Logique pour les pages spécifiques sans ancres (ex: Simulateur en ligne)
            for (const item of activeMap) {
                if (currentUrl.includes(item.pattern) && item.pattern.includes('.php')) {
                    const link = document.querySelector(item.selector);
                    if (link) {
                        link.classList.add('active');
                        const parentDropdown = link.closest('.dropdown');
                        if (parentDropdown) {
                            const parentToggle = parentDropdown.querySelector('.dropdown-toggle');
                            if (parentToggle) {
                                parentToggle.classList.add('active');
                            }
                        }
                        matched = true;
                        // Ne pas continuer pour les ancres
                        return; 
                    }
                }
            }

            // 2. Logique pour les ancres sur la page principale
            if (currentBase.endsWith('choix-specialite-chu-celine-ecn.php')) {
                if (currentHash) {
                    const link = document.querySelector(`.nav-link[href$="${currentHash}"], .dropdown-item[href$="${currentHash}"]`);
                    if (link) {
                        link.classList.add('active');
                        const parentDropdown = link.closest('.dropdown');
                        if (parentDropdown) {
                            const parentToggle = parentDropdown.querySelector('.dropdown-toggle');
                            if (parentToggle) {
                                parentToggle.classList.add('active');
                            }
                        }
                    }
                } else {
                    // Si pas de hash sur la page d'accueil, on ne sélectionne rien
                    removeAllActiveClasses();
                }
                matched = true;
            }

            // 3. Logique de défilement pour les pages avec ancres
            window.addEventListener('scroll', function() {
                if (currentBase.endsWith('choix-specialite-chu-celine-ecn.php')) {
                    let currentSectionId = '';
                    sections.forEach(section => {
                        const sectionTop = section.offsetTop - 100; // Ajustez la valeur si nécessaire
                        const sectionHeight = section.clientHeight;
                        if (window.scrollY >= sectionTop && window.scrollY < sectionTop + sectionHeight) {
                            currentSectionId = section.id;
                        }
                    });

                    if (currentSectionId) {
                        const activeLink = document.querySelector(`.nav-link[href*="#${currentSectionId}"], .dropdown-item[href*="#${currentSectionId}"]`);
                        if (activeLink) {
                            removeAllActiveClasses();
                            activeLink.classList.add('active');
                            const parentDropdown = activeLink.closest('.dropdown');
                            if (parentDropdown) {
                                const parentToggle = parentDropdown.querySelector('.dropdown-toggle');
                                if (parentToggle) {
                                    parentToggle.classList.add('active');
                                }
                            }
                        }
                    }
                }
            });
        }

        // Déclenchement initial
        activateLink();

        // Événements de navigation
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                setTimeout(activateLink, 50);
            });
        });

        window.addEventListener('popstate', activateLink);
        window.addEventListener('hashchange', activateLink);
    });
</script>

