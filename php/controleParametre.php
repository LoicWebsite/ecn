<?php 

		// initialisation des variables
		$debug = false;
		$code= "inconnu";
		$chu= "";
		$rang = "rangIndifferent";
		$reference = 2024;
		$type = "typeIndifferent";
		$cesp = "off";
		$lieu = "lieuIndifferent";
		$internat = "internatIndifferent";
		$benefice = "beneficeIndifferent";
		$depuis="liste";
		$specialite = "";

		// récupération du paramètre pour passer en mode debug (par défaut pas de debug)
		if (isset($_GET['debug']))  {
			if ($_GET['debug'] == "true") {
				$debug=true;
			}
		}

		// récupération des paramètres du formulaire et contrôle des valeurs passées
		if (isset($_GET['specialite']))  {
			$specialite = $_GET['specialite'];
		}

		if (isset($_GET['code']))  {
			$code = $_GET['code'];
		}

		if (isset($_GET['chu']))  {
			$chu = $_GET['chu'];
		}

		if (isset($_GET['rang']))  {
			if (is_numeric($_GET['rang'])) {
				if (($_GET['rang'] > 0) and ($_GET['rang'] < 10000)) {
					$rang = floor($_GET['rang']);
				}
			}
		}

		if (isset($_GET['reference']))  {
			if (($_GET['reference'] == "2024") or ($_GET['reference'] == "2023") or ($_GET['reference'] == "2022") or ($_GET['reference'] == "2021") or ($_GET['reference'] == "2020") or ($_GET['reference'] == "2019") or ($_GET['reference'] == "2018") or ($_GET['reference'] == "2017")) {
				$reference = $_GET['reference'];
			}
		}
		
		if (isset($_GET['type']))  {
			if (($_GET['type'] == "chirurgie") or ($_GET['type'] == "medico-chirurgical") or ($_GET['type'] == "organe") or ($_GET['type'] == "transversal")) {
				$type = $_GET['type'];
			}
		}
		
		if (isset($_GET['cesp']))  {
			if (($_GET['cesp'] == "on") or ($_GET['cesp'] == "1")) {
				$cesp = "on";
			}
		}

		if (isset($_GET['lieu']))  {
			if (($_GET['lieu'] == "hopital") or ($_GET['lieu'] == "ville") or ($_GET['lieu'] == "autre")) {
				$lieu = $_GET['lieu'];
			}
		}
		
		if (isset($_GET['internat']))  {
			if (($_GET['internat'] == 3) or ($_GET['internat'] == 4) or ($_GET['internat'] == 5) or ($_GET['internat'] == 6)) {
				$internat = $_GET['internat'];
			}
		}
		
		if (isset($_GET['benefice']))  {
			if (($_GET['benefice'] == "benefice60") or ($_GET['benefice'] == "benefice100") or ($_GET['benefice'] == "benefice140") or ($_GET['benefice'] == "benefice500")) {
				$benefice = $_GET['benefice'];
			}
		}

		if (isset($_GET['depuis']))  {
			if ($_GET['depuis'] == "tableau") {
				$depuis = "tableau";
			}
		}

?>