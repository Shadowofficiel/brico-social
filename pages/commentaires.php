<?php 
session_start();
if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: connexion.php");
    exit;
}

$host = 'localhost';
$dbname = 'bricoconnect';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['publication_id'])) {
        $publication_id = $_GET['publication_id'];

        // Récupération de la publication
        $stmt = $pdo->prepare("SELECT p.*, u.nom_utilisateur, u.photo_profil FROM publications p JOIN utilisateurs u ON p.utilisateur_id = u.utilisateur_id WHERE p.publication_id = :publication_id");
        $stmt->bindParam(':publication_id', $publication_id);
        $stmt->execute();
        $publication = $stmt->fetch(PDO::FETCH_ASSOC);

        // Gestion de l'ajout d'un commentaire ou d'une réponse
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['contenu_commentaire'])) {
            $contenu_commentaire = $_POST['contenu_commentaire'];
            $utilisateur_id = $_SESSION['utilisateur_id'];
            $parent_id = $_POST['parent_id'] ?? null;

            $stmt = $pdo->prepare("INSERT INTO commentaires (utilisateur_id, publication_id, contenu, parent_id, date_commentaire) VALUES (:utilisateur_id, :publication_id, :contenu, :parent_id, NOW())");
            $stmt->bindParam(':utilisateur_id', $utilisateur_id);
            $stmt->bindParam(':publication_id', $publication_id);
            $stmt->bindParam(':contenu', $contenu_commentaire);
            $stmt->bindParam(':parent_id', $parent_id);
            $stmt->execute();

            // Recharger la page pour afficher le nouveau commentaire
            header("Location: commentaires.php?publication_id=" . $publication_id);
            exit;
        }

        // Récupération des commentaires et de leurs réponses
        $stmt = $pdo->prepare("
            SELECT c.*, u.nom_utilisateur, u.photo_profil, u.utilisateur_id 
            FROM commentaires c 
            JOIN utilisateurs u ON c.utilisateur_id = u.utilisateur_id 
            WHERE c.publication_id = :publication_id 
            ORDER BY c.parent_id ASC, c.date_commentaire DESC
        ");
        $stmt->bindParam(':publication_id', $publication_id);
        $stmt->execute();
        $commentaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        echo "<p class='text-danger'>Publication non trouvée.</p>";
        exit;
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
    <title>Commentaires - BricoConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .main-color {
            color: #ff6600;
        }
        .btn-main {
            background-color: #ff6600;
            color: white;
            border: none;
        }
        .btn-main:hover {
            background-color: #e55d00;
        }
        .card-header-main {
            background-color: #ff6600;
            color: white;
        }
        .profile-image {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }
        .comment-bubble {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 10px;
        }
        .reply-button {
            color: #ff6600;
            cursor: pointer;
            font-size: 0.9rem;
        }
        .reply-button:hover {
            text-decoration: underline;
        }
        .back-btn {
            font-size: 1.5rem;
            color: #ff6600;
            cursor: pointer;
        }
        .back-btn:hover {
            color: #e55d00;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <!-- Bouton de retour vers la publication -->
    <a href="home.php" class="back-btn">&larr; Retour</a>


    <!-- Affichage de la publication avec photo et nom de l'auteur -->
    <div class="card mb-4 mt-2">
        <div class="card-header d-flex align-items-center card-header-main">
            <img src="<?php echo $publication['photo_profil'] ? '../uploads/' . htmlspecialchars($publication['photo_profil']) : '../images/default_avatar.png'; ?>" class="profile-image me-2" alt="Photo de profil">
            <div>
                <h5 class="mb-0"><?php echo htmlspecialchars($publication['nom_utilisateur']); ?></h5>
                <small class="text-light"><?php echo date('d M Y, H:i', strtotime($publication['date_creation'])); ?></small>
            </div>
        </div>
        <div class="card-body">
            <p class="card-text"><?php echo htmlspecialchars($publication['contenu']); ?></p>
            <?php if ($publication['image']): ?>
                <img src="../<?php echo htmlspecialchars($publication['image']); ?>" class="img-fluid mt-3" alt="Image de la publication">
            <?php endif; ?>
        </div>
    </div>

    <!-- Formulaire pour ajouter un commentaire -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title main-color">Ajouter un commentaire</h5>
            <form method="POST" class="mt-3">
                <input type="hidden" name="parent_id" id="parent_id" value="">
                <div class="mb-3">
                    <textarea id="contenu_commentaire" name="contenu_commentaire" class="form-control" rows="3" placeholder="Écrivez votre commentaire ici..." required></textarea>
                </div>
                <button type="submit" class="btn btn-main">Commenter</button>
            </form>
        </div>
    </div>

    <!-- Affichage des commentaires existants -->
    <h3 class="main-color">Commentaires</h3>
    <?php if (count($commentaires) > 0): ?>
        <?php foreach ($commentaires as $commentaire): ?>
            <?php if (!$commentaire['parent_id']): ?>
                <div class="comment-bubble mb-2">
                    <div class="d-flex align-items-center">
                        <img src="<?php echo $commentaire['photo_profil'] ? '../uploads/' . htmlspecialchars($commentaire['photo_profil']) : '../images/default_avatar.png'; ?>" class="profile-image me-2" alt="Photo de profil">
                        <h6 class="mb-0">
                            <?php 
                            if ($commentaire['utilisateur_id'] == $_SESSION['utilisateur_id']) {
                                echo "<strong>Vous</strong>";
                            } else {
                                echo "<a href='profil_utilisateur.php?utilisateur_id=" . $commentaire['utilisateur_id'] . "' class='text-decoration-none main-color'>" . htmlspecialchars($commentaire['nom_utilisateur']) . "</a>";
                            }
                            ?>
                        </h6>
                        <small class="text-muted ms-2"><?php echo date('d M Y, H:i', strtotime($commentaire['date_commentaire'])); ?></small>
                    </div>
                    <p class="card-text mt-2"><?php echo htmlspecialchars($commentaire['contenu']); ?></p>

                    <?php if ($commentaire['utilisateur_id'] != $_SESSION['utilisateur_id']): ?>
                        <div class="reply-button" onclick="setReply(<?php echo $commentaire['commentaire_id']; ?>, '<?php echo htmlspecialchars($commentaire['nom_utilisateur']); ?>')">Répondre</div>
                    <?php endif; ?>
                </div>

                <!-- Affichage des réponses -->
                <?php foreach ($commentaires as $reponse): ?>
                    <?php if ($reponse['parent_id'] == $commentaire['commentaire_id']): ?>
                        <div class="comment-bubble ms-4">
                            <div class="d-flex align-items-center">
                                <img src="<?php echo $reponse['photo_profil'] ? '../uploads/' . htmlspecialchars($reponse['photo_profil']) : '../images/default_avatar.png'; ?>" class="profile-image me-2" alt="Photo de profil">
                                <h6 class="mb-0">
                                    <?php 
                                    if ($reponse['utilisateur_id'] == $_SESSION['utilisateur_id']) {
                                        echo "<strong>Vous</strong>";
                                    } else {
                                        echo "<a href='profil_utilisateur.php?utilisateur_id=" . $reponse['utilisateur_id'] . "' class='text-decoration-none main-color'>" . htmlspecialchars($reponse['nom_utilisateur']) . "</a>";
                                    }
                                    ?>
                                </h6>
                                <small class="text-muted ms-2"><?php echo date('d M Y, H:i', strtotime($reponse['date_commentaire'])); ?></small>
                            </div>
                            <p class="card-text mt-2"><strong>@<?php echo htmlspecialchars($commentaire['nom_utilisateur']); ?>:</strong> <?php echo htmlspecialchars($reponse['contenu']); ?></p>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-muted">Aucun commentaire pour cette publication.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function setReply(parentId, userName) {
        document.getElementById('parent_id').value = parentId;
        document.getElementById('contenu_commentaire').placeholder = "Répondre à " + userName;
        document.getElementById('contenu_commentaire').focus();
    }
</script>
</body>
</html>
