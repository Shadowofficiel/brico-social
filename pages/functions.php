<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

function envoyerEmail($destinataire, $sujet, $contenuHtml) {
    $mail = new PHPMailer(true);
    try {
        // Configuration SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.hostinger.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'richesse@richesse-monde.com';
        $mail->Password = '2023ARGENTmoney@';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Informations sur l’expéditeur
        $mail->setFrom('richesse@richesse-monde.com', 'SOCIAL-BRICO');
        $mail->addAddress($destinataire);

        // Contenu de l'email
        $mail->isHTML(true);
        $mail->Subject = $sujet;
        $mail->Body = $contenuHtml;

        // Envoi de l'email
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
