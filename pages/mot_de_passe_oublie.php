<?php
session_start();
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$host = 'localhost';
$dbname = 'bricoconnect';
$username = 'root';
$password = '';
$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT utilisateur_id, nom_utilisateur FROM utilisateurs WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $reset_token = bin2hex(random_bytes(50));
            $stmt = $pdo->prepare("UPDATE utilisateurs SET reset_token = :reset_token, token_expiration = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE utilisateur_id = :id");
            $stmt->execute([':reset_token' => $reset_token, ':id' => $user['utilisateur_id']]);

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.hostinger.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'richesse@richesse-monde.com';
            $mail->Password = '2023ARGENTmoney@';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('richesse@richesse-monde.com', 'SOCIAL-BRICO');
            $mail->addAddress($email, $user['nom_utilisateur']);
            $mail->isHTML(true);
            $mail->Subject = 'Reinitialisation de votre mot de passe';

            $reset_link = "http://localhost/webforum/pages/reinitialisation.php?token=" . $reset_token;
            $mail->Body = "
                <html><body style='font-family: Arial, sans-serif; color: #333;'>
                    <h2 style='color: #FF8C00;'>Réinitialisation de votre mot de passe</h2>
                    <p>Bonjour, {$user['nom_utilisateur']}.</p>
                    <p>Vous avez demandé une réinitialisation de votre mot de passe. Cliquez sur le lien suivant pour définir un nouveau mot de passe :</p>
                    <p><a href='{$reset_link}' style='color: #FF8C00; font-weight: bold;'>Réinitialiser mon mot de passe</a></p>
                    <p>Ce lien est valide pendant une heure.</p>
                </body></html>";

            $mail->send();
            $success_message = "Un email de réinitialisation a été envoyé. Cliquez sur le lien pour réinitialiser votre mot de passe.";
        } else {
            $error_message = "Email introuvable.";
        }
    } catch (Exception $e) {
        $error_message = "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - SOCIAL-BRICO</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #fdf3e3 0%, #f4c68e 100%);
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .reset-container {
            background: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .reset-container h2 {
            color: #FF8C00;
            font-weight: bold;
            margin-bottom: 25px;
        }
        .form-control {
            border-radius: 8px;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background-color: #FF8C00;
            border: none;
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #e07b00;
        }
        .alert {
            font-size: 0.9em;
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="reset-container">
    <h2><i class="fas fa-unlock-alt"></i> Mot de passe oublié</h2>
    <p>Entrez votre adresse email pour recevoir un lien de réinitialisation.</p>

    <?php if ($error_message): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> <?= $error_message ?></div>
    <?php elseif ($success_message): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success_message ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="form-group mt-4">
            <label for="email"><i class="fas fa-envelope"></i> Votre adresse email</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="email@example.com" required>
        </div>
        <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-paper-plane"></i> Envoyer le lien</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
