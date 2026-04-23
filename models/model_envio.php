<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__.'/../_api/phpmailer/Exception.php';
require_once __DIR__.'/../_api/phpmailer/PHPMailer.php';
require_once __DIR__.'/../_api/phpmailer/SMTP.php';

class model_envio
{
    public function enviar($assunto, $mensagem, array $destinatarios, $emailRemetente)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.titan.email';
            $mail->SMTPAuth   = true;
            $mail->Username   = defined('SMTP_USER') && SMTP_USER ? SMTP_USER : 'contato@cvmnews.com.br';
            $mail->Password   = defined('SMTP_PASS') && SMTP_PASS ? SMTP_PASS : 'Rgdweb@26';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->CharSet    = 'UTF-8';
            $mail->Encoding   = PHPMailer::ENCODING_BASE64;

            $mail->setFrom($emailRemetente, 'Nome Remetente');
            foreach ($destinatarios as $email => $nome) {
                $mail->addAddress($email, $nome);
            }

            $mail->isHTML(true);
            $mail->Subject = $assunto;
            $mail->Body    = $mensagem;
            $mail->AltBody = strip_tags($mensagem);

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("PHPMailer erro: {$mail->ErrorInfo}");
            return false;
        }
    }
}
