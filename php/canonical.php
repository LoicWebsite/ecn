<?php
// Génère une balise canonique qui pointe vers la page actuelle SANS paramètres d'URL
// Cela aide Google à identifier la version "canonique" d'une page et évite les doublons

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$script = $_SERVER['SCRIPT_NAME'];

// URL canonique = le domaine + le chemin du fichier (SANS paramètres)
$canonical_url = $protocol . '://' . $host . $script;
?>
<link rel="canonical" href="<?php echo htmlspecialchars($canonical_url, ENT_QUOTES, 'UTF-8'); ?>" />
