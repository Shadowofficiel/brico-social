<?php
session_start();

if (!isset($_SESSION['utilisateur_id']) || !isset($_GET['ami_id'])) {
    exit; // Arrêter si l'utilisateur n'est pas connecté ou si aucun ami n'est défini
}

$utilisateur_id = $_SESSION['utilisateur_id'];
$ami_id = $_GET['ami_id'];

// Connexion à la base de données
$host = 'localhost';
$dbname = 'bricoconnect';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("
        SELECT * FROM messages 
        WHERE (expediteur_id = :utilisateur_id AND destinataire_id = :ami_id) 
           OR (expediteur_id = :ami_id AND destinataire_id = :utilisateur_id)
        ORDER BY date_envoi ASC
    ");
    $stmt->execute([':utilisateur_id' => $utilisateur_id, ':ami_id' => $ami_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($messages as $message) {
        $message_class = ($message['expediteur_id'] == $utilisateur_id) ? 'message-sent' : 'message-received';

        echo "<div class='message $message_class'>";

        // Affichage du contenu texte du message s'il existe
        if (!empty($message['contenu'])) {
            echo "<p>" . htmlspecialchars($message['contenu']) . "</p>";
        }

        // Affichage de fichier s'il existe
        if (!empty($message['fichier'])) {
            $filePath = '../' . htmlspecialchars($message['fichier']); // Chemin relatif à la racine du projet
            $absolutePath = __DIR__ . '/../' . $message['fichier'];

            if (file_exists($absolutePath)) {
                $fileType = mime_content_type($absolutePath);

                // Vérification si le fichier est une image
                if (strpos($fileType, 'image') !== false) {
                    echo "<div class='mb-3'><img src='$filePath' class='img-fluid rounded' style='max-width: 100%; margin-top: 5px;' alt='Image du message'></div>";
                } 
                // Vérification si le fichier est une vidéo
                elseif (strpos($fileType, 'video') !== false) {
                    echo "<div class='mb-3'><video controls style='width: 100%; margin-top: 5px;'>
                            <source src='$filePath' type='$fileType'>
                            Votre navigateur ne supporte pas la lecture de vidéos.
                          </video></div>";
                } 
                // Affichage d'un lien pour les autres types de fichiers
                else {
                    echo "<div class='mb-3'><a href='$filePath' target='_blank' class='d-block mt-2'>Télécharger le fichier</a></div>";
                }
            } else {
                echo "<p class='text-danger'>Le fichier n'a pas été trouvé.</p>";
            }
        }

        // Affichage de l'heure d'envoi
        echo "<small class='text-muted'>" . date('H:i', strtotime($message['date_envoi'])) . "</small>";

        // Statut de lecture pour les messages envoyés par l'utilisateur
        if ($message['expediteur_id'] == $utilisateur_id) {
            $statut = ($message['statut'] == 'vu') ? 'Vu' : 'Pas encore vu';
            echo "<small class='message-status'>$statut</small>";
        }

        echo "</div>";
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
