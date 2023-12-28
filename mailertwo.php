<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require 'vendor/autoload.php';

// Carica variabili d'ambiente da .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Avvia la sessione
session_start();

// Include il file con il form di input
include './index.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $name = $_POST['name'];
    $telephone = $_POST['telephone'];
    $contact = $_POST['contact'];

    // Verifica se la checkbox "sendMail" è selezionata
    $sendMail = isset($_POST['sendMail']) ? $_POST['sendMail'] : false;

    // Se la checkbox è selezionata, procedi con l'invio dell'email
    if ($sendMail) {
        // Invia email al cliente
        sendEmail($email, "Oggetto della seconda email", "Contenuto della seconda email");

        // Invia email a MAIL_FROM
        sendEmail($_ENV['MAIL_FROM'], "Email mandata da $name", getMailBody($email, $telephone, $contact));

        header('Location: grazie.php');
        exit();
    } else {
        // La checkbox non è selezionata, mostra un messaggio di errore
        echo 'Errore: Devi selezionare la checkbox per inviare l\'email.';
    }
}

function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->Host = $_ENV['MAIL_HOST'];
        $mail->Username = $_ENV['MAIL_USERNAME'];
        $mail->Password = $_ENV['MAIL_PASSWORD'];
        $mail->SMTPSecure = $_ENV['MAIL_SMTP_SECURE'];
        $mail->Port = $_ENV['MAIL_PORT'];

        $mail->setFrom($_ENV['MAIL_FROM']);
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
    } catch (Exception $e) {
        echo "Impossibile inviare il messaggio. Errore Mailer: {$mail->ErrorInfo}";
    }
}

function getMailBody($email, $telephone, $contact) {
    return <<<END
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Document</title>
        </head>
        <body>
            <h1>Mail del cliente</h1>
            <p>Email: $email; </p>
            <p>Telefono: $telephone; </p>
            <p>Il cliente vorrebbe essere contattato attraverso: $contact;</p>
        </body>
        </html>
        END;
}
?>