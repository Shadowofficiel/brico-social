<?php
// Vérifiez et démarrez la session si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirigez vers la page de connexion si l'utilisateur n'est pas connecté
if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: connexion.php");
    exit;
}

$utilisateur_id = $_SESSION['utilisateur_id'];
$nom_utilisateur = $_SESSION['nom_utilisateur'];

// Connexion à la base de données
$host = 'localhost';
$dbname = 'bricoconnect';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérifiez si l'utilisateur a déjà une photo de profil ou s'il s'agit de la première connexion
    $stmt = $pdo->prepare("SELECT photo_profil, premiere_connexion FROM utilisateurs WHERE utilisateur_id = :utilisateur_id");
    $stmt->execute([':utilisateur_id' => $utilisateur_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si l'utilisateur n'a pas de photo de profil ou s'il s'agit de la première connexion, afficher la modale
    $has_profile_image = !empty($result['photo_profil']);
    $is_first_login = $result['premiere_connexion'];
    $profil_image = $has_profile_image ? $result['photo_profil'] : "../images/default_avatar.png";

    // Traitement de la recherche
    $search_results = [];
    if (isset($_GET['search_username']) && !empty($_GET['search_username'])) {
        $search_term = "%" . $_GET['search_username'] . "%";
        $search_stmt = $pdo->prepare("SELECT utilisateur_id, nom_utilisateur, photo_profil FROM utilisateurs WHERE nom_utilisateur LIKE :search_term AND utilisateur_id != :utilisateur_id LIMIT 10");
        $search_stmt->execute([':search_term' => $search_term, ':utilisateur_id' => $utilisateur_id]);
        $search_results = $search_stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil</title>
    <!-- Lien vers les styles Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    
    <style>
        .main-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #ffffff;
            padding: 10px 15px;
            color: #FF7F50;
            border-bottom: 1px solid #ddd;
        }
        .main-header img {
            height: 50px;
            margin-right: 15px;
        }
        .search-form {
            display: inline-block;
            position: relative;
        }
        .search-form input[type="text"] {
            display: none;
            position: absolute;
            top: 0;
            right: 35px;
            width: 150px;
        }
        .search-form.active input[type="text"] {
            display: inline;
        }
        .btn-primary {
            background-color: #FF7F50;
            border-color: #FF7F50;
        }
        .btn-primary:hover {
            background-color: #e67341;
            border-color: #e67341;
        }
        
    </style>
</head>
<body>
    <!-- Barre de navigation avec le logo et icône de recherche -->
    <header class="main-header">
        <div class="d-flex align-items-center">
            <img src="../images/brico.png" alt="Logo ">
            <h1 class="h4 mb-0">SocialBrico</h1>
        </div>
        <form action="home.php" method="GET" class="search-form" id="searchForm">
            <input type="text" name="search_username" class="form-control" placeholder="Rechercher..." />
            <button type="button" class="btn btn-link" onclick="toggleSearch()"><i class="bi bi-search" style="color: #FF7F50;"></i></button>
        </form>
    </header>

    <?php include 'header.php'; ?>

    <div class="container mt-4 mb-5">
        <!-- Message de bienvenue -->
        <div class="text-center mb-4">
            <h1 class="h4" style="color: #FF7F50;">Bienvenue, <?php echo htmlspecialchars($nom_utilisateur); ?> !</h1>
        </div>

        <!-- Résultats de recherche -->
        <?php if (!empty($search_results)): ?>
            <div class="search-results mb-4">
                <h5 class="text-muted">Résultats de recherche :</h5>
                <?php foreach ($search_results as $user): ?>
                    <div class="d-flex align-items-center mb-2">
                        <img src="<?php echo $user['photo_profil'] ?: '../images/default_avatar.png'; ?>" alt="Photo de profil" class="rounded-circle" style="width: 40px; height: 40px; margin-right: 10px;">
                        <a href="profil_utilisateur.php?utilisateur_id=<?php echo $user['utilisateur_id']; ?>" class="text-decoration-none"><?php echo htmlspecialchars($user['nom_utilisateur']); ?></a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif (isset($_GET['search_username'])): ?>
            <p class="text-muted">Aucun utilisateur trouvé avec ce nom.</p>
        <?php endif; ?>

        <!-- Formulaire de publication -->
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <h5 class="card-title text-center"><i class="bi bi-pencil-square"></i> Publier un message</h5>
                <form action="traitement_publication.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <textarea id="contenu" name="contenu" class="form-control" rows="3" placeholder="Quoi de neuf, <?php echo htmlspecialchars($nom_utilisateur); ?> ?" required></textarea>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <label for="image" class="form-label mb-0"><i class="bi bi-image text-success me-2"></i> Ajouter une photo</label>
                        <input type="file" id="image" name="image" class="form-control-file" accept="image/*">
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-send-fill"></i> Publier</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Section des publications -->
        <div class="publications-section">
            <?php include 'affichage_publications.php'; ?>
        </div>

        <!-- Bouton de déconnexion -->
        <div class="text-center mt-5">
            <a href="deconnexion.php" class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-right"></i> Déconnexion</a>
        </div>
    </div>

    <!-- Modale pour l'ajout de photo de profil si c'est la première connexion ou si aucune photo n'est définie -->
    <?php if (!$has_profile_image || $is_first_login): ?>
    <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="profileModalLabel">Ajouter une photo de profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Vous n'avez pas encore ajouté de photo de profil. Souhaitez-vous en ajouter une maintenant ou plus tard ?</p>
                    <div class="d-flex justify-content-center mt-4">
                        <button type="button" class="btn btn-primary me-2" data-bs-toggle="collapse" data-bs-target="#uploadSection">Ajouter maintenant</button>
                        <a href="#" onclick="setDefaultProfilePicture()" class="btn btn-secondary">Plus tard</a>
                    </div>
                    <div id="uploadSection" class="collapse mt-4">
                        <form action="traitement_photo_profil.php" method="POST" enctype="multipart/form-data">
                            <label for="photo_profil" class="form-label">Choisissez une photo :</label>
                            <input type="file" id="photo_profil" name="photo_profil" class="form-control-file" accept="image/*" required>
                            <button type="submit" class="btn btn-success mt-2">Enregistrer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Lien vers les scripts Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSearch() {
            const form = document.getElementById('searchForm');
            form.classList.toggle('active');
            form.querySelector('input').focus();
        }
    </script>
</body>
</html>
