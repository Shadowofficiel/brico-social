<?php
session_start();

$host = 'localhost';
$dbname = 'bricoconnect';
$username = 'root';
$password = '';
$error_message = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Récupérer et valider les données envoyées par le formulaire
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $mot_de_passe = $_POST['mot_de_passe'];

        // Préparer et exécuter la requête sécurisée
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérification du mot de passe
        if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe_hash'])) {
            $_SESSION['utilisateur_id'] = $utilisateur['utilisateur_id'];
            $_SESSION['nom_utilisateur'] = $utilisateur['nom_utilisateur'];
            header("Location: home.php");
            exit;
        } else {
            $error_message = "Email ou mot de passe incorrect.";
        }
    }
} catch (PDOException $e) {
    $error_message = "Erreur : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <title>Connexion - BricoConnect</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Styles pour la page */
        body {
            background: linear-gradient(135deg, #fdf3e3 0%, #f4c68e 100%);
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
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
            .login-container {
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

<div class="login-container">
    <div class="logo-container">
        <img src="../images/brico.png" alt="Logo BricoConnect">
    </div>

    <h2>Connexion</h2>

    <!-- Affichage du message d'erreur ou de succès (si existant) -->
    <?php if (!empty($error_message)): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i> <?php echo $error_message; ?>
    </div>
    <?php endif; ?>

    <form action="" method="POST">
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
        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Se connecter</button>
    </form>

    <div class="options mt-3">
        <a href="mot_de_passe_oublie.php">Mot de passe oublié ?</a>
        <span> | </span>
        <a href="inscription.php">Créer un compte</a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
