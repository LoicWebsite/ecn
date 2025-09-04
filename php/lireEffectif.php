<?php
header('Content-Type: application/json; charset=utf-8');

// connexion à la base de données
include_once "fonctionECN.php";  // Inclure le fichier contenant openDatabase()
$pdo = openDatabase();

// Lecture des paramètres
$specialite  = $_GET['specialite'] ?? '00-Ensemble';

// Requête SQL
$sql = "SELECT departement, effectif_2025
        FROM Demographie
        WHERE specialites = :specialite
            AND territoire <> '0-France entière'
            AND region <> '00-Ensemble'
            AND departement <> '000-Ensemble'
            AND tranche_age = '00-Ensemble'
            AND exercice = '0-Ensemble'
            AND sexe = '0-Ensemble'";

if ($specialite == '00-Ensemble') {
    $sql .= " AND specialites_agregees = '00-Ensemble'";
} else {
    $sql .= " AND specialites_agregees <> '00-Ensemble'";
}

$params = [
    ':specialite' => $specialite,
];

$sql .= " ORDER BY departement";

// exécution de la requête
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

// pour debug
//$fichier = fopen("LogPHP.txt", "w");
//fwrite($fichier, $sql . "/n" . $specialite);

$data = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Extrait les 3 premiers caractères (ex: "006")
    $numero_complet = substr($row['departement'], 0, 3);
    
    // Supprime les zéros en début de chaîne pour obtenir "6" ou "13"
    $numero_sans_zero = preg_replace('/^0+/', '', $numero_complet);
    
    // Ajoute un zéro en début de chaîne si le numéro ne fait qu'un caractère
    // '6' devient '06', '13' reste '13'
    $numero_propre = str_pad($numero_sans_zero, 2, '0', STR_PAD_LEFT);
    
    // Extrait le nom du département
    $nom_departement = trim(substr($row['departement'], strpos($row['departement'], '-') + 1));
    
    // Utiliser ce numéro propre comme clé pour le tableau $data
    $data[$numero_propre] = [
        'nom' => $nom_departement,
        'effectif' => $row['effectif_2025']
    ];
}

// pour debug
// fwrite($fichier, "\n--- Résultat ---\n");
// fwrite($fichier, print_r($data, true)); // version lisible
// fclose($fichier);

echo json_encode($data);

?>