<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['utilisateur_id'])) {
    echo json_encode(['error' => 'Utilisateur non connecté.']);
    exit;
}

$host = 'localhost';
$dbname = 'bricoconnect';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_POST['publication_id'])) {
        $publication_id = $_POST['publication_id'];
        $utilisateur_id = $_SESSION['utilisateur_id'];

        // Vérifie si l'utilisateur a déjà liké cette publication
        $checkLike = $pdo->prepare("SELECT * FROM mentions_j_aime WHERE utilisateur_id = :utilisateur_id AND publication_id = :publication_id");
        $checkLike->execute([':utilisateur_id' => $utilisateur_id, ':publication_id' => $publication_id]);

        if ($checkLike->rowCount() === 0) {
            // Ajoute un like
            $stmt = $pdo->prepare("INSERT INTO mentions_j_aime (utilisateur_id, publication_id) VALUES (:utilisateur_id, :publication_id)");
            $stmt->execute([':utilisateur_id' => $utilisateur_id, ':publication_id' => $publication_id]);
            $user_has_liked = true;
        } else {
            // Supprime le like
            $stmt = $pdo->prepare("DELETE FROM mentions_j_aime WHERE utilisateur_id = :utilisateur_id AND publication_id = :publication_id");
            $stmt->execute([':utilisateur_id' => $utilisateur_id, ':publication_id' => $publication_id]);
            $user_has_liked = false;
        }

        // Compte le nombre de likes mis à jour
        $likeCountStmt = $pdo->prepare("SELECT COUNT(*) AS like_count FROM mentions_j_aime WHERE publication_id = :publication_id");
        $likeCountStmt->execute([':publication_id' => $publication_id]);
        $like_count = $likeCountStmt->fetch(PDO::FETCH_ASSOC)['like_count'];

        // Renvoie la réponse JSON
        echo json_encode([
            'new_like_count' => $like_count,
            'user_has_liked' => $user_has_liked
        ]);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
