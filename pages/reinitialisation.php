<?php
session_start();

$host = 'localhost';
$dbname = 'bricoconnect';
$username = 'root';
$password = '';
$error_message = '';
$success_message = '';

$token = $_GET['token'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && $token) {
    $mot_de_passe = $_POST['mot_de_passe'];
    $confirme_mot_de_passe = $_POST['confirme_mot_de_passe'];

    if ($mot_de_passe !== $confirme_mot_de_passe) {
        $error_message = "Les mots de passe ne correspondent pas.";
    } else {
        $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_BCRYPT);

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare("SELECT utilisateur_id FROM utilisateurs WHERE reset_token = :token AND token_expiration > NOW()");
            $stmt->execute([':token' => $token]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $stmt = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe_hash = :mot_de_passe_hash, reset_token = NULL, token_expiration = NULL WHERE utilisateur_id = :id");
                $stmt->execute([':mot_de_passe_hash' => $mot_de_passe_hash, ':id' => $user['utilisateur_id']]);

                $success_message = "Votre mot de passe a été réinitialisé avec succès. Vous allez être redirigé vers la page de connexion.";
                header("refresh:3;url=connexion.php");
            } else {
                $error_message = "Le lien est invalide ou a expiré.";
            }
        } catch (PDOException $e) {
            $error_message = "Erreur de base de données.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe - SOCIAL-BRICO</title>
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
        .toggle-password {
            cursor: pointer;
            color: #FF8C00;
        }
    </style>
</head>
<body>

<div class="reset-container">
    <h2><i class="fas fa-unlock-alt"></i> Réinitialisation du mot de passe</h2>
    <?php if ($error_message): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> <?= $error_message ?></div>
    <?php elseif ($success_message): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success_message ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="form-group mt-4">
            <label for="mot_de_passe"><i class="fas fa-lock"></i> Nouveau mot de passe</label>
            <input type="password" name="mot_de_passe" id="mot_de_passe" class="form-control" required>
        </div>
        <div class="form-group mt-3">
            <label for="confirme_mot_de_passe"><i class="fas fa-lock"></i> Confirmez le mot de passe</label>
            <input type="password" name="confirme_mot_de_passe" id="confirme_mot_de_passe" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary mt-4"><i class="fas fa-redo"></i> Réinitialiser</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
