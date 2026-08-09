<?php

/*************************************************************************************************
 * Script orchestrateur annuel: mise a jour Poste/CESP en une seule execution.
 *
 * Objectif
 * --------
 * Eviter de modifier 4 scripts chaque annee. Ce script prend l'annee en parametre
 * et met a jour automatiquement les colonnes annuelles dans les tables Rang et Specialite.
 *
 * Etapes executees (dans cet ordre)
 * ---------------------------------
 * 1) Rang.PosteYYYY      <- table temporaire Poste (CodeSpecialite, CHU, Poste)
 * 2) Specialite.PosteYYYY <- somme de Rang.PosteYYYY par CodeSpecialite
 * 3) Rang.CESPYYYY       <- table temporaire CESP (CodeSpecialite, CHU, CESP)
 * 4) Specialite.CESPYYYY  <- somme de Rang.CESPYYYY par CodeSpecialite
 *
 * Parametres HTTP
 * ---------------
 * - annee (optionnel): format 20xx, ex: 2026
 *                      defaut: annee courante
 * - debug (optionnel): true|false
 *                      defaut: true
 *
 * Exemples
 * --------
 * - /ECN/php/insererPosteCESPAnnee.php?annee=2026
 * - /ECN/php/insererPosteCESPAnnee.php?annee=2026&debug=true
 *
 * Prerequis
 * ---------
 * - Les colonnes annuelles doivent exister:
 *   Rang.PosteYYYY, Rang.CESPYYYY, Specialite.PosteYYYY, Specialite.CESPYYYY
 * - Les tables temporaires Poste et CESP doivent etre prealablement chargees via CSV.
 *
 * Sortie
 * ------
 * - Resume texte avec le nombre de lignes mises a jour par etape.
 *************************************************************************************************/

header("Content-Type: text/plain; charset=utf-8");

require_once __DIR__ . "/fonctionECN.php";

function parseYearFromRequest(): int {
    $year = (int) date("Y");
    if (!isset($_GET["annee"])) {
        return $year;
    }

    $raw = trim((string) $_GET["annee"]);
    if (!preg_match('/^20[0-9]{2}$/', $raw)) {
        die("Parametre annee invalide (format attendu: 20xx)\n");
    }

    return (int) $raw;
}

function parseDebugFromRequest(): bool {
    if (!isset($_GET["debug"])) {
        return true;
    }

    return $_GET["debug"] === "true";
}

function assertColumnExists(PDO $db, string $table, string $column): void {
    $safeTable = str_replace('`', '``', $table);
    $safeColumn = str_replace('`', '``', $column);
    $sql = "SHOW COLUMNS FROM `{$safeTable}` LIKE :column";

    $stmt = $db->prepare($sql);
    $stmt->execute([":column" => $safeColumn]);

    if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
        die("Colonne manquante: {$table}.{$column}\n");
    }
}

function updateRangFromPoste(PDO $db, string $rangPosteColSql, bool $debug): int {
    $count = 0;
    $selectSql = "SELECT CodeSpecialite, CHU, Poste FROM Poste";
    if ($debug) {
        echo "SQL SELECT Poste: {$selectSql}\n";
    }

    $rows = $db->query($selectSql);
    $updateSql = "UPDATE Rang SET {$rangPosteColSql} = :nb WHERE CHU = :chu AND CodeSpecialite = :code";
    $updateStmt = $db->prepare($updateSql);

    while ($row = $rows->fetch(PDO::FETCH_ASSOC)) {
        $nb = (int) $row["Poste"];
        $code = $row["CodeSpecialite"];
        $chu = $row["CHU"];

        if ($debug) {
            echo "POSTE > {$code} | {$chu} | {$nb}\n";
        }

        $updateStmt->execute([
            ":nb" => $nb,
            ":chu" => $chu,
            ":code" => $code,
        ]);

        $count += 1;
    }

    return $count;
}

function updateSpecialiteFromRangPoste(PDO $db, string $rangPosteColSql, string $spePosteColSql, bool $debug): int {
    $count = 0;
    $selectSql = "SELECT CodeSpecialite AS Code, SUM({$rangPosteColSql}) AS Poste FROM Rang GROUP BY CodeSpecialite";
    if ($debug) {
        echo "SQL SELECT Rang->Specialite Poste: {$selectSql}\n";
    }

    $rows = $db->query($selectSql);
    $updateSql = "UPDATE Specialite SET {$spePosteColSql} = :nb WHERE CodeSpecialite = :code";
    $updateStmt = $db->prepare($updateSql);

    while ($row = $rows->fetch(PDO::FETCH_ASSOC)) {
        $code = $row["Code"];
        $nb = (int) $row["Poste"];

        if ($debug) {
            echo "SPE POSTE > {$code} | {$nb}\n";
        }

        $updateStmt->execute([
            ":nb" => $nb,
            ":code" => $code,
        ]);

        $count += 1;
    }

    return $count;
}

function updateRangFromCesp(PDO $db, string $rangCespColSql, bool $debug): int {
    $count = 0;
    $selectSql = "SELECT CodeSpecialite, CHU, CESP FROM CESP";
    if ($debug) {
        echo "SQL SELECT CESP: {$selectSql}\n";
    }

    $rows = $db->query($selectSql);
    $updateSql = "UPDATE Rang SET {$rangCespColSql} = :nb WHERE CHU = :chu AND CodeSpecialite = :code";
    $updateStmt = $db->prepare($updateSql);

    while ($row = $rows->fetch(PDO::FETCH_ASSOC)) {
        $raw = $row["CESP"];
        if ($raw === null || $raw === "" || $raw === "NULL") {
            continue;
        }

        $nb = (int) $raw;
        $code = $row["CodeSpecialite"];
        $chu = $row["CHU"];

        if ($debug) {
            echo "CESP > {$code} | {$chu} | {$nb}\n";
        }

        $updateStmt->execute([
            ":nb" => $nb,
            ":chu" => $chu,
            ":code" => $code,
        ]);

        $count += 1;
    }

    return $count;
}

function updateSpecialiteFromRangCesp(PDO $db, string $rangCespColSql, string $speCespColSql, bool $debug): int {
    $count = 0;
    $selectSql = "SELECT CodeSpecialite AS Code, SUM({$rangCespColSql}) AS CESP FROM Rang GROUP BY CodeSpecialite";
    if ($debug) {
        echo "SQL SELECT Rang->Specialite CESP: {$selectSql}\n";
    }

    $rows = $db->query($selectSql);
    $updateSql = "UPDATE Specialite SET {$speCespColSql} = :nb WHERE CodeSpecialite = :code";
    $updateStmt = $db->prepare($updateSql);

    while ($row = $rows->fetch(PDO::FETCH_ASSOC)) {
        $code = $row["Code"];
        $nb = (int) $row["CESP"];

        if ($debug) {
            echo "SPE CESP > {$code} | {$nb}\n";
        }

        $updateStmt->execute([
            ":nb" => $nb,
            ":code" => $code,
        ]);

        $count += 1;
    }

    return $count;
}

$debug = parseDebugFromRequest();
$annee = parseYearFromRequest();

$rangPosteCol = "Poste" . $annee;
$rangCespCol = "CESP" . $annee;
$spePosteCol = "Poste" . $annee;
$speCespCol = "CESP" . $annee;

$rangPosteColSql = "`" . $rangPosteCol . "`";
$rangCespColSql = "`" . $rangCespCol . "`";
$spePosteColSql = "`" . $spePosteCol . "`";
$speCespColSql = "`" . $speCespCol . "`";

if ($debug) {
    echo "********** debut orchestrateur annuel **********\n";
    echo "Annee = {$annee}\n";
}

$db = openDatabase();

// Verifie en amont la presence des colonnes annuelles attendues.
assertColumnExists($db, "Rang", $rangPosteCol);
assertColumnExists($db, "Rang", $rangCespCol);
assertColumnExists($db, "Specialite", $spePosteCol);
assertColumnExists($db, "Specialite", $speCespCol);

try {
    $db->beginTransaction();

    $nbPosteRang = updateRangFromPoste($db, $rangPosteColSql, $debug);
    $nbPosteSpecialite = updateSpecialiteFromRangPoste($db, $rangPosteColSql, $spePosteColSql, $debug);
    $nbCespRang = updateRangFromCesp($db, $rangCespColSql, $debug);
    $nbCespSpecialite = updateSpecialiteFromRangCesp($db, $rangCespColSql, $speCespColSql, $debug);

    $db->commit();

    echo "\n===== Resume =====\n";
    echo "Rang <= Poste: {$nbPosteRang} lignes\n";
    echo "Specialite <= SUM(Rang.Poste): {$nbPosteSpecialite} lignes\n";
    echo "Rang <= CESP: {$nbCespRang} lignes\n";
    echo "Specialite <= SUM(Rang.CESP): {$nbCespSpecialite} lignes\n";
    echo "Statut: OK\n";
} catch (PDOException $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    echo "Erreur SQL: " . $e->getMessage() . "\n";
}

$db = null;

if ($debug) {
    echo "********** fin orchestrateur annuel **********\n";
}
