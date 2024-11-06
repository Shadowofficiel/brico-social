<?php
$host = 'localhost';
$dbname = 'bricoconnect';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'];
        $mot_de_passe = $_POST['mot_de_passe'];

        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe_hash'])) {
            session_start();
            $_SESSION['utilisateur_id'] = $utilisateur['utilisateur_id'];
            $_SESSION['nom_utilisateur'] = $utilisateur['nom_utilisateur'];
            header("Location: home.php");
            exit;
        } else {
            echo "Email ou mot de passe incorrect.";
        }
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
