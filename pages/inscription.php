<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Charger PHPMailer avec l'autoloader de Composer
require 'vendor/autoload.php';

$host = 'localhost';
$dbname = 'bricoconnect';
$username = 'root';
$password = '';
$error_message = '';
$success_message = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nom_utilisateur = htmlspecialchars(trim($_POST['nom_utilisateur']));
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $mot_de_passe = $_POST['mot_de_passe'];
        $confirme_mot_de_passe = $_POST['confirme_mot_de_passe'];

        if ($mot_de_passe !== $confirme_mot_de_passe) {
            $error_message = "Les mots de passe ne correspondent pas.";
        } else {
            $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_BCRYPT);

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $email_count = $stmt->fetchColumn();

            if ($email_count > 0) {
                $error_message = "Cet email est déjà utilisé.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom_utilisateur, email, mot_de_passe_hash) VALUES (:nom_utilisateur, :email, :mot_de_passe_hash)");
                $stmt->bindParam(':nom_utilisateur', $nom_utilisateur);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':mot_de_passe_hash', $mot_de_passe_hash);

                try {
                    $stmt->execute();
                    $success_message = "Inscription réussie ! Vous allez être redirigé vers la page de connexion.";

                    // Envoyer l'email de confirmation
                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = 'smtp.hostinger.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'richesse@richesse-monde.com';
                    $mail->Password = '2023ARGENTmoney@';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('richesse@richesse-monde.com', 'SOCIAL-BRICO');
                    $mail->addAddress($email, $nom_utilisateur);

                    $mail->isHTML(true);
                    $mail->Subject = 'Bienvenue sur SOCIAL-BRICO !';
                    $mail->Body = "
                        <html>
                        <body style='font-family: Arial, sans-serif; color: #333;'>
                            <h2 style='color: #FF8C00;'>Bienvenue, $nom_utilisateur !</h2>
                            <p>Merci de vous être inscrit sur BricoConnect. Nous sommes ravis de vous compter parmi nous.</p>
                            <p>Vous pouvez maintenant accéder à votre compte et rejoindre notre communauté de bricoleurs.</p>
                            <br>
                            <p style='font-size: 0.9rem;'>Cordialement,</p>
                            <p style='font-size: 0.9rem;'>L'équipe BricoConnect</p>
                        </body>
                        </html>
                    ";
                    $mail->AltBody = "Bienvenue, $nom_utilisateur !\n\nMerci de vous être inscrit sur BricoConnect. Nous sommes ravis de vous compter parmi nous.\n\nVous pouvez maintenant accéder à votre compte et rejoindre notre communauté de bricoleurs.\n\nCordialement,\nL'équipe BricoConnect";

                    $mail->send();
                    echo "<script>
                            setTimeout(() => {
                                window.location.href = 'connexion.php';
                            }, 3000);
                          </script>";
                } catch (Exception $e) {
                    $error_message = "Inscription réussie, mais l'email de confirmation n'a pas pu être envoyé.";
                }
            }
        }
    }
} catch (PDOException $e) {
    $error_message = "Erreur de connexion à la base de données. Veuillez réessayer plus tard.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <title>Inscription - BricoConnect</title>
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
        .signup-container {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .logo-container img {
            max-width: 160px;
            margin-bottom: 25px;
        }
        h2 {
            margin-bottom: 25px;
            color: #FF8C00;
            font-weight: bold;
        }
        .form-control {
            border-radius: 8px;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
        }
        .input-group-text {
            background-color: #fff;
            border-right: none;
            color: #FF8C00;
            font-size: 1.2rem;
        }
        .input-group .form-control {
            border-left: none;
        }
        .toggle-password {
            cursor: pointer;
            color: #FF8C00;
        }
        .btn-primary {
            background-color: #FF8C00;
            border: none;
            padding: 12px;
            font-size: 1rem;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #e07b00;
        }
        .options {
            margin-top: 10px;
            color: #555;
            font-size: 0.9rem;
        }
        .options a {
            color: #FF8C00;
            text-decoration: none;
            font-weight: bold;
        }
        .options a:hover {
            text-decoration: underline;
        }
        .alert {
            font-size: 0.9em;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: left;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        @media (max-width: 576px) {
            .signup-container {
                padding: 20px;
                max-width: 340px;
            }
            .btn-primary {
                padding: 10px;
            }
        }
    </style>
</head>
<body>

<div class="signup-container">
    <div class="logo-container">
        <img src="../images/brico.png" alt="Logo Brico">
    </div>

    <h2>Inscription</h2>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> <?php echo $error_message; ?>
        </div>
    <?php elseif (!empty($success_message)): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
            </div>
            <input type="text" id="nom_utilisateur" name="nom_utilisateur" class="form-control" placeholder="Nom d'utilisateur" required>
        </div>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
            </div>
            <input type="email" id="email" name="email" class="form-control" placeholder="Email" required>
        </div>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
            </div>
            <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control" placeholder="Mot de passe" required>
            <div class="input-group-append">
                <span class="input-group-text toggle-password" onclick="togglePassword('mot_de_passe')"><i class="fas fa-eye"></i></span>
            </div>
        </div>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
            </div>
            <input type="password" id="confirme_mot_de_passe" name="confirme_mot_de_passe" class="form-control" placeholder="Confirmer le mot de passe" required>
            <div class="input-group-append">
                <span class="input-group-text toggle-password" onclick="togglePassword('confirme_mot_de_passe')"><i class="fas fa-eye"></i></span>
            </div>
        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-user-plus"></i> S'inscrire</button>
    </form>

    <div class="options mt-3">
        <a href="mot_de_passe_oublie.php">Mot de passe oublié ?</a><br>
        <span>Déjà un compte ? <a href="connexion.php">Se connecter</a></span>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    function togglePassword(id) {
        const passwordInput = document.getElementById(id);
        const toggleIcon = passwordInput.nextElementSibling.querySelector('.fas');
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            toggleIcon.classList.remove("fa-eye");
            toggleIcon.classList.add("fa-eye-slash");
        } else {
            passwordInput.type = "password";
            toggleIcon.classList.remove("fa-eye-slash");
            toggleIcon.classList.add("fa-eye");
        }
    }
</script>
</body>
</html>
