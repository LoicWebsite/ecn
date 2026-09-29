<?php
http_response_code(503);
header('Retry-After: 300');
?>
<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="noindex, nofollow">
    <title>Travaux en cours - Simulateur spécialités ECN</title>
    <?php
      include "php/favicon.php";
      include "php/style.php";
    ?>
  </head>
  <body id="hautdepage">
    <nav class="navbar navbar-dark bg-secondary">
      <a class="navbar-brand" href="choix-specialite-chu-celine-ecn-TRAVAUX.php">
        <img src="image/stethoscopeBlanc.svg" width="30" height="30" alt="" loading="eager">
        &nbsp;Simulateur spécialités
      </a>
    </nav>

    <main class="container text-center py-5">
      <section class="my-5 text-center" aria-labelledby="titre-travaux">
        <img src="image/interne.jpg" alt="" class="img-fluid mb-4" style="max-height: 280px;">
        <h1 id="titre-travaux" class="h3">Travaux en cours</h1>
        <p class="display-4 text-secondary my-4 d-flex justify-content-center align-items-center" aria-hidden="true"><i class="bi bi-tools"></i></p>
        <p class="lead mt-3 text-center">Le site est momentanément indisponible pour une mise à jour.</p>
        <p class="text-center">Il sera de nouveau opérationnel dans quelques minutes.</p>
        <p class="mt-4 text-center">Merci de votre compréhension et à très bientôt.</p>
      </section>
    </main>
  </body>
</html>
