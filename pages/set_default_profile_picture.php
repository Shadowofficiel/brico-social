<?php
session_start();
$utilisateur_id = $_SESSION['utilisateur_id'];
$default_photo = "../images/default_avatar.png";

$host = 'localhost';
$dbname = 'bricoconnect';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Mettre à jour la photo de profil avec la photo par défaut et définir première connexion à faux
    $stmt = $pdo->prepare("UPDATE utilisateurs SET photo_profil = :photo_profil, premiere_connexion = 0 WHERE utilisateur_id = :utilisateur_id");
    $stmt->execute([':photo_profil' => $default_photo, ':utilisateur_id' => $utilisateur_id]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
