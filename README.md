#Choix d'une spécialité d'internat en médecine
Ce site Web permet d'explorer les 44 spécialités d'internat de médecine. Il permet en particulier de voir le rang du dernier admis ainsi que le nombre de postes par spéciélité et CHU.
###Home page
La page d'accueil est une page HTML statique : `choix-specialite-chu-celine-ecn.php`
###Pages dynamiques
Les autres pages sont générées en PHP.
Les pages utilisent le framework Bootstrap.
###Base de données
La base de données est une base mySQL. Dans le repertoire mySQL il y a un fichier pour la création de la base vide : `ecn - tables.sql`, et autant de fichier de données que de tables. Il suffit de faire des imports de ces fichiers dans mySQL.