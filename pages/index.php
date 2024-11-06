<?php

session_start();


$host = 'localhost';
$dbname = 'bricoconnect';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $nb_notifications_non_vues = 0; 
    if (isset($_SESSION['utilisateur_id'])) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE utilisateur_id = :utilisateur_id AND vue = 0");
        $stmt->execute([':utilisateur_id' => $_SESSION['utilisateur_id']]);
        $nb_notifications_non_vues = $stmt->fetchColumn();
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Brico-Social</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        /* Global styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .main-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: white;
            color: black;
            padding: 10px 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .main-header img {
            height: 50px;
            margin-right: 10px;
        }
        .btn-primary {
            background-color: #FF7F50;
            border: none;
        }
        .btn-outline-primary {
            border-color: #FF7F50;
            color: #FF7F50;
        }
        .btn-primary:hover, .btn-outline-primary:hover {
            background-color: #e67341;
            border-color: #e67341;
        }
        .container {
            margin-top: 2rem;
        }
        .main-content h2 {
            font-weight: bold;
            color: #343a40;
        }
        .publication-image {
            width: 100%;
            height: auto;
            max-height: 400px;
            object-fit: cover;
            border-radius: 8px;
        }
        .nav-bottom {
            background-color: white;
            border-top: 1px solid #ddd;
            box-shadow: 0px -2px 5px rgba(0, 0, 0, 0.1);
        }
        .nav-item i {
            font-size: 1.3rem;
            color: #FF7F50;
        }
        .nav-link {
            color: #FF7F50;
            font-weight: 500;
        }
        .notification-badge {
            position: absolute;
            top: 5px;
            right: 15px;
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.75rem;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    
    <header class="main-header">
        <div class="d-flex align-items-center">
            <img src="../images/brico.png" alt="Logo de Brico-Social">
            <h1>Brico-Social</h1>
        </div>
        <div>
            <a href="connexion.php" class="btn btn-primary me-2">Se connecter</a>
            <a href="inscription.php" class="btn btn-outline-primary">Créer un compte</a>
        </div>
    </header>

    <div class="container">
        <h2 class="text-center mb-4">Explorez les publications de Brico-Social</h2>

       
        <div class="main-content">
            <?php include 'affichage_publications.php'; ?>
        </div>
    </div>

    
    <nav class="navbar fixed-bottom navbar-light bg-light">
        <ul class="nav nav-pills nav-fill w-100">
            <li class="nav-item">
                <a class="nav-link" href="home.php">
                    <i class="bi bi-house-door-fill"></i>
                    <span>Accueil</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="connexion.php">
                    <i class="bi bi-chat-dots-fill"></i>
                    <span>Discussions</span>
                </a>
            </li>
            <li class="nav-item position-relative">
                <a class="nav-link" href="connexion.php">
                    <i class="bi bi-bell-fill"></i>
                    <span>Notifications</span>
                    <?php if ($nb_notifications_non_vues > 0): ?>
                        <span class="notification-badge"><?php echo $nb_notifications_non_vues; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="connexion.php">
                    <i class="bi bi-people-fill"></i>
                    <span>Invitations</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="connexion.php">
                    <i class="bi bi-person-fill"></i>
                    <span>Connexion</span>
                </a>
            </li>
        </ul>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
