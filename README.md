# Choix d'une spécialité d'internat en médecine
Ce site Web permet d'explorer les 44 spécialités d'internat de médecine. Il permet en particulier de voir le rang du dernier admis ainsi que le nombre de postes par spécialité et CHU.
### Home page
La page d'accueil est une page HTML statique : `choix-specialite-chu-celine-ecn.php`
### Pages dynamiques
Les autres pages sont générées en PHP.
Les pages utilisent le framework Bootstrap.
### Base de données
La base de données est une base mySQL. Dans le repertoire `mySQL` il y a un fichier pour la création de la base vide : `ecn - tables.sql`, et autant de fichier de données que de tables. Il suffit de faire des imports de ces fichiers dans mySQL.
### Mise à jour des données
Dans le répertoire `php` il y a un batch PHP `base-2023-batch.php` qui met à jour automatiquement les données de la base depuis le site CELINE du CNG. Il faut au préalable vérifier que le format des pages HTML du site CELINE soit toujours le même que celui des années passées. Cette mise à jour se fait une fois par an, après la période de choix des néo-internes en général en septembre.
### Google Analytics
Il faut changer dans chaque page l'identifiant Google analytics `ID GOOGLE` si vous voulez conservez les statistiques des pages. Sinon il faut enlever le code correspondant qui se trouve en début de chaque page.