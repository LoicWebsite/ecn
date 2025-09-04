<?php
header('Content-Type: application/json; charset=utf-8');

try {
    // connexion à la base de données
    include_once "fonctionECN.php";  // Inclure le fichier contenant openDatabase()
    $pdo = openDatabase();

    // Requête SQL pour obtenir des valeurs uniques et triées
    $sql = "SELECT DISTINCT Specialite FROM Densite ORDER BY Specialite";

    // Exécution de la requête
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Récupération de toutes les lignes
    $data = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Encodage en JSON et envoi
    echo json_encode($data);

} catch (PDOException $e) {
    // Gestion des erreurs
    http_response_code(500); // Envoie un code d'erreur HTTP 500
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

?>