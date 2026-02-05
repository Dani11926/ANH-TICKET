<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Assicurati che l'autoload di composer sia caricato qui o nel tuo index/bootstrap
// require 'path/to/vendor/autoload.php';

session_start();

class Email extends Controller
{
    public function verifyEmail()
    {
        $this->sendEmail();
    }

    public function sendEmail()
    {
        // 1. Recupera l'email dalla sessione
        $to = $_SESSION["email"] ?? null;

        if (!$to) {
            echo "Errore: Sessione scaduta o email mancante.";
            return;
        }

        // 2. Crea l'istanza di PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Configurazione Server (Esempio con Gmail)
            // Se usi un altro provider, cambia Host e Port
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'gambadaniele1@gmail.com';
            $mail->Password = 'tsqs pffo wmbj ovbw'; // Non la password normale!
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Destinatari
            $mail->setFrom('gambadaniele1@gmail.com', 'ANH-Ticket');
            $mail->addAddress($to); // L'email dell'utente dalla sessione

            // Contenuto
            $mail->isHTML(true);
            $mail->Subject = 'Email prova';
            $mail->Body = 'Ciao! Questa è una mail di prova inviata con <b>PHPMailer</b>.';
            $mail->AltBody = 'Ciao! Questa è una mail di prova in testo semplice.';

            // 3. Invia
            $mail->send();
            echo "Email inviata con successo a: " . $to;

        } catch (Exception $e) {
            echo "Errore, impossibile inviare l'email. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}