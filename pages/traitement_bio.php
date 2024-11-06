<?php
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: connexion.php");
    exit;
}

// Récupération de l'ID de l'utilisateur
$utilisateur_id = $_SESSION['utilisateur_id'];

// Connexion à la base de données
$host = 'localhost';
$dbname = 'bricoconnect';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérifiez si une biographie a été soumise
    if (isset($_POST['bio'])) {
        $bio = $_POST['bio'];

        // Mettre à jour la biographie dans la base de données
        $stmt = $pdo->prepare("UPDATE utilisateurs SET bio = :bio WHERE utilisateur_id = :utilisateur_id");
        $stmt->bindParam(':bio', $bio);
        $stmt->bindParam(':utilisateur_id', $utilisateur_id);

        if ($stmt->execute()) {
            // Redirection vers la page de profil avec un message de succès
            $_SESSION['message'] = "Votre biographie a été mise à jour avec succès.";
            header("Location: profil.php");
            exit;
        } else {
            $_SESSION['message'] = "Erreur lors de la mise à jour de votre biographie.";
            header("Location: profil.php");
            exit;
        }
    } else {
        $_SESSION['message'] = "Aucune biographie reçue.";
        header("Location: profil.php");
        exit;
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
