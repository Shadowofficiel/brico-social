<?php 
session_start();
include 'header.php';

// Vérifiez si un utilisateur est connecté et qu'un ID utilisateur est passé en paramètre
if (!isset($_SESSION['utilisateur_id']) || !isset($_GET['utilisateur_id'])) {
    header("Location: home.php");
    exit;
}

$utilisateur_id = $_GET['utilisateur_id'];
$utilisateur_connecte_id = $_SESSION['utilisateur_id'];

// Connexion à la base de données
$host = 'localhost';
$dbname = 'bricoconnect';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupération des informations de l'utilisateur cible
    $stmt = $pdo->prepare("SELECT nom_utilisateur, email, bio, photo_profil FROM utilisateurs WHERE utilisateur_id = :utilisateur_id");
    $stmt->execute([':utilisateur_id' => $utilisateur_id]);
    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$utilisateur) {
        echo "<p class='text-danger'>Utilisateur non trouvé.</p>";
        exit;
    }

    // Vérification de l'état de l'amitié entre l'utilisateur connecté et l'utilisateur cible
    $stmt = $pdo->prepare("SELECT statut FROM amities WHERE (utilisateur_id = :utilisateur_connecte_id AND utilisateur_ami_id = :utilisateur_id) OR (utilisateur_id = :utilisateur_id AND utilisateur_ami_id = :utilisateur_connecte_id)");
    $stmt->execute([':utilisateur_connecte_id' => $utilisateur_connecte_id, ':utilisateur_id' => $utilisateur_id]);
    $amitie = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier si l'amitié est acceptée
    $est_ami = $amitie && $amitie['statut'] == 1; // Booléen pour le statut d'amitié

    // Récupération des publications de l'utilisateur cible
    $stmt = $pdo->prepare("SELECT contenu, image, date_creation FROM publications WHERE utilisateur_id = :utilisateur_id ORDER BY date_creation DESC");
    $stmt->execute([':utilisateur_id' => $utilisateur_id]);
    $publications = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - <?php echo htmlspecialchars($utilisateur['nom_utilisateur']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<div class="container mt-4">
    <div class="text-center mb-4">
        <!-- Affichage de la photo de profil ou d'une image par défaut -->
        <img src="<?php echo $utilisateur['photo_profil'] ? htmlspecialchars($utilisateur['photo_profil']) : '../images/default_avatar.png'; ?>" alt="Photo de profil" class="rounded-circle" style="width: 120px; height: 120px;">
        
        <!-- Affichage du nom de l'utilisateur -->
        <h2><?php echo htmlspecialchars($utilisateur['nom_utilisateur']); ?></h2>
        
        <!-- Affichage de la biographie si elle existe -->
        <?php if ($utilisateur['bio']): ?>
            <p class="text-muted"><?php echo htmlspecialchars($utilisateur['bio']); ?></p>
        <?php else: ?>
            <p class="text-muted">Cet utilisateur n'a pas encore ajouté de biographie.</p>
        <?php endif; ?>
    </div>

    <div class="text-center mb-4">
        <!-- Boutons d'interaction : Discuter ou Envoyer une demande d'ami -->
        <?php if ($est_ami): ?>
            <p class="text-success">Vous êtes déjà amis</p>
            <a href="discussions.php?utilisateur_id=<?php echo $utilisateur_id; ?>" class="btn btn-primary me-2"><i class="fas fa-comments"></i> Discuter</a>
        <?php else: ?>
            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#nonAmiModal"><i class="fas fa-comments"></i> Discuter</button>
            
            <?php if (!$amitie): ?>
                <form action="envoyer_invitation.php" method="POST" class="d-inline">
                    <input type="hidden" name="utilisateur_ami_id" value="<?php echo $utilisateur_id; ?>">
                    <button type="submit" class="btn btn-secondary"><i class="fas fa-user-plus"></i> Envoyer une demande d'ami</button>
                </form>
            <?php elseif ($amitie['statut'] == 0): ?>
                <button class="btn btn-warning" disabled>Demande en attente</button>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="text-center">
        <!-- Boutons de signalement et de blocage -->
        <a href="signaler_utilisateur.php?utilisateur_id=<?php echo $utilisateur_id; ?>" class="btn btn-danger me-2"><i class="fas fa-exclamation-triangle"></i> Signaler</a>
        <a href="bloquer_utilisateur.php?utilisateur_id=<?php echo $utilisateur_id; ?>" class="btn btn-dark"><i class="fas fa-ban"></i> Bloquer</a>
    </div>

    <!-- Section des publications de l'utilisateur -->
    <div class="mt-5">
        <h4>Publications de <?php echo htmlspecialchars($utilisateur['nom_utilisateur']); ?></h4>
        <?php if (count($publications) > 0): ?>
            <?php foreach ($publications as $publication): ?>
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($publication['contenu']); ?></p>
                        <?php if ($publication['image']): ?>
                            <img src="../<?php echo htmlspecialchars($publication['image']); ?>" class="img-fluid rounded" alt="Image de publication">
                        <?php endif; ?>
                        <small class="text-muted">Publié le <?php echo date('d M Y, H:i', strtotime($publication['date_creation'])); ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">Aucune publication à afficher.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Modale pour avertir que l'utilisateur n'est pas ami -->
<div class="modal fade" id="nonAmiModal" tabindex="-1" aria-labelledby="nonAmiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="nonAmiModalLabel">Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Vous n'êtes pas encore amis avec cet utilisateur. Veuillez envoyer une demande d'ami pour pouvoir discuter.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
