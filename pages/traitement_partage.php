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
        $utilisateur_id = $_SESSION['utilisateur_id'];

        // Vérification si l'utilisateur a déjà partagé la publication
        $checkShare = $pdo->prepare("SELECT * FROM partages WHERE utilisateur_id = :utilisateur_id AND publication_id = :publication_id");
        $checkShare->bindParam(':utilisateur_id', $utilisateur_id);
        $checkShare->bindParam(':publication_id', $publication_id);
        $checkShare->execute();

        if ($checkShare->rowCount() === 0) {  // Si pas encore partagé
            $stmt = $pdo->prepare("INSERT INTO partages (utilisateur_id, publication_id) VALUES (:utilisateur_id, :publication_id)");
            $stmt->bindParam(':utilisateur_id', $utilisateur_id);
            $stmt->bindParam(':publication_id', $publication_id);

            if ($stmt->execute()) {
                header("Location: home.php");
                exit;
            } else {
                echo "Erreur lors du partage.";
            }
        } else {
            echo "Vous avez déjà partagé cette publication.";
        }
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
