<?php

use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/mailconfig.php';

function enviarMail(string $destinatario, string $asunto, string $cuerpoHtml): bool
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // para los acentos
        $mail->CharSet  = 'UTF-8';
        $mail->Encoding = 'base64';

        $mail->setFrom(MAIL_USERNAME, 'Sistema de Vuelos');
        $mail->addAddress($destinatario);

        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $cuerpoHtml;
        $mail->AltBody = trim(strip_tags($cuerpoHtml));

        $mail->send();

        return true;
    } catch (\Throwable $e) {
        error_log("Error al enviar mail a $destinatario: " . $mail->ErrorInfo . ' | ' . $e->getMessage());
        return false;
    }
}
