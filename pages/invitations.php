<?php
session_start();
include 'header.php';

// Connexion à la base de données
$host = 'localhost';
$dbname = 'bricoconnect';
$username = 'root';
$password = '';
$utilisateur_id = $_SESSION['utilisateur_id'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupération des invitations en attente
    $stmt = $pdo->prepare("SELECT a.amitie_id, u.nom_utilisateur, u.photo_profil 
                           FROM amities a 
                           JOIN utilisateurs u ON a.utilisateur_id = u.utilisateur_id 
                           WHERE a.utilisateur_ami_id = :utilisateur_id AND a.statut = 'en_attente'");
    $stmt->execute([':utilisateur_id' => $utilisateur_id]);
    $invitations = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitations - BricoConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .content {
            max-width: 700px;
            margin: auto;
            padding: 20px;
        }
        .profile-pic {
            width: 50px;
            height: 50px;
            object-fit: cover;
        }
        .btn-accept, .btn-decline {
            color: #fff;
            padding: 6px 12px;
            border-radius: 5px;
            font-size: 0.9rem;
            margin-left: 5px;
        }
        .btn-accept {
            background-color: #28a745;
        }
        .btn-decline {
            background-color: #dc3545;
        }
        .invitation-card {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<div class="content">
    <h1 class="text-center mb-4"><i class="fas fa-user-friends"></i> Invitations</h1>
    <p class="text-center text-muted">Gérez vos invitations d'amis.</p>

    <?php if (count($invitations) > 0): ?>
        <div class="list-group">
            <?php foreach ($invitations as $invitation): ?>
                <div class="list-group-item invitation-card d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <img src="<?php echo $invitation['photo_profil'] ? htmlspecialchars($invitation['photo_profil']) : '../images/default_avatar.png'; ?>" alt="Photo de profil" class="rounded-circle profile-pic me-3">
                        <strong><?php echo htmlspecialchars($invitation['nom_utilisateur']); ?></strong>
                    </div>
                    <div>
                        <!-- Boutons accepter et refuser l'invitation -->
                        <form action="traitement_invitation.php" method="POST" class="d-inline">
                            <input type="hidden" name="amitie_id" value="<?php echo $invitation['amitie_id']; ?>">
                            <input type="hidden" name="action" value="accepter">
                            <button type="submit" class="btn btn-accept"><i class="fas fa-check"></i> Accepter</button>
                        </form>
                        <form action="traitement_invitation.php" method="POST" class="d-inline">
                            <input type="hidden" name="amitie_id" value="<?php echo $invitation['amitie_id']; ?>">
                            <input type="hidden" name="action" value="refuser">
                            <button type="submit" class="btn btn-decline"><i class="fas fa-times"></i> Refuser</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-center text-muted">Aucune invitation en attente.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>  
