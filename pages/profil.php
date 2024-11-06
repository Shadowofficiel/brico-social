<?php
session_start();
include 'header.php';

// Connexion à la base de données pour récupérer les informations de l'utilisateur
$host = 'localhost';
$dbname = 'bricoconnect';
$username = 'root';
$password = '';
$utilisateur_id = $_SESSION['utilisateur_id'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer les informations du profil
    $stmt = $pdo->prepare("SELECT nom_utilisateur, email, bio, photo_profil FROM utilisateurs WHERE utilisateur_id = :utilisateur_id");
    $stmt->execute([':utilisateur_id' => $utilisateur_id]);
    $profil = $stmt->fetch(PDO::FETCH_ASSOC);

    // Mise à jour de la biographie
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bio'])) {
        $nouvelle_bio = $_POST['bio'];
        $stmt = $pdo->prepare("UPDATE utilisateurs SET bio = :bio WHERE utilisateur_id = :utilisateur_id");
        $stmt->execute([':bio' => $nouvelle_bio, ':utilisateur_id' => $utilisateur_id]);
        $profil['bio'] = $nouvelle_bio; // Met à jour localement pour l'affichage
        $_SESSION['message'] = "Votre biographie a été mise à jour avec succès.";
    }

    // Mise à jour de la photo de profil
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['photo_profil'])) {
        $target_dir = "../uploads/";
        $photo_tmp = $_FILES['photo_profil']['tmp_name'];
        $photo_name = $target_dir . basename($_FILES['photo_profil']['name']);

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (move_uploaded_file($photo_tmp, $photo_name)) {
            $stmt = $pdo->prepare("UPDATE utilisateurs SET photo_profil = :photo_profil WHERE utilisateur_id = :utilisateur_id");
            $stmt->execute([':photo_profil' => $photo_name, ':utilisateur_id' => $utilisateur_id]);
            $profil['photo_profil'] = $photo_name;
            $_SESSION['message'] = "Votre photo de profil a été mise à jour avec succès.";
        } else {
            $_SESSION['message'] = "Erreur lors de l'enregistrement de la photo de profil.";
        }
    }

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - BricoConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .content {
            max-width: 700px;
            margin: auto;
            padding: 20px;
        }
        .profile-pic {
            width: 120px;
            height: 120px;
            object-fit: cover;
        }
        .btn-custom {
            background-color: #FF8C00;
            color: white;
            transition: background-color 0.3s;
        }
        .btn-custom:hover {
            background-color: #e07b00;
        }
        .btn-photo-modify {
            margin-top: 15px;
            color: #fff;
            background-color: #FF8C00;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 0.9rem;
            display: block;
            width: 100%;
            margin-bottom:10px;
            
        }
        .btn-photo-modify:hover {
            background-color: orange;
        }
    </style>
</head>
<body>

<div class="content">
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info">
            <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <!-- Profil de l'utilisateur -->
    <div class="text-center mb-4">
        <img src="<?php echo $profil['photo_profil'] ? htmlspecialchars($profil['photo_profil']) : '../images/default_avatar.png'; ?>" alt="Photo de profil" class="rounded-circle profile-pic mb-2">
        
        <!-- Bouton stylisé pour modifier la photo de profil -->
        <button class="btn btn-photo-modify" onclick="togglePhotoForm()">Modifier la photo de profil</button>

        <!-- Formulaire de modification de photo (caché par défaut) -->
        <form id="photoForm" action="profil.php" method="POST" enctype="multipart/form-data" style="display: none; margin-top: 15px;">
            <input type="file" name="photo_profil" class="form-control mb-2" accept="image/*" required>
            <button type="submit" class="btn btn-custom btn-sm">Enregistrer la photo</button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="togglePhotoForm()">Annuler</button>
        </form>

        <h2><?php echo htmlspecialchars($profil['nom_utilisateur']); ?></h2>
        <p class="text-muted"><?php echo htmlspecialchars($profil['email']); ?></p>
    </div>

    <!-- Section de la biographie -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-user-edit"></i> À propos de moi</h5>

            <?php if (!empty($profil['bio'])): ?>
                <p id="bioText"><?php echo htmlspecialchars($profil['bio']); ?></p>
                <button class="btn btn-custom btn-sm" onclick="toggleBioForm()">Modifier la bio</button>
            <?php endif; ?>

            <!-- Formulaire pour ajouter ou modifier la bio -->
            <form id="bioForm" action="profil.php" method="POST" style="display: <?php echo empty($profil['bio']) ? 'block' : 'none'; ?>;">
                <div class="mb-3">
                    <textarea name="bio" class="form-control" rows="3" placeholder="Ajoutez une courte biographie..."><?php echo htmlspecialchars($profil['bio'] ?? ''); ?></textarea>
                </div>
                <button type="submit" class="btn btn-custom btn-sm">Enregistrer</button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="toggleBioForm()">Annuler</button>
            </form>
        </div>
    </div>

    <!-- Paramètres de confidentialité et de compte -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-cogs"></i> Paramètres du compte</h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-user-shield"></i> Paramètres de confidentialité</span>
                    <a href="confidentialite.php" class="text-decoration-none"><i class="fas fa-chevron-right"></i></a>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-lock"></i> Sécurité du compte</span>
                    <a href="securite.php" class="text-decoration-none"><i class="fas fa-chevron-right"></i></a>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-bell"></i> Notifications</span>
                    <a href="notifications.php" class="text-decoration-none"><i class="fas fa-chevron-right"></i></a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Déconnexion -->
    <div class="text-center mt-4">
        <a href="deconnexion.php" class="btn btn-outline-danger"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleBioForm() {
        document.getElementById('bioForm').style.display = document.getElementById('bioForm').style.display === 'none' ? 'block' : 'none';
        document.getElementById('bioText').style.display = document.getElementById('bioForm').style.display === 'block' ? 'none' : 'block';
    }

    function togglePhotoForm() {
        const photoForm = document.getElementById('photoForm');
        photoForm.style.display = photoForm.style.display === 'none' ? 'block' : 'none';
    }
</script>
</body>
</html>
