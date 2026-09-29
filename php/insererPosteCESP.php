<?php

/*************************************************************************************************
 * Script orchestrateur annuel: mise a jour Poste/CESP en une seule execution.
 *
 * Objectif
 * --------
 * Eviter de modifier 4 scripts chaque annee. Ce script prend l'annee en parametre,
 * alimente les lignes annuelles de Rang.
 *
 * Etapes executees (dans cet ordre)
 * ---------------------------------
 * 1) Rang.Poste pour Annee=YYYY <- table temporaire Poste (CodeSpecialite, CHU, Poste)
 * 2) Rang.CESP pour Annee=YYYY  <- table temporaire CESP (CodeSpecialite, CHU, CESP)
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
 * - /ECN/php/insererPosteCESP.php?annee=2026
 * - /ECN/php/insererPosteCESP.php?annee=2026&debug=true
 *
 * Prerequis
 * ---------
 * - Rang doit utiliser une ligne par CodeSpecialite, CHU et Annee.
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

function updateRangFromPoste(PDO $db, int $annee, bool $debug): int {
    $count = 0;
    $selectSql = "SELECT CodeSpecialite, CHU, Poste FROM Poste";
    if ($debug) {
        echo "SQL SELECT Poste: {$selectSql}\n";
    }

    $rows = $db->query($selectSql);
    $updateSql = "INSERT INTO Rang (CodeSpecialite, CHU, Annee, Poste)
            VALUES (:code, :chu, :annee, :nb)
            ON DUPLICATE KEY UPDATE Poste = VALUES(Poste)";
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
            ":annee" => $annee,
        ]);

        $count += 1;
    }

    return $count;
}

function updateRangFromCesp(PDO $db, int $annee, bool $debug): int {
    $count = 0;
    $selectSql = "SELECT CodeSpecialite, CHU, CESP FROM CESP";
    if ($debug) {
        echo "SQL SELECT CESP: {$selectSql}\n";
    }

    $rows = $db->query($selectSql);
    $updateSql = "INSERT INTO Rang (CodeSpecialite, CHU, Annee, CESP)
            VALUES (:code, :chu, :annee, :nb)
            ON DUPLICATE KEY UPDATE CESP = VALUES(CESP)";
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
            ":annee" => $annee,
        ]);

        $count += 1;
    }

    return $count;
}

$debug = parseDebugFromRequest();
$annee = parseYearFromRequest();

if ($debug) {
    echo "********** debut orchestrateur annuel **********\n";
    echo "Annee = {$annee}\n";
}

$db = openDatabase();

try {
    $db->beginTransaction();

    $nbPosteRang = updateRangFromPoste($db, $annee, $debug);
    $nbCespRang = updateRangFromCesp($db, $annee, $debug);

    $db->commit();

    echo "\n===== Resume =====\n";
    echo "Rang <= Poste: {$nbPosteRang} lignes\n";
    echo "Rang <= CESP: {$nbCespRang} lignes\n";
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
