<?php
header('Content-Type: application/json; charset=utf-8');

// Connexion PDO
$pdo = new PDO('mysql:host=localhost;dbname=ecn;charset=utf8', 'ecn', 'ecn');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Lecture des paramètres
$region      = $_GET['region'] ?? '00-Ensemble';
$departement = $_GET['departement'] ?? '000-Ensemble';
$specialite  = $_GET['specialite'] ?? '00-Ensemble';
$genre       = $_GET['genre'] ?? '0-Ensemble';

// Requête SQL
$sql = "SELECT tranche_age, SUM(effectif_2024) AS total
        FROM Demographie
        WHERE region = :region
            AND departement = :departement
            AND specialites = :specialite
            AND sexe = :genre
            AND territoire <> '0-France entière'
            AND specialites_agregees <> '00-Ensemble'
            AND exercice <> '0-Ensemble'";

$params = [
    ':region' => $region,
    ':departement' => $departement,
    ':specialite' => $specialite,
    ':genre' => $genre
];

$sql .= " GROUP BY tranche_age ORDER BY tranche_age";

//echo "SQL: $sql\n"; // Pour débogage, à supprimer en production

// Exécution
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

// Résultat → tableau JSON
$data = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // [tranche_age => total]
echo json_encode($data);
