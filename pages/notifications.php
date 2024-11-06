<?php
session_start();
include 'header.php';

$host = 'localhost';
$dbname = 'bricoconnect';
$username = 'root';
$password = '';
$utilisateur_id = $_SESSION['utilisateur_id'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Mettre à jour les notifications pour les marquer comme vues
    $stmt = $pdo->prepare("UPDATE notifications SET vue = 1 WHERE utilisateur_id = :utilisateur_id");
    $stmt->execute([':utilisateur_id' => $utilisateur_id]);

    // Récupération des notifications (likes, commentaires, invitations, messages)
    $stmt = $pdo->prepare("
        SELECT 'like' AS type, l.utilisateur_id, u.nom_utilisateur, l.publication_id, l.date_jaime AS date
        FROM mentions_j_aime l
        JOIN utilisateurs u ON l.utilisateur_id = u.utilisateur_id
        JOIN publications p ON l.publication_id = p.publication_id
        WHERE p.utilisateur_id = :utilisateur_id

        UNION ALL

        SELECT 'comment' AS type, c.utilisateur_id, u.nom_utilisateur, c.publication_id, c.date_commentaire AS date
        FROM commentaires c
        JOIN utilisateurs u ON c.utilisateur_id = u.utilisateur_id
        JOIN publications p ON c.publication_id = p.publication_id
        WHERE p.utilisateur_id = :utilisateur_id

        UNION ALL

        SELECT 'invitation' AS type, a.utilisateur_id, u.nom_utilisateur, NULL AS publication_id, a.date_demande AS date
        FROM amities a
        JOIN utilisateurs u ON a.utilisateur_id = u.utilisateur_id
        WHERE a.utilisateur_ami_id = :utilisateur_id AND a.statut = 'en_attente'

        UNION ALL

        SELECT 'message' AS type, m.expediteur_id AS utilisateur_id, u.nom_utilisateur, NULL AS publication_id, m.date_envoi AS date
        FROM messages m
        JOIN utilisateurs u ON m.expediteur_id = u.utilisateur_id
        WHERE m.destinataire_id = :utilisateur_id
        ORDER BY date DESC
    ");
    $stmt->execute([':utilisateur_id' => $utilisateur_id]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - BricoConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .notification-item {
            border-bottom: 1px solid #ddd;
            padding: 10px;
            display: flex;
            align-items: center;
        }
        .notification-item:last-child {
            border-bottom: none;
        }
        .notification-icon {
            font-size: 1.5rem;
            margin-right: 15px;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <h1 class="text-center"><i class="fas fa-bell"></i> Notifications</h1>
    <p class="text-center text-muted">Voici vos notifications récentes.</p>

    <?php if (count($notifications) > 0): ?>
        <?php foreach ($notifications as $notification): ?>
            <div class="notification-item">
                <div class="notification-icon">
                    <?php
                    switch ($notification['type']) {
                        case 'like':
                            echo '<i class="fas fa-thumbs-up text-primary"></i>';
                            break;
                        case 'comment':
                            echo '<i class="fas fa-comment text-success"></i>';
                            break;
                        case 'invitation':
                            echo '<i class="fas fa-user-plus text-warning"></i>';
                            break;
                        case 'message':
                            echo '<i class="fas fa-envelope text-info"></i>';
                            break;
                    }
                    ?>
                </div>      

                <div>
                    <?php if ($notification['type'] == 'like'): ?>
                        <strong><?php echo htmlspecialchars($notification['nom_utilisateur']); ?></strong> a aimé votre <a href="home.php?id=<?php echo $notification['publication_id']; ?>">publication</a>.
                    <?php elseif ($notification['type'] == 'comment'): ?>
                        <strong><?php echo htmlspecialchars($notification['nom_utilisateur']); ?></strong> a commenté votre <a href="home.php?id=<?php echo $notification['publication_id']; ?>">publication</a>.
                    <?php elseif ($notification['type'] == 'invitation'): ?>
                        <strong><?php echo htmlspecialchars($notification['nom_utilisateur']); ?></strong> vous a envoyé une <a href="invitations.php">demande d'ami</a>.
                    <?php elseif ($notification['type'] == 'message'): ?>
                        <strong><?php echo htmlspecialchars($notification['nom_utilisateur']); ?></strong> vous a envoyé un <a href="chat.php?ami_id=<?php echo $notification['utilisateur_id']; ?>">message</a>.
                    <?php endif; ?>
                    <br><small class="text-muted"><?php echo date('d M Y, H:i', strtotime($notification['date'])); ?></small>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-center text-muted">Aucune notification récente.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
