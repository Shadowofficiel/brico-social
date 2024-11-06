<?php
session_start();

if (!isset($_SESSION['utilisateur_id']) || !isset($_POST['utilisateur_ami_id'])) {
    header("Location: home.php");
    exit;
}

$utilisateur_id = $_SESSION['utilisateur_id'];
$utilisateur_ami_id = $_POST['utilisateur_ami_id'];

// Connexion à la base de données
$host = 'localhost';
$dbname = 'bricoconnect';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ajoute une nouvelle demande d'amitié en attente
    $stmt = $pdo->prepare("INSERT INTO amities (utilisateur_id, utilisateur_ami_id, statut, date_demande) VALUES (:utilisateur_id, :utilisateur_ami_id, 'en_attente', NOW())");
    $stmt->execute([':utilisateur_id' => $utilisateur_id, ':utilisateur_ami_id' => $utilisateur_ami_id]);

    // Ajoute une notification pour l'utilisateur cible
    $stmt = $pdo->prepare("INSERT INTO notifications (utilisateur_id, type, contenu, vue, date_creation) VALUES (:utilisateur_id, 'invitation', :contenu, 0, NOW())");
    $contenu = "Vous avez reçu une demande d'ami de " . $_SESSION['nom_utilisateur'];
    $stmt->execute([':utilisateur_id' => $utilisateur_ami_id, ':contenu' => $contenu]);

    // Redirection vers le profil de l'utilisateur
    header("Location: profil_utilisateur.php?utilisateur_id=$utilisateur_ami_id");
    exit;

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
