# Choix d'une spécialité d'internat en médecine

[![Site web](https://img.shields.io/badge/Site-web-blue)](https://loic.website/ECN/choix-specialite-chu-celine-ecn.php)

Ce projet est un **simulateur indépendant et gratuit de choix de spécialité et de CHU** destiné aux étudiants en médecine qui préparent leur choix d'internat après les EDN/ECOS.

Il permet notamment de :

- explorer les **44 spécialités d'internat** ;
- consulter les **rangs limites des derniers admis** par spécialité et par CHU ;
- consulter le **nombre de postes** proposés par spécialité et par CHU ;
- simuler les possibilités correspondant à un rang donné ;
- comparer les spécialités selon différents critères ;
- consulter des données démographiques concernant les médecins.

Le projet est développé et maintenu bénévolement.

## 🌐 Utiliser le simulateur

**[Accéder au simulateur en ligne](https://loic.website/ECN/choix-specialite-chu-celine-ecn.php)**

Une version Excel et une version OpenOffice du simulateur sont également disponibles depuis le site.

---

# Pourquoi ce projet ?

Le choix d'une spécialité et d'un CHU constitue une étape importante pour les étudiants en médecine.

L'objectif de ce projet est de permettre aux étudiants de **se projeter avant de connaître leur classement définitif**, en confrontant leurs souhaits avec les données des années précédentes.

Le simulateur peut notamment être utilisé pour construire :

- des vœux « de rêve » ;
- des vœux réalistes ;
- des solutions de repli.

L'objectif n'est pas de déterminer quelle spécialité ou quel CHU choisir, mais de fournir des données permettant à chaque étudiant de construire sa propre réflexion.

---

# 📊 Données utilisées

Les données présentées par le simulateur proviennent de différentes sources.

| Donnée | Source | Utilisation dans le projet |
|---|---|---|
| Rangs des derniers admis | **CNG Santé** depuis 2024 inclus | Rangs limites par spécialité et CHU |
|| **Journal Officiel** pour les années précédentes | Rangs limites par spécialité et CHU |
| Nombre de postes | **Journal officiel** | Nombre de postes ouverts par spécialité et CHU |
| Nombre de CESP | **Journal officiel** | Nombre de CESP ouverts par spécialité et CHU |
| Revenus des médecins libéraux | **UNASA / CARMF** | Données indicatives sur les revenus par spécialité pour les exercices libéraux |
| Données démographiques médicales | **DREES** | Effectifs et pyramide des âges |
| | **Conseil National de l'Ordre des Médecins (CNOM)** | Densité médicale |

### Sources officielles et traitement des données

Les données sources ne sont pas produites par ce projet.

Elles sont collectées auprès des organismes mentionnés ci-dessus, puis **intégrées, structurées et mises en forme** pour permettre leur utilisation dans le simulateur.

Le dépôt GitHub contient notamment les données utilisées pour alimenter la base MySQL ainsi que le code permettant de les exploiter.

Les informations publiées par le simulateur ne doivent donc pas être considérées comme des données officielles produites par ce projet.

---

# 🔄 Mise à jour des données

Les données relatives aux **rangs** sont mises à jour **une fois par an**, après les résultats de l'appariement national, généralement au début du mois d'octobre.

Les données relatives aux nombre de **postes** et de **CESP** sont mises à jour **une fois par an**, après la publication au Journal Officiel de l'arrêté correspondant, généralement au mois de juillet.

Le répertoire [`mySQL`](./mySQL) contient les données actuellement utilisées par le site sous forme de fichiers SQL.

Le dépôt permet ainsi de conserver une copie des données utilisées par le simulateur et de rendre leur traitement plus transparent.

> **Important :** Pour les informations officielles relatives à l'affectation des internes et aux postes ouverts, il convient de se reporter aux publications du CNG et aux textes officiels correspondants.

---

# 🧮 Fonctionnement du simulateur

Le principe du simulateur est simple :

1. l'utilisateur indique un **rang candidat** qu'il souhaite tester ;
2. le simulateur recherche les spécialités et les CHU correspondant aux données historiques ;
3. l'utilisateur peut ensuite sélectionner les spécialités et les CHU qui l'intéressent ;
4. il peut comparer ses possibilités et construire progressivement ses vœux.

Les données historiques permettent de se faire une idée des possibilités associées à un rang donné.

**Elles ne constituent cependant pas une prévision du résultat d'un futur appariement.**

---

# 🗂️ Organisation du dépôt

```text
/
├── css/                         # Feuilles de style
├── icon/                        # Icônes du site
├── image/                       # Images et ressources graphiques
├── mySQL/                       # Schéma et données de la base MySQL
│
├── php/                         # Fonctions PHP communes
│
├── choix-specialite-chu-celine-ecn.php
├── questionnaire-choix-specialite.php
├── detail-specialite-simulateur.php
├── detail-CHU.php
├── liste-specialite.php
├── liste-CHU.php
├── tableau-specialite.php
├── tableau-poste.php
├── tableau-cesp.php
├── rang-limite-VRAI.php
│
├── carte-chu.php
├── carte-chu-simulateur.php
├── demographie-medecin.php
├── carte-effectif-medecin.php
├── carte-densite-medecin.php
├── carte-densite-medecin-DREES.php
│
├── Simulation.xlsx
├── Simulation.ods
│
├── LICENSE.md
└── README.md
```

## Base de données MySQL

Le répertoire [`mySQL`](./mySQL) contient :

- le script de création des tables ;
- les fichiers SQL contenant les données ;
- les données nécessaires au fonctionnement du simulateur.

Le fichier :

```text
mySQL/ecn - tables.sql
```

permet de créer la structure de la base.

Les autres fichiers SQL permettent ensuite d'importer les données.

---

# 💻 Technologies

Le site utilise principalement :

- **PHP**
- **MySQL**
- **HTML / CSS**
- **Bootstrap**
- **JavaScript**
- **Chart.js** pour certaines représentations graphiques

Les versions Excel et OpenOffice du simulateur sont également conservées dans le dépôt.

---

# 🚀 Installation locale

Le site peut être installé sur un serveur PHP disposant d'une base MySQL.

### 1. Créer la base de données

Créer une base MySQL puis exécuter :

```text
mySQL/ecn - tables.sql
```

Importer ensuite les fichiers SQL contenant les données.

### 2. Configurer la connexion MySQL

La connexion à la base est définie dans :

```text
php/fonctionECN.php
```

Adapter les paramètres de connexion à votre environnement.


### 3. Installer les fichiers du site

Placer les fichiers PHP, CSS, images et autres ressources dans le répertoire correspondant à votre serveur web.

---

# 📈 Simulateur Excel / OpenOffice

Les fichiers :

- [`Simulation.xlsx`](./Simulation.xlsx)
- [`Simulation.ods`](./Simulation.ods)

constituent une version autonome du simulateur.

Ils permettent notamment d'explorer les spécialités et les CHU à partir d'un rang candidat et de consulter les données historiques disponibles.

---

# 🔎 Transparence et reproductibilité

Ce dépôt est public afin de permettre à toute personne intéressée de :

- consulter le code du simulateur ;
- examiner la structure de la base de données ;
- consulter les données intégrées au site ;
- comprendre comment les données sont exploitées ;
- reproduire le fonctionnement du site dans un environnement PHP/MySQL.

Le dépôt constitue ainsi un complément au site public.

**Site :**  
https://loic.website/ECN/choix-specialite-chu-celine-ecn.php

**Dépôt :**  
https://github.com/LoicWebsite/ecn

---

# ⚠️ Limites et avertissement

Ce simulateur est un **outil d'aide à la réflexion** et non un outil officiel du CNG.

Les rangs historiques ne permettent pas de garantir qu'une spécialité ou un CHU sera accessible à un rang donné lors d'un futur appariement.

Les données provenant d'organismes externes restent susceptibles d'être modifiées, corrigées ou interprétées différemment selon les années.

Malgré le soin apporté au développement et à la mise à jour du projet, des erreurs ou des bugs peuvent subsister.

Pour les informations officielles concernant les affectations, les postes et les procédures d'internat, il convient de se reporter aux sources officielles correspondantes.

---

# 📄 Licence

Voir [`LICENSE.md`](./LICENSE.md).

---

# 📬 Contact

Pour signaler une erreur, proposer une amélioration ou poser une question concernant le projet :

**contact@loic.website**

Les remarques et contributions sont les bienvenues.

---

## À propos

Ce projet est développé et maintenu bénévolement par son auteur.

Il a initialement été créé pour aider un étudiant en médecine de son entourage à réfléchir à son choix de spécialité et de CHU, puis a été rendu public afin d'être utile à d'autres étudiants.

## Bonne chance pour les EDN, les ECOS et l'internat !
