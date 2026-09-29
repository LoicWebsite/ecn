<?php
/**
 * Résumé dynamique d'une spécialité.
 * Les attributs stables viennent de Specialite ; les données annuelles viennent de Rang.
 */

$dbResume = isset($db) ? $db : openDatabase();
$codeResume = isset($CodeSpecialite) ? $CodeSpecialite : '';
if ($codeResume === '' && isset($code) && $code !== 'inconnu') {
    $codeResume = $code;
}
if ($codeResume === '' && isset($specialite) && strlen($specialite) === 3) {
    $codeResume = $specialite;
}

$specialiteResume = null;
$rangParAnnee = [];
$libelleSpecialite = '';

$specialiteStmt = $dbResume->prepare(
    'SELECT CodeSpecialite, Specialite, Benefice, Type, Nature, Lieu, DureeInternat
     FROM Specialite WHERE CodeSpecialite = :code'
);
$specialiteStmt->execute([':code' => $codeResume]);
$specialiteResume = $specialiteStmt->fetch(PDO::FETCH_ASSOC);

if ($specialiteResume) {
    $rangStmt = $dbResume->prepare(
        'SELECT Annee, CHU, Poste, CESP, Dernier, DernierCESP
         FROM Rang WHERE CodeSpecialite = :code
         ORDER BY Annee DESC, Dernier DESC, DernierCESP DESC, CHU'
    );
    $rangStmt->execute([':code' => $codeResume]);

    while ($rangRow = $rangStmt->fetch(PDO::FETCH_ASSOC)) {
        $annee = intval($rangRow['Annee']);
        if (!isset($rangParAnnee[$annee])) {
            $rangParAnnee[$annee] = [
                'poste' => 0,
                'cesp' => 0,
                'dernier' => 0,
                'chuDernier' => '',
                'dernierCesp' => 0,
                'chuDernierCesp' => '',
            ];
        }

        $rangParAnnee[$annee]['poste'] += intval($rangRow['Poste']);
        $rangParAnnee[$annee]['cesp'] += intval($rangRow['CESP']);

        if (intval($rangRow['Dernier']) > $rangParAnnee[$annee]['dernier']) {
            $rangParAnnee[$annee]['dernier'] = intval($rangRow['Dernier']);
            $rangParAnnee[$annee]['chuDernier'] = $rangRow['CHU'];
        }
        if (intval($rangRow['DernierCESP']) > $rangParAnnee[$annee]['dernierCesp']) {
            $rangParAnnee[$annee]['dernierCesp'] = intval($rangRow['DernierCESP']);
            $rangParAnnee[$annee]['chuDernierCesp'] = $rangRow['CHU'];
        }
    }
}

if ($specialiteResume) {
    $montantResume = new NumberFormatter('fr-FR', NumberFormatter::DECIMAL);
    $libelleSpecialiteResume = $specialiteResume['Specialite'];
    $libelleSpecialite = $libelleSpecialiteResume;
    $typeSpecialiteResume = getLibelleTypeNature($specialiteResume['Type'], $specialiteResume['Nature']);
    $lieuSpecialiteResume = getLibelleLieu($specialiteResume['Lieu']);

    echo "<div id='resume' class='container resume-specialite'>";
    echo "<h1 id='resume-title' class='h5 resume-title'>";
    echo "<a class='h5' data-toggle='collapse' aria-expanded='false' aria-controls='detail' href='#detail'>";
    echo "<i id='symbole' class='bi bi-plus-circle-fill'></i>&nbsp; Détail de la spécialité " . escapeHtml($libelleSpecialiteResume) . "...";
    echo "</a></h1>";

    echo "<div id='detail' class='collapse'>";
    echo "<details class='resume-section' open><summary>Identité et exercice</summary>";
    echo "<div class='resume-identity-grid'>";
    echo "<div class='resume-label'>Spécialité</div><div>" . escapeHtml($libelleSpecialiteResume) . "</div>";
    echo "<div class='resume-label'>Code</div><div><strong>" . escapeHtml($specialiteResume['CodeSpecialite']) . "</strong></div>";
    echo "<div class='resume-label'>Type</div><div>" . escapeHtml($typeSpecialiteResume) . "</div>";
    echo "<div class='resume-label'>Durée</div><div>" . intval($specialiteResume['DureeInternat']) . " ans</div>";
    echo "<div class='resume-label'>Lieu</div><div>" . escapeHtml($lieuSpecialiteResume) . "</div>";
    echo "<div class='resume-label'>Revenu net</div><div>" . $montantResume->format(intval($specialiteResume['Benefice'])) . " €</div>";
    echo "</div></details>";

    echo "<details class='resume-section' open><summary>Rangs et postes récents</summary>";
    echo "<div class='resume-rang-table'>";
    echo "<div class='resume-rang-grid resume-grid-head'><div>Année</div><div>Postes</div><div>CESP</div><div>Dernier</div><div>CHU</div><div>Dernier CESP</div><div>CHU</div></div>";
    foreach ([2025, 2024] as $anneeResume) {
        $valeurs = $rangParAnnee[$anneeResume] ?? ['poste' => 0, 'cesp' => 0, 'dernier' => 0, 'chuDernier' => '', 'dernierCesp' => 0, 'chuDernierCesp' => ''];
        $rangPrincipal = $valeurs['dernier'] > 0 ? $montantResume->format($valeurs['dernier']) : '-';
        $rangCesp = $valeurs['dernierCesp'] > 0 ? $montantResume->format($valeurs['dernierCesp']) : '-';
        echo "<div class='resume-rang-grid resume-grid-row'><div data-label='Année'><strong>" . $anneeResume . "</strong></div><div data-label='Postes'>" . $montantResume->format($valeurs['poste']) . "</div><div data-label='CESP'>" . $montantResume->format($valeurs['cesp']) . "</div><div class='resume-rang-value' data-label='Dernier'>" . $rangPrincipal . "</div><div class='resume-rang-chu' data-label='CHU'>" . escapeHtml($valeurs['chuDernier']) . "</div><div class='resume-rang-value' data-label='Dernier CESP'>" . $rangCesp . "</div><div class='resume-rang-chu' data-label='CHU CESP'>" . escapeHtml($valeurs['chuDernierCesp']) . "</div></div>";
    }
    echo "</details>";

    echo "<details class='resume-section'><summary>Historique des rangs principaux</summary>";
    echo "<div class='resume-history'>";
    foreach ($rangParAnnee as $anneeResume => $valeurs) {
        if ($anneeResume >= 2024) {
            continue;
        }
        $dernier = $valeurs['dernier'] > 0 ? $montantResume->format($valeurs['dernier']) : '-';
        echo "<div><strong>" . $anneeResume . "</strong><span class='resume-rang-value'>" . $dernier . "</span><span class='resume-rang-chu'>" . escapeHtml($valeurs['chuDernier']) . "</span></div>";
    }
    echo "</div></details>";
    echo "</div></div>";

    echo "<script>
        (function () {
            var toggle = document.querySelector('#resume-title > a');
            var icon = document.querySelector('#resume-title #symbole');
            if (!toggle || !icon || !window.MutationObserver) return;
            new MutationObserver(function () {
                var ouvert = toggle.getAttribute('aria-expanded') === 'true';
                icon.classList.toggle('bi-plus-circle-fill', !ouvert);
                icon.classList.toggle('bi-dash-circle-fill', ouvert);
            }).observe(toggle, { attributes: true, attributeFilter: ['aria-expanded'] });
        }());
    </script>";
}
?>
