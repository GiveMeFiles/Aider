here are my two codes , the first one is my php and i want you to replace the button with the second code, do one prompt do not stop before giving the full code, do not justify, only the code:

First code : 

<?php
// Configuration de l'API GLPI
define('API_URL', 'http://glpi.greentech.lan/apirest.php/');
define('USER_TOKEN', 'k3x86yyz3ou6wsmcnti7zauw1766r33gz7nq5id3');
define('APP_TOKEN', 'q05h0px54u7uuq4g2wbby66g1czbcil43uuir9fu');

// Fonction pour effectuer les requêtes API
function apiRequest($url, $method = 'GET', $data = null, $sessionToken = null) {
    $headers = [
        'Content-Type: application/json',
        'App-Token: ' . APP_TOKEN,
        'Authorization: user_token ' . USER_TOKEN
    ];

    if ($sessionToken) {
        $headers[] = 'Session-Token: ' . $sessionToken;
    }

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, API_URL . $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);

    if ($data) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    }

    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    return ['response' => json_decode($response, true), 'httpCode' => $httpCode];
}

// Fonction pour obtenir la date actuelle ou une date personnalisée
function getCurrentDateTime($customDateTime = null) {
    if ($customDateTime) {
        return date('Y-m-d H:i:s', strtotime($customDateTime));
    } else {
        return date('Y-m-d H:i:s');
    }
}

// Étape 1 : Obtenir le jeton de session
$sessionResponse = apiRequest('initSession', 'GET');

if ($sessionResponse['httpCode'] == 200 && isset($sessionResponse['response']['session_token'])) {
    $sessionToken = $sessionResponse['response']['session_token'];

    // Choix des entités avec Greentech comme valeur par défaut
    $entity = isset($_POST['entity']) ? $_POST['entity'] : 'Greentech'; 
    
    // Correspondance entre les entités et leurs ID
    $entityID = [
        'Greencell' => 90,
        'Greensea' => 11,
        'Greentech' => 9,
        'IntelligentDrink' => 88
    ];

	//ID des Utilisateurs
	$technicianUserID = '135'; // ID du technicien assigné
	$supervisorUserID = '6'; // ID du superviseur
	

    // Vérification si l'entité sélectionnée existe dans le tableau, sinon, utiliser Greentech par défaut
    $entities_id = array_key_exists($entity, $entityID) ? $entityID[$entity] : $entityID['Greentech'];

    $ticketType = $_POST['type'] == 'Incident' ? 1 : 2; // 1 pour Incident, 2 pour Demande

    // Création du tableau de données du ticket
    $ticketData = [
        'input' => [
            'date_creation' => getCurrentDateTime(), // Utilise la date actuelle
            'id' => null,
            'entities_id' => $entities_id,
            'name' => $_POST['title'],
            'date' => getCurrentDateTime(), // Utilise la date actuelle
            'closedate' => null,
            'solvedate' => null,
            'date_mod' => getCurrentDateTime(), // Utilise la date actuelle
            'users_id_lastupdater' => 135,
            'status' => 2,
            'users_id_recipient' => 137,
            'requesttypes_id' => 3,
            '_users_id_assign' => $technicianUserID, // ID du technicien assigné
       		'_users_id_observer' => $supervisorUserID, // ID du superviseur
            'content' => $_POST['description'],
            'urgency' => 3,
            'impact' => 3,
            'priority' => $_POST['priority'],
            'itilcategories_id' => 0,
            'type' => $ticketType,
            'solutiontypes_id' => 0,
            'solution' => null,
            'global_validation' => 1,
            'slts_ttr_id' => 0,
            'slts_tto_id' => 0,
            'ttr_slalevels_id' => 0,
            'due_date' => null,
            'time_to_own' => null,
            'begin_waiting_date' => null,
            'sla_waiting_duration' => 0,
            'waiting_duration' => 0,
            'close_delay_stat' => 0,
            'solve_delay_stat' => 0,
            'takeintoaccount_delay_stat' => 1,
            'actiontime' => 0,
            'is_deleted' => 0,
            'locations_id' => 0,
            'validation_percent' => 0,
        ]
    ];

    // Envoi de la requête de création de ticket
    $result = apiRequest('Ticket', 'POST', $ticketData, $sessionToken);

    if ($result['httpCode'] == 201) {
        $message = "Ticket créé avec succès. ID du ticket: " . $result['response']['id'];
    } elseif (isset($result['response']['message']) && strpos($result['response']['message'], 'app_token') !== false) {
        $message = "Erreur : paramètre app_token manquant. Veuillez vérifier la configuration.";
    } else {
        $message = "Erreur lors de la création du ticket : " . json_encode($result['response']);
    }
} else {
    die('Échec de l\'initialisation de la session.');
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel='shortcut icon' type='images/x-icon' href='/pics/favicon.ico' >
<title>Création de Ticket GLPI</title>
<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}
.container {
    max-width: 500px;
    margin: 50px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}
h2 {
    text-align: center;
}
form {
    margin-top: 20px;
}
label {
    display: block;
    margin-bottom: 5px;
}
input[type="text"],
textarea,
select {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}
button {
    width: 100%;
    padding: 10px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
button:hover {
    background-color: #0056b3;
}
</style>
</head>
<body>

<div class="container">
    <h2>Création de Ticket GLPI</h2>
    
    <?php if ($message): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="entity">Entité:</label>
        <select id="entity" name="entity">
            <option value="Greentech">Greentech</option>
            <option value="Greencell">Greencell</option>
            <option value="Greensea">Greensea</option>
            <option value="IntelligentDrink">Intelligent Drink</option>
        </select>
        
        <label for="type">Type de Ticket:</label>
        <select id="type" name="type">
            <option value="Incident">Incident</option>
            <option value="Request">Demande</option>
        </select>
        
        <label for="title">Titre:</label>
        <input type="text" id="title" name="title">
        
        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="4" cols="50"></textarea>
        
        <label for="priority">Priorité:</label>
        <select id="priority" name="priority">
            <option value="6">Majeure</option>
            <option value="5">Très haute</option>
            <option value="4">Haute</option>
            <option value="3" selected>Moyenne</option>
            <option value="2">Basse</option>
            <option value="1">Très basse</option>
        </select>
        
        <button type="submit">Soumettre Ticket</button>
    </form>
</div>

</body>
</html>


Second code : 

<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>GLPI Ticket</title>
  <link rel="stylesheet" href="style.css">
  <style>
    /* Paste your CSS code here */
    body {
  background: #1D1F20;
}

main {
  height: 100vh;
  width: 100vw;
}

.button {
  background: #2B2D2F;
  height: 80px;
  width: 300px;
  text-align: center;
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  left: 0;
  right: 0;
  margin: 0 auto;
  cursor: pointer;
  border-radius: 4px;
}

.text {
  font: bold 1.25rem/1 poppins;
  color: #71DFBE;
  position: absolute;
  top: 50%;
  transform: translateY(-52%);
  left: 0;
  right: 0;
}

.progress-bar {
  position: absolute;
  height: 10px;
  width: 0;
  right: 0;
  top: 50%;
  left: 50%;
  border-radius: 200px;
  transform: translateY(-50%) translateX(-50%);
  background: #505357;
}

svg {
  width: 30px;
  position: absolute;
  top: 50%;
  transform: translateY(-50%) translateX(-50%);
  left: 50%;
  right: 0;
}

.check {
  fill: none;
  stroke: #FFFFFF;
  stroke-width: 3;
  stroke-linecap: round;
  stroke-linejoin: round;
}
  </style>
</head>
<body>
<!-- partial:index.partial.html -->
<link href="https://fonts.googleapis.com/css?family=Poppins:600" rel="stylesheet">


<main>
  <div class="button">
    <div class="text">Soumettre le Ticket</div>
  </div>
  <div class="progress-bar"></div>
  <svg x="0px" y="0px"
	 viewBox="0 0 25 30" style="enable-background:new 0 0 25 30;">
    <path class="check" class="st0" d="M2,19.2C5.9,23.6,9.4,28,9.4,28L23,2"/>
  </svg>
</main>
<!-- partial -->
  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/animejs/2.0.2/anime.js'></script><script  src="script.js"></script>
<script>
  // Paste your JavaScript code here
  var basicTimeline = anime.timeline({
  autoplay: false
});

var pathEls = $(".check");
for (var i = 0; i < pathEls.length; i++) {
  var pathEl = pathEls[i];
  var offset = anime.setDashoffset(pathEl);
  pathEl.setAttribute("stroke-dashoffset", offset);
}

basicTimeline
  .add({
    targets: ".text",
    duration: 1,
    opacity: "0"
  })
  .add({
    targets: ".button",
    duration: 1300,
    height: 10,
    width: 300,
    backgroundColor: "#2B2D2F",
    border: "0",
    borderRadius: 100
  })
  .add({
    targets: ".progress-bar",
    duration: 2000,
    width: 300,
    easing: "linear"
  })
  .add({
    targets: ".button",
    width: 0,
    duration: 1
  })
  .add({
    targets: ".progress-bar",
    width: 80,
    height: 80,
    delay: 500,
    duration: 750,
    borderRadius: 80,
    backgroundColor: "#71DFBE"
  })
  .add({
    targets: pathEl,
    strokeDashoffset: [offset, 0],
    duration: 200,
    easing: "easeInOutSine"
  });

$(".button").click(function() {
  basicTimeline.play();
});

$(".text").click(function() {
  basicTimeline.play();
});
</script>
</body>
</html>


