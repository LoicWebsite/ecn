<?php
header('Content-Type: application/json; charset=utf-8');

try {
    // connexion à la base de données
    include_once "fonctionECN.php";  // Inclure le fichier contenant openDatabase()
    $pdo = openDatabase();

    // Lecture du paramètre 'specialite' depuis l'URL.
    $specialite = $_GET['specialite'] ?? 'toutes';

    // Requête SQL pour toutes les spécialité ou bien une seule passée en paramètre.
    if ($specialite === 'toutes') {
        // Si la spécialité est "toutes", on récupère toutes les données.
        $sql = "SELECT code_departement, Departement, AVG(Densite_2025) AS Densite FROM Densite
                GROUP BY code_departement, Departement
                ORDER BY code_departement";
        $params = [];
    } else {
        // Sinon, on filtre par la spécialité demandée.
        $sql = "SELECT code_departement, Departement, Densite_2025 AS Densite FROM Densite
                WHERE Specialite = :specialite
                ORDER BY code_departement";
        $params[':specialite'] = $specialite;
    }

    // Exécution de la requête
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // Créer un tableau associatif avec le Code_Departement comme clé.
    $data = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Formater le numéro de département seulement s'il est numérique et sur un seul chiffre
        $code = $row['code_departement'];
        if (is_numeric($code) && intval($code) < 10) {
            $code_departement_formate = sprintf('%02d', $code);
        } else {
            $code_departement_formate = $code;
        }

        $data[$code_departement_formate] = [
            'Departement' => $row['Departement'],
            'Densite_2025' => $row['Densite']
        ];
    }

    // Encodage en JSON et envoi au client
    echo json_encode($data);

    // // --- POUR DEBOGAGE - A SUPPRIMER EN PRODCUTION ---
    // // Nom du fichier pour le log
    // $logFile = 'log-LireDensite.txt';
    
    // // Contenu à écrire dans le fichier
    // $logContent = "Requête SQL exécutée : " . $sql . "\n";
    // $logContent .= "Résultat JSON : " . json_encode($data) . "\n\n";
    
    // // Écrire dans le fichier (en ajoutant le nouveau contenu à la fin du fichier)
    // file_put_contents($logFile, $logContent, FILE_APPEND);
    // // --- FIN DE L'AJOUT POUR DEBOGAGE ---

} catch (PDOException $e) {
    // Gestion des erreurs de base de données
    http_response_code(500);
    echo json_encode(['error' => 'Erreur de base de données : ' . $e->getMessage()]);
}
?>