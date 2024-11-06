<?php
$host = 'localhost';
$dbname = 'bricoconnect';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nom_utilisateur = $_POST['nom_utilisateur'];
        $email = $_POST['email'];
        $mot_de_passe_hash = password_hash($_POST['mot_de_passe'], PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom_utilisateur, email, mot_de_passe_hash) VALUES (:nom_utilisateur, :email, :mot_de_passe_hash)");
        $stmt->bindParam(':nom_utilisateur', $nom_utilisateur);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':mot_de_passe_hash', $mot_de_passe_hash);

        if ($stmt->execute()) {
            header("Location: connexion.php");
            exit;
        } else {
            echo "Erreur lors de l'inscription.";
        }
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
