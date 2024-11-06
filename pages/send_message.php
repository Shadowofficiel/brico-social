<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['utilisateur_id']) || !isset($_POST['ami_id'])) {
    echo json_encode(['error' => 'Utilisateur non connecté ou ami non défini']);
    exit;
}

$utilisateur_id = $_SESSION['utilisateur_id'];
$ami_id = $_POST['ami_id'];
$message = isset($_POST['message']) ? trim($_POST['message']) : '';
$uploadDir = '../uploads/messages/';
$uploadedFilePath = null;

// Connexion à la base de données
$host = 'localhost';
$dbname = 'bricoconnect';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Gestion du fichier uploadé
    if (!empty($_FILES['file']['name'])) {
        $fileName = basename($_FILES['file']['name']);
        $filePath = $uploadDir . time() . '_' . $fileName;

        // Crée le répertoire si nécessaire
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Déplacement du fichier
        if (move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
            $uploadedFilePath = 'uploads/messages/' . time() . '_' . $fileName; // Chemin à enregistrer en base
        } else {
            echo json_encode(['error' => 'Échec du téléchargement du fichier']);
            exit;
        }
    }

    // Vérifiez si un message ou un fichier est présent
    if (!empty($message) || !empty($uploadedFilePath)) {
        $stmt = $pdo->prepare("INSERT INTO messages (expediteur_id, destinataire_id, contenu, fichier, date_envoi) VALUES (:expediteur_id, :destinataire_id, :contenu, :fichier, NOW())");
        $stmt->execute([
            ':expediteur_id' => $utilisateur_id,
            ':destinataire_id' => $ami_id,
            ':contenu' => $message,
            ':fichier' => $uploadedFilePath
        ]);

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Ni message ni fichier fourni']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
