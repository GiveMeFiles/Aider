<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 
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
    /* Paste your CSS code here */
    body {
  background: #1D1F20;
}

        .button-container {
  height: auto; /* Remove fixed height from container */
  width: 100%; 
  position: relative; 
  text-align: center;
  margin-top: 15px; /* Adjust top margin as needed */
}

.button {
  background: #2B2D2F;
  height: 80px;
  width: 300px;
  text-align: center;
  position: relative;
  top: 0; /* Remove vertical positioning */
  margin: 0 auto; /* Center horizontally within the container */
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
    <link href="https://fonts.googleapis.com/css?family=Poppins:600" rel="stylesheet">
</head>
<body>
<div class="container">
    <h2>Création de Ticket GLPI</h2>
    
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
    $message = ""; // Initialize $message

    if ($sessionResponse['httpCode'] == 200 && isset($sessionResponse['response']['session_token'])) {
        $sessionToken = $sessionResponse['response']['session_token'];

        // Check if the submit button was pressed
        if (isset($_POST['submitTicket'])) {  // Assuming your submit button has name="submitTicket"
            // Get form data 
            $entity = $_POST['entity']; 
            $type = $_POST['type'];
            $title = $_POST['title'];
            $description = $_POST['description'];
            $priority = $_POST['priority'];

            // Choix des entités 
            $entityID = [
                'Greencell' => 90,
                'Greensea' => 11,
                'Greentech' => 9,
                'IntelligentDrink' => 88
            ];
            $entities_id = $entityID[$entity];

            //ID des Utilisateurs
            $technicianUserID = '135'; // ID du technicien assigné
            $supervisorUserID = '6'; // ID du superviseur
            
            // Determine ticket type
            $ticketType = $type == 'Incident' ? 1 : 2; 

            // Création du tableau de données du ticket
            $ticketData = [
                'input' => [
                    'date_creation' => getCurrentDateTime(), 
                    'id' => null,
                    'entities_id' => $entities_id,
                    'name' => $title, // Use submitted title
                    'date' => getCurrentDateTime(), 
                    'closedate' => null,
                    'solvedate' => null,
                    'date_mod' => getCurrentDateTime(), 
                    'users_id_lastupdater' => 135,
                    'status' => 2,
                    'users_id_recipient' => 137,
                    'requesttypes_id' => 3,
                    '_users_id_assign' => $technicianUserID, 
                    '_users_id_observer' => $supervisorUserID,
                    'content' => $description, // Use submitted description
                    'urgency' => 3,
                    'impact' => 3,
                    'priority' => $priority, // Use submitted priority
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
        } 
    } else {
        $message = 'Échec de l\'initialisation de la session.';
    }
    ?>
    <?php if ($message): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" id="glpiForm">
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

            <main class="button-container"> 
                <div class="button"> 
    <div class="text">Soumettre le Ticket</div>
  </div>
  <div class="progress-bar"></div>
                <svg x="0px" y="0px" viewBox="0 0 25 30" style="enable-background:new 0 0 25 30;">
    <path class="check" class="st0" d="M2,19.2C5.9,23.6,9.4,28,9.4,28L23,2"/>
  </svg>
</main>
        <input type="hidden" name="submitTicket" value="1">
    </form>
</div>

  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/animejs/2.0.2/anime.js'></script>
<script>
// ... (Your Anime.js code) ... 
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
  // Submit the form after the animation
  setTimeout(function() {
    $("#glpiForm").submit();
  }, 3000); // Adjust delay if needed based on animation duration
});

$(".text").click(function() {
  basicTimeline.play();
  // Submit the form after the animation
  setTimeout(function() {
    $("#glpiForm").submit();
  }, 3000); // Adjust delay if needed based on animation duration
});
</script>
</body>
</html>
