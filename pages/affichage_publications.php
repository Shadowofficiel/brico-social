<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$dbname = 'bricoconnect';
$username = 'root';
$password = '';

$utilisateur_id_connecte = $_SESSION['utilisateur_id'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupération des publications avec le nombre de mentions "J'aime" et de commentaires, et vérification si l'utilisateur a déjà liké
    $stmt = $pdo->prepare("
        SELECT p.*, u.nom_utilisateur, u.photo_profil,
            (SELECT COUNT(*) FROM mentions_j_aime WHERE publication_id = p.publication_id) AS nombre_jaimes,
            (SELECT COUNT(*) FROM commentaires WHERE publication_id = p.publication_id) AS nombre_commentaires,
            EXISTS(SELECT 1 FROM mentions_j_aime WHERE utilisateur_id = :utilisateur_id AND publication_id = p.publication_id) AS deja_aime
        FROM publications p
        JOIN utilisateurs u ON p.utilisateur_id = u.utilisateur_id
        ORDER BY p.date_creation DESC
    ");
    $stmt->execute([':utilisateur_id' => $utilisateur_id_connecte]);
    $publications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($publications as $publication) {
        echo "<div class='card mb-3 shadow-sm'>";
        echo "<div class='card-body'>";

        // En-tête de la publication avec photo de profil
        echo "<div class='d-flex align-items-center mb-2'>";
        $photo_profil = $publication['photo_profil'] ? htmlspecialchars($publication['photo_profil']) : "../images/default_avatar.png";
        echo "<img src='$photo_profil' class='rounded-circle me-2' style='width: 40px; height: 40px;' alt='Photo de profil'>";

        echo "<div>";
        $nom_affiche = $publication['utilisateur_id'] == $utilisateur_id_connecte ? "Vous" : htmlspecialchars($publication['nom_utilisateur']);
        $lien_profil = $publication['utilisateur_id'] == $utilisateur_id_connecte ? "profil.php" : "profil_utilisateur.php?utilisateur_id=" . $publication['utilisateur_id'];
        echo "<h6 class='mb-0'><a href='$lien_profil' class='text-decoration-none text-dark'>$nom_affiche</a></h6>";
        echo "<small class='text-muted'>" . date('d M Y, H:i', strtotime($publication['date_creation'])) . "</small>";
        echo "</div></div>";

        // Contenu de la publication
        echo "<p class='card-text mt-3'>" . htmlspecialchars($publication['contenu']) . "</p>";
        if ($publication['image']) {
            echo "<div class='mb-3'><img src='../" . htmlspecialchars($publication['image']) . "' class='img-fluid rounded' alt='Image de la publication'></div>";
        }

        // Bouton de "J'aime" avec état et compteur
        $like_class = $publication['deja_aime'] ? 'btn-primary' : 'btn-outline-primary';
        $like_icon = $publication['deja_aime'] ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-up';
        echo "<div class='d-flex justify-content-around mt-3'>";
        echo "<button onclick='toggleLike(" . $publication['publication_id'] . ")' class='btn $like_class btn-sm d-flex align-items-center' id='like-btn-" . $publication['publication_id'] . "'>";
        echo "<i class='bi $like_icon me-1'></i> J'aime <span id='like-count-" . $publication['publication_id'] . "' class='badge bg-primary ms-1'>" . $publication['nombre_jaimes'] . "</span>";
        echo "</button>";
        echo "<a href='commentaires.php?publication_id=" . $publication['publication_id'] . "' class='btn btn-outline-secondary btn-sm d-flex align-items-center'><i class='bi bi-chat-left-text me-1'></i> Commenter <span class='badge bg-secondary ms-1'>" . $publication['nombre_commentaires'] . "</span></a>";
        echo "<a href='traitement_partage.php?publication_id=" . $publication['publication_id'] . "' class='btn btn-outline-success btn-sm d-flex align-items-center'><i class='bi bi-share me-1'></i> Reposter</a>";
        echo "</div>";

        echo "</div></div>";
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function toggleLike(publication_id) {
    $.ajax({
        url: 'traitement_like.php',
        type: 'POST',
        data: { publication_id: publication_id },
        success: function(response) {
            const likeCountElement = $('#like-count-' + publication_id);
            const likeButton = $('#like-btn-' + publication_id);
            
            // Mise à jour du compteur de "J'aime"
            likeCountElement.text(response.new_like_count);

            // Bascule de l'état du bouton (like/unlike)
            if (response.user_has_liked) {
                likeButton.removeClass('btn-outline-primary').addClass('btn-primary');
                likeButton.find('i').removeClass('bi-hand-thumbs-up').addClass('bi-hand-thumbs-up-fill');
            } else {
                likeButton.removeClass('btn-primary').addClass('btn-outline-primary');
                likeButton.find('i').removeClass('bi-hand-thumbs-up-fill').addClass('bi-hand-thumbs-up');
            }
        },
        error: function() {
            alert('Erreur lors de l\'ajout du "J\'aime".');
        }
    });
}
</script>
