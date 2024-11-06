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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $utilisateur_id = $_SESSION['utilisateur_id'];
        $contenu = $_POST['contenu'];
        $image = null;

        // Vérification et traitement de l'image
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = '../uploads/'; // Chemin vers le dossier uploads depuis le dossier pages
            $image_name = basename($_FILES['image']['name']);
            $target_file = $target_dir . $image_name;

            // Enregistrement de l'image dans le dossier uploads
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image = 'uploads/' . $image_name; // Chemin relatif pour l'affichage
            } else {
                echo "Erreur lors du téléchargement de l'image.";
            }
        }

        // Insertion de la publication dans la base de données
        $stmt = $pdo->prepare("INSERT INTO publications (utilisateur_id, contenu, image) VALUES (:utilisateur_id, :contenu, :image)");
        $stmt->bindParam(':utilisateur_id', $utilisateur_id);
        $stmt->bindParam(':contenu', $contenu);
        $stmt->bindParam(':image', $image);

        if ($stmt->execute()) {
            header("Location: home.php");
            exit;
        } else {
            echo "Erreur lors de la publication.";
        }
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
