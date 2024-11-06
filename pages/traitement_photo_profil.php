<?php
session_start();
if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: connexion.php");
    exit;
}

$utilisateur_id = $_SESSION['utilisateur_id'];
$target_dir = "../uploads/";
$uploadOk = 1;

// Vérification si le fichier a bien été téléchargé
if (isset($_FILES["photo_profil"]) && $_FILES["photo_profil"]["error"] == 0) {
    $target_file = $target_dir . basename($_FILES["photo_profil"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Vérification de la taille du fichier (par exemple, max 2Mo)
    if ($_FILES["photo_profil"]["size"] > 2000000) {
        echo "Désolé, votre fichier est trop volumineux. Taille max: 2Mo.";
        $uploadOk = 0;
    }

    // Vérification du format du fichier
    $valid_formats = array("jpg", "png", "jpeg", "gif");
    if (!in_array($imageFileType, $valid_formats)) {
        echo "Désolé, seuls les formats JPG, JPEG, PNG & GIF sont autorisés.";
        $uploadOk = 0;
    }

    // Téléchargement du fichier si toutes les vérifications sont passées
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["photo_profil"]["tmp_name"], $target_file)) {
            // Connexion à la base de données
            $host = 'localhost';
            $dbname = 'bricoconnect';
            $username = 'root';
            $password = '';

            try {
                $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Mise à jour de l'image de profil dans la base de données
                $stmt = $pdo->prepare("UPDATE utilisateurs SET photo_profil = :photo_profil WHERE utilisateur_id = :utilisateur_id");
                $stmt->bindParam(':photo_profil', $target_file);
                $stmt->bindParam(':utilisateur_id', $utilisateur_id);

                if ($stmt->execute()) {
                    echo "La photo de profil a été mise à jour avec succès.";
                    // Redirection vers la page d'accueil
                    header("Location: home.php");
                    exit;
                } else {
                    echo "Erreur lors de la mise à jour de la photo de profil.";
                }
            } catch (PDOException $e) {
                echo "Erreur de connexion à la base de données : " . $e->getMessage();
            }
        } else {
            echo "Désolé, une erreur est survenue lors du téléchargement de votre fichier.";
        }
    }
} else {
    echo "Aucun fichier téléchargé ou une erreur est survenue.";
}
?>
