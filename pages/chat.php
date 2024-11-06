<?php 
session_start();
include 'header.php';

if (!isset($_SESSION['utilisateur_id']) || !isset($_GET['ami_id'])) {
    header("Location: discussions.php");
    exit;
}

$utilisateur_id = $_SESSION['utilisateur_id'];
$ami_id = $_GET['ami_id'];

// Connexion à la base de données
$host = 'localhost';
$dbname = 'bricoconnect';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer les informations de l'ami
    $stmt = $pdo->prepare("SELECT nom_utilisateur, photo_profil FROM utilisateurs WHERE utilisateur_id = :ami_id");
    $stmt->execute([':ami_id' => $ami_id]);
    $ami = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ami) {
        echo "<p class='text-danger'>Utilisateur non trouvé.</p>";
        exit;
    }

    // Marquer les messages comme vus lorsqu'ils sont affichés
    $updateStatus = $pdo->prepare("UPDATE messages SET statut = 'vu' WHERE expediteur_id = :ami_id AND destinataire_id = :utilisateur_id AND statut = 'pas encore vu'");
    $updateStatus->execute([':ami_id' => $ami_id, ':utilisateur_id' => $utilisateur_id]);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discussion avec <?php echo htmlspecialchars($ami['nom_utilisateur']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
            background-color: white;
        }
        .chat-container {
            width: 100%;
            max-width: 800px;
            margin: auto;
            display: flex;
            flex-direction: column;
            height: 100vh;
            background-color: #ffffff;
        }
        .chat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            background-color: #ffffff;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 10;
            max-width: 800px;
        }
        .chat-header img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .chat-messages {
            flex-grow: 1;
            overflow-y: auto;
            padding: 15px;
            margin-top: 60px;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
        }
        .message {
            margin-bottom: 10px;
            max-width: 75%;
            padding: 10px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            word-wrap: break-word;
            display: inline-block;
            position: relative;
        }
        .message-sent {
            background-color: #ff6600;
            color: white;
            align-self: flex-end;
            text-align: right;
            border-top-right-radius: 0;
            max-width: calc(100% - 60px);
        }
        .message-received {
            background-color: #e0e0e0;
            color: black;
            align-self: flex-start;
            text-align: left;
            border-top-left-radius: 0;
            max-width: calc(100% - 60px);
        }
        .message img, .message video {
            max-width: 100%;
            border-radius: 10px;
            margin-top: 5px;
        }
        .message a {
            color: #007bff;
            text-decoration: underline;
        }
        .message-status {
            font-size: 0.75rem;
            color: gray;
            margin-top: 5px;
            text-align: right;
        }
        .chat-input {
            padding: 10px;
            background-color: #ffffff;
            border-top: 1px solid #ddd;
            position: sticky;
            bottom: 0;
            width: 100%;
            z-index: 10;
        }
        .chat-input form {
            display: flex;
            align-items: center;
            width: 100%;
        }
        .chat-input input[type="text"] {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 20px;
            margin-right: 10px;
            background-color: #f5f5f5;
            color: black;
        }
        .chat-input button {
            background-color: #ff6600;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 50%;
        }
    </style>
</head>
<body>

<div class="chat-container">
    <!-- En-tête du chat avec photo de profil de l'ami et bouton de fermeture -->
    <div class="chat-header">
        <div class="d-flex align-items-center">
            <img src="<?php echo $ami['photo_profil'] ?: '../images/default_avatar.png'; ?>" alt="Photo de profil">
            <h5 class="mb-0"><a href="profil_utilisateur.php?utilisateur_id=<?php echo $ami_id; ?>" class="text-decoration-none text-dark"><?php echo htmlspecialchars($ami['nom_utilisateur']); ?></a></h5>
        </div>
        <a href="discussions.php" class="text-decoration-none text-danger"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>

    <!-- Zone de messages -->
    <div class="chat-messages" id="chat-messages">
        <!-- Les messages sont chargés ici par AJAX -->
    </div>

    <!-- Zone de saisie du message avec possibilité d'ajouter un fichier -->
    <div class="chat-input">
        <form id="messageForm" enctype="multipart/form-data">
            <input type="text" name="message" id="message" placeholder="Écrivez votre message...">
            <label for="file-upload" class="btn btn-outline-secondary"><i class="fas fa-paperclip"></i></label>
            <input type="file" id="file-upload" name="file" style="display: none;">
            <button type="submit"><i class="fas fa-paper-plane"></i></button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function loadMessages() {
    $.ajax({
        url: 'load_messages.php',
        type: 'GET',
        data: { ami_id: <?php echo $ami_id; ?> },
        success: function (data) {
            var chatMessages = $('#chat-messages');
            var isScrolledToBottom = Math.abs(chatMessages[0].scrollHeight - chatMessages.scrollTop() - chatMessages.outerHeight()) < 1;

            chatMessages.html(data);
            if (isScrolledToBottom) {
                chatMessages.scrollTop(chatMessages[0].scrollHeight);
            }
        }
    });
}

// Charger les messages en temps réel toutes les 2 secondes
setInterval(loadMessages, 2000);

// Envoi du message et/ou fichier avec AJAX
$('#messageForm').on('submit', function (e) {
    e.preventDefault();
    var message = $('#message').val();
    var file = $('#file-upload')[0].files[0];
    var formData = new FormData();

    formData.append('ami_id', <?php echo $ami_id; ?>);
    if (message.trim() !== '') {
        formData.append('message', message);
    }
    if (file) {
        formData.append('file', file);
    }

    if (message.trim() !== '' || file) {
        $.ajax({
            url: 'send_message.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function () {
                $('#message').val('');
                $('#file-upload').val('');
                loadMessages();
                $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
            }
        });
    }
});

$(document).ready(function () {
    loadMessages();
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
