# Choix d'une spécialité d'internat en médecine
Ce site Web permet d'explorer les 44 spécialités d'internat de médecine. Il permet en particulier de voir le rang du dernier admis ainsi que le nombre de postes par spécialité et CHU.

Mon site Web est public : https://loic.website/ECN/choix-specialite-chu-celine-ecn.php
### Home page
La page d'accueil est une page HTML statique : `choix-specialite-chu-celine-ecn.php`
### Pages dynamiques
Les autres pages sont générées en PHP.
Les pages utilisent le framework Bootstrap.
### Base de données
La base de données est une base mySQL. Dans le repertoire `mySQL` il y a un fichier pour la création de la base vide : `ecn - tables.sql`, et autant de fichier de données que de tables. Il suffit de faire des imports de ces fichiers dans mySQL.

Par défaut le nom de la base est `ecn`. Dans chaque page qui se connecte à la base de données, il faut remplacer `USER` et `PASSE` par l'utilisateur et son mot de passe de votre base de données.
### Mise à jour des données
Les données sont à jour dans le répertoire mySQL. Ce sont des données au format SQL (insert). Le schéma de la base est également dans ce répertoire (create table).
Je mets à jour les données une fois par an dans ce répertoire à chaque résultat du concours EDN/ECOS, plus précisément à la fin de l'appariement début octobre (voir la plateforme du CNG Santé ppour plsu de détail).
### Google Analytics
Il faut changer dans chaque page l'identifiant Google analytics `ID GOOGLE` si vous voulez conserver les statistiques des pages. Sinon il faut enlever le code correspondant qui se trouve en début de chaque page.