<?php
session_start();
include 'header.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: connexion.php");
    exit;
}

$utilisateur_id = $_SESSION['utilisateur_id'];

// Connexion à la base de données
$host = 'localhost';
$dbname = 'bricoconnect';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer la liste unique des amis avec le dernier message
    $stmt = $pdo->prepare("
        SELECT u.utilisateur_id, u.nom_utilisateur, u.photo_profil,
            (SELECT contenu FROM messages m 
             WHERE (m.expediteur_id = u.utilisateur_id AND m.destinataire_id = :utilisateur_id) 
                OR (m.expediteur_id = :utilisateur_id AND m.destinataire_id = u.utilisateur_id)
             ORDER BY m.date_envoi DESC LIMIT 1) AS dernier_message,
            (SELECT date_envoi FROM messages m 
             WHERE (m.expediteur_id = u.utilisateur_id AND m.destinataire_id = :utilisateur_id) 
                OR (m.expediteur_id = :utilisateur_id AND m.destinataire_id = u.utilisateur_id)
             ORDER BY m.date_envoi DESC LIMIT 1) AS date_dernier_message
        FROM utilisateurs u
        JOIN amities a ON (u.utilisateur_id = a.utilisateur_ami_id OR u.utilisateur_id = a.utilisateur_id)
        WHERE (a.utilisateur_id = :utilisateur_id OR a.utilisateur_ami_id = :utilisateur_id)
          AND a.statut = 1 
          AND u.utilisateur_id != :utilisateur_id
        GROUP BY u.utilisateur_id
        ORDER BY date_dernier_message DESC
    ");
    $stmt->execute([':utilisateur_id' => $utilisateur_id]);
    $amis = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discussions - BricoConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        .friend-list {
            max-width: 600px;
            margin: auto;
            padding-top: 20px;
        }
        .friend-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }
        .friend-item img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 15px;
        }
        .friend-details {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            margin-right: 10px;
        }
        .friend-details span {
            font-weight: bold;
            color: #333;
        }
        .message-preview {
            font-size: 0.9rem;
            color: #666;
            max-width: 400px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .message-time {
            font-size: 0.8rem;
            color: #999;
            align-self: flex-end;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <h1 class="text-center mb-4 text-orange"><i class="fas fa-comments"></i> Discussions</h1>
    <p class="text-center text-muted">Choisissez un ami pour démarrer une conversation.</p>

    <?php if (!empty($amis)): ?>
        <div class="friend-list list-group">
            <?php foreach ($amis as $ami): ?>
                <a href="chat.php?ami_id=<?php echo $ami['utilisateur_id']; ?>" class="list-group-item list-group-item-action friend-item">
                    <div class="d-flex align-items-center">
                        <img src="<?php echo $ami['photo_profil'] ?: '../images/default_avatar.png'; ?>" alt="Photo de profil">
                        <div class="friend-details">
                            <span><?php echo htmlspecialchars($ami['nom_utilisateur']); ?></span>
                            <p class="message-preview mb-0"><?php echo htmlspecialchars($ami['dernier_message'] ?? 'Aucun message'); ?></p>
                        </div>
                    </div>
                    <?php if ($ami['date_dernier_message']): ?>
                        <small class="message-time"><?php echo date('d M Y, H:i', strtotime($ami['date_dernier_message'])); ?></small>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-center text-muted">Vous n'avez pas encore d'amis avec qui discuter.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
