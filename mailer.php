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
include './mail.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $name = $_POST['name'];
    $telephone = $_POST['telephone'];
    $contact = $_POST['contact'];

    // Verifica se la checkbox "sendMail" è selezionata
    $sendMail = isset($_POST['sendMail']) ? $_POST['sendMail'] : false;


     // Se la checkbox è selezionata, procedi con l'invio dell'email
     if ($sendMail) {
        // Crea un'istanza di PHPMailer
        $mail = new PHPMailer(true);

        try {
            
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;

            $mail->isSMTP();
            $mail->SMTPAuth   = true;

            $mail->Host = $_ENV['MAILTRAP_HOST'];
            $mail->Username = $_ENV['MAILTRAP_USERNAME'];
            $mail->Password = $_ENV['MAILTRAP_PASSWORD'];
            $mail->SMTPSecure = $_ENV['MAILTRAP_SMTP_SECURE'];
            $mail->Port = $_ENV['MAILTRAP_PORT'];

            // Destinatari
            $mail->setFrom($_ENV['MAIL_FROM']);
            $mail->addAddress($email);
        
            //Content
            $mail->isHTML(true);   //Set email format to HTML
            $mail->Subject = "Email mandata da $name";
            $mail->Body    =  <<<END

            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Document</title>
            </head>
            <body>
            <h1>Mail del cliente</h1>
                <p>Telefono: $telephone; </p>
                <p>Il cliente vorrebbe essere contattato attraverso: $contact;</p>
            </body>

            END;
        

            $mail->send();
            header('Location: grazie.php'); 
            exit();
        } catch (Exception $e) {
            echo "Impossibile inviare il messaggio. Errore Mailer: {$mail->ErrorInfo}";
        }
    } else {
        // La checkbox non è selezionata, mostra un messaggio di errore
        echo 'Errore: Devi selezionare la checkbox per inviare l\'email.';
    }    
}