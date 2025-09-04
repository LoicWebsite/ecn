# Choix d'une spécialité d'internat en médecine
Ce site Web permet d'explorer les 44 spécialités d'internat de médecine. Il permet en particulier de voir le rang du dernier admis ainsi que le nombre de postes par spécialité et CHU. Il contient également des données démographiques médicales.

Mon site Web est public : https://loic.website/ECN/choix-specialite-chu-celine-ecn.php
### Home page
La page d'accueil est une page HTML statique : `choix-specialite-chu-celine-ecn.php`
### Pages dynamiques
Les autres pages sont générées en PHP.
Les pages utilisent le framework Bootstrap.
### Base de données
La base de données est une base mySQL. Dans le repertoire `/mySQL` il y a un fichier pour la création de la base vide : `ecn - tables.sql`, et autant de fichier de données que de tables. Il suffit de faire des imports de ces fichiers dans mySQL.

Dans le code, pour accéder à la base de données, il faut remplacer `USER` et `PASSWORD` par l'utilisateur et son mot de passe de votre base de données dans la fonction `openDatabase()` du script `/php/fonctionECN.php`
### Mise à jour des données
Les données sont à jour dans le répertoire `/mySQL`. Ce sont des données au format SQL (insert). Le schéma de la base est également dans ce répertoire (create table).
Je mets à jour les données une fois par an dans ce répertoire à chaque résultat du concours EDN/ECOS, plus précisément à la fin de l'appariement début octobre (voir la plateforme du CNG Santé ppour plus de détail).
### Google Analytics
Si vous voulez utiliser GoogleAnalytics pour suivre le trafic de vos pages, il faut changer l'identifiant Google analytics `ID GOOGLE` dans le script `/php/GoogleAnalytics.php`. Sinon supprimer l'appel de ce script dans toutes les pages (ou remplacer par `<scrip></script>` dans `/php/GoogleAnalytics.php`).