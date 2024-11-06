<?php
// Démarrer la session seulement si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$dbname = 'bricoconnect';
$username = 'root';
$password = '';
$nb_notifications_non_vues = 0; // Initialiser le compteur de notifications non vues

if (isset($_SESSION['utilisateur_id'])) {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Compter les notifications non vues pour l'utilisateur connecté
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE utilisateur_id = :utilisateur_id AND vue = 0");
        $stmt->execute([':utilisateur_id' => $_SESSION['utilisateur_id']]);
        $nb_notifications_non_vues = $stmt->fetchColumn();
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BricoConnect</title>
    <link rel="stylesheet" href="../styles/style.css">
    <!-- Lien vers les icônes Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        .navbar.fixed-bottom {
            padding-top: 6px;
            padding-bottom: 6px;
            height: 70px;
        }
        .nav-pills .nav-link {
            font-size: 0.85rem;
            padding: 4px 0;
        }
        .nav-item i {
            font-size: 1.2rem;
        }
        .main-content {
            padding-bottom: 70px;
        }
        .notification-badge {
            position: absolute;
            top: 0px;
            right: 5px;
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>

<div class="main-content">
    <!-- Contenu de chaque page -->
</div>

<!-- Barre de navigation en bas de la page -->
<nav class="navbar fixed-bottom navbar-light bg-light">
    <ul class="nav nav-pills nav-fill w-100">
        <li class="nav-item">
            <a class="nav-link" href="home.php">
                <i class="bi bi-house-door-fill"></i>
                <span>Accueil</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="discussions.php">
                <i class="bi bi-chat-dots-fill"></i>
                <span>Discussions</span>
            </a>
        </li>
        <li class="nav-item position-relative">
            <a class="nav-link" href="notifications.php">
                <i class="bi bi-bell-fill"></i>
                <span>Notifications</span>
                <?php if ($nb_notifications_non_vues > 0): ?>
                    <span class="notification-badge"><?php echo $nb_notifications_non_vues; ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="invitations.php">
                <i class="bi bi-people-fill"></i>
                <span>Invitations</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="profil.php">
                <i class="bi bi-person-fill"></i>
                <span>Profil</span>
            </a>
        </li>
    </ul>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
