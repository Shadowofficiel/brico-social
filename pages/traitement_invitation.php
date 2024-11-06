<?php
session_start();

if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: connexion.php");
    exit;
}

// Connexion à la base de données
$host = 'localhost';
$dbname = 'bricoconnect';
$username = 'root';
$password = '';
$utilisateur_id = $_SESSION['utilisateur_id'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['amitie_id']) && isset($_POST['action'])) {
        $amitie_id = $_POST['amitie_id'];
        $action = $_POST['action'];

        // Mise à jour de l'invitation en fonction de l'action
        if ($action === 'accepter') {
            // Accepter l'invitation et mettre à jour le statut comme boolean accepté
            $stmt = $pdo->prepare("UPDATE amities SET statut = 1 WHERE amitie_id = :amitie_id AND utilisateur_ami_id = :utilisateur_id");
            $stmt->execute([':amitie_id' => $amitie_id, ':utilisateur_id' => $utilisateur_id]);

            // Ajout de l'amitié réciproque
            $stmt = $pdo->prepare("INSERT INTO amities (utilisateur_id, utilisateur_ami_id, statut) VALUES (:utilisateur_id, :utilisateur_ami_id, 1)");
            $stmt->execute([':utilisateur_id' => $utilisateur_id, ':utilisateur_ami_id' => $amitie_id]);
            
        } elseif ($action === 'refuser') {
            // Supprimer l'invitation si refusée
            $stmt = $pdo->prepare("DELETE FROM amities WHERE amitie_id = :amitie_id AND utilisateur_ami_id = :utilisateur_id");
            $stmt->execute([':amitie_id' => $amitie_id, ':utilisateur_id' => $utilisateur_id]);
        }
    }

    // Redirection vers la page invitations après le traitement
    header("Location: invitations.php");
    exit;

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
