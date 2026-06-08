<?php 

// Nettoie un parametre texte libre.
// Regles: trim, longueur max UTF-8, suppression des caracteres dangereux,
// puis normalisation des espaces.
// Exemples:
// - "  CHU de Lyon<script>alert(1)</script>  " -> "CHU de Lyonscriptalert1script"
// - "Neuro   chir  " -> "Neuro chir"
function sanitizeTextParam($value, $maxLen = 256) {
    $value = trim((string)$value);
    $value = mb_substr($value, 0, $maxLen, 'UTF-8');
    $value = preg_replace('/[^\p{L}\p{N}\s._-]/u', '', $value);
    $value = preg_replace('/\s+/u', ' ', $value);
    return trim($value);
}

// Nettoie un parametre de type code technique.
// Regles: trim, longueur max, puis conservation stricte de [A-Za-z0-9_-].
// Exemples:
// - " DES_12-AB " -> "DES_12-AB"
// - "abc' OR 1=1 --" -> "abcOR11--"
function sanitizeCodeParam($value, $maxLen = 64) {
    $value = trim((string)$value);
    $value = substr($value, 0, $maxLen);
    return preg_replace('/[^A-Za-z0-9_-]/', '', $value);
}

// Construit une URL avec query string encodee de maniere sure (RFC3986).
// Exemples:
// - buildSafeUrl('liste.php', ['code' => 'DES-12', 'rang' => 1234])
//   -> "liste.php?code=DES-12&rang=1234"
// - buildSafeUrl('detail.php', ['chu' => 'AP-HP Paris'])
//   -> "detail.php?chu=AP-HP%20Paris"
function buildSafeUrl($path, $params = []) {
    if (empty($params)) {
        return $path;
    }
    return $path . '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
}

// Echappe une chaine pour un contexte HTML (texte/attribut).
// Exemples:
// - escapeHtml("<script>alert('x')</script>")
//   -> "&lt;script&gt;alert(&#039;x&#039;)&lt;/script&gt;"
// - escapeHtml('CHU "A" & B')
//   -> "CHU &quot;A&quot; &amp; B"
function escapeHtml($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

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
$specialite = isset($_GET['specialite']) ? sanitizeTextParam($_GET['specialite']) : "";

$code = isset($_GET['code']) ? sanitizeCodeParam($_GET['code']) : "inconnu";

$chu = isset($_GET['chu']) ? sanitizeTextParam($_GET['chu']) : "";

// Rang numérique (0 < rang < 15001)
$rang = isset($_GET['rang']) && is_numeric($_GET['rang']) && $_GET['rang'] > 0 && $_GET['rang'] < 15001
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