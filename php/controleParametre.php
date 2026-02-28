<?php 

// --- BOOLEENS ---

// DEBUG ---
$debug = false;
if (isset($_GET['debug'])) {
    $debug = ($_GET['debug'] === "true") ? true : false;
}

// CESP
$cesp = "off";
if (isset($_GET['cesp'])) {
    $cesp = (($_GET['cesp'] === "on") || ($_GET['cesp'] === "1")) ? "on" : "off";
}

// --- LISTES BLANCHES ---
$allowed_references = ["2025","2024","2023","2022","2021","2020","2019","2018","2017"];
$allowed_types = ["chirurgie","medico-chirurgical","organe","transversal"];
$allowed_lieux = ["hopital","ville","autre"];
$allowed_internat = [3,4,5,6];
$allowed_benefices = ["benefice60","benefice100","benefice140","benefice500"];
$allowed_depuis = ["liste","tableau"];

// --- PARAMETRES FILTRES / SECURISES ---

// Paramètres “libres”
$specialite = isset($_GET['specialite']) ? trim($_GET['specialite']) : "";
$specialite = substr($specialite, 0, 256);

$code = isset($_GET['code']) ? trim($_GET['code']) : "inconnu";
$code = substr($code, 0, 256);

$chu = isset($_GET['chu']) ? trim($_GET['chu']) : "";
$chu = substr($chu, 0, 256);

// Rang numérique (0 < rang < 10000)
$rang = isset($_GET['rang']) && is_numeric($_GET['rang']) && $_GET['rang'] > 0 && $_GET['rang'] < 10000
    ? floor($_GET['rang'])
    : "rangIndifferent";

// Référence année
$reference = isset($_GET['reference']) && in_array($_GET['reference'], $allowed_references)
    ? $_GET['reference']
    : 2025;

// Type
$type = isset($_GET['type']) && in_array($_GET['type'], $allowed_types)
    ? $_GET['type']
    : "typeIndifferent";

// Lieu
$lieu = isset($_GET['lieu']) && in_array($_GET['lieu'], $allowed_lieux)
    ? $_GET['lieu']
    : "lieuIndifferent";

// Internat
$internat = isset($_GET['internat']) && in_array((int)$_GET['internat'], $allowed_internat)
    ? (int)$_GET['internat']
    : "internatIndifferent";

// Benefice
$benefice = isset($_GET['benefice']) && in_array($_GET['benefice'], $allowed_benefices)
    ? $_GET['benefice']
    : "beneficeIndifferent";

// Depuis
$depuis = isset($_GET['depuis']) && in_array($_GET['depuis'], $allowed_depuis)
    ? $_GET['depuis']
    : "liste";

?>