<?php
// Connexion à la base de données MySQL
$pdo = new PDO("mysql:host=localhost;dbname=ecn", "USER", "PASSWORD");


// ***** SPECIALITE ****
//
// 1. Récupérer les rangs de chaque spécialité
$query = $pdo->query("SELECT Specialite, Rang FROM Classement2024 ORDER BY Specialite, Rang");
$data = [];
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $data[$row['Specialite']][] = $row['Rang'];
}

// 2. Calculer la médiane pour chaque spécialité
$medians = [];
foreach ($data as $specialite => $rangs) {
    sort($rangs); // Trier les rangs pour chaque spécialité
    $count = count($rangs);
    $median = ($count % 2 == 0) ? ($rangs[$count / 2 - 1] + $rangs[$count / 2]) / 2 : $rangs[floor($count / 2)];
    $medians[$specialite] = $median;
}

// 3. Mettre à jour la colonne Médiane Spécialité
$updateQuery = $pdo->prepare("UPDATE Classement2024 SET MedianeSpecialite = :median WHERE Specialite = :specialite");
foreach ($medians as $specialite => $median) {
    $updateQuery->execute([':median' => $median, ':specialite' => $specialite]);
}

echo "Médianes Spécilité mises à jour avec succès.\n";


// ***** GROUPE ****
//
// 1. Récupérer les rangs de chaque groupe
$query = $pdo->query("SELECT Groupe, Rang FROM Classement2024 ORDER BY Groupe, Rang");
$data = [];
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $data[$row['Groupe']][] = $row['Rang'];
}

// 2. Calculer la médiane pour chaque groupe
$medians = [];
foreach ($data as $groupe => $rangs) {
    sort($rangs); // Trier les rangs pour chaque groupe
    $count = count($rangs);
    $median = ($count % 2 == 0) ? ($rangs[$count / 2 - 1] + $rangs[$count / 2]) / 2 : $rangs[floor($count / 2)];
    $medians[$groupe] = $median;
}

// 3. Mettre à jour la colonne Médiane Groupe
$updateQuery = $pdo->prepare("UPDATE Classement2024 SET MedianeGroupe = :median WHERE Groupe = :groupe");
foreach ($medians as $groupe => $median) {
    $updateQuery->execute([':median' => $median, ':groupe' => $groupe]);
}

echo "Médianes Groupe mises à jour avec succès.\n";


// ***** CHU ****
//
// 1. Récupérer les rangs de chaque CHU
$query = $pdo->query("SELECT CHU, Rang FROM Classement2024 ORDER BY CHU, Rang");
$data = [];
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $data[$row['CHU']][] = $row['Rang'];
}

// 2. Calculer la médiane pour chaque CHU
$medians = [];
foreach ($data as $chu => $rangs) {
    sort($rangs); // Trier les rangs pour chaque CHU
    $count = count($rangs);
    $median = ($count % 2 == 0) ? ($rangs[$count / 2 - 1] + $rangs[$count / 2]) / 2 : $rangs[floor($count / 2)];
    $medians[$chu] = $median;
}

// 3. Mettre à jour la colonne Médiane CHU
$updateQuery = $pdo->prepare("UPDATE Classement2024 SET MedianeCHU = :median WHERE CHU = :chu");
foreach ($medians as $chu => $median) {
    $updateQuery->execute([':median' => $median, ':chu' => $chu]);
}

echo "Médianes Groupe mises à jour avec succès.\n";

?>
