<?php
/**
 * Created by PhpStorm.
 * User: Andrea
 * Date: 20/05/18
 * Time: 12:35
 */

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailHelper
{

    /**
     * Emailelper constructor.
     */
    public function __construct()
    {
    }

    function sendEmail($messaggio, $email, $password){

        $link = 'https://www.unimolshare.it/login.php';
        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
        try {
            //Server settings
            //$mail->SMTPDebug = 2;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'unimolshare@gmail.com';                 // SMTP username
            $mail->Password = 'projectUnimol300518';                           // SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587;                                    // TCP port to connect to

            //Recipients
            $mail->setFrom('unimolshare@gmail.com', 'UnimolShare - Automatic Password Recovery');
            $mail->addAddress('andreacb94@gmail.com', 'TEST Andrea');     // Add a recipient

            /*** LEVARE DAI COMMENTI UNA VOLTA FINITI I TEST ***
             * $mail->addAddress($email);               // Name is optional
             */

            //$mail->addReplyTo('unimolshare2@gmail.com', 'Information');
            /*$mail->addCC('cc@example.com');
            $mail->addBCC('bcc@example.com');*/

            //Attachments
            //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            //$mail->addAttachment('img/logo.png');    // Optional name

            $mail->Subject = 'UnimolShare - Recupero credenziali';

            //Content
            $mail->isHTML(true);                               // Set email format to HTML
            $mail->AddEmbeddedImage("/img/logo.jpg", "logo-img", "logo.jpg");
            $mail->Body    = '<!doctype html><html lang = "it"><header><meta charset="UTF-8"></header>';
            $mail->Body   .= '<body><h1>UnimolShare</h1><div>';
            $mail->Body   .= $messaggio.':<br/><br/><b>'.$password.'</div><br/><div>Vai su '.$link.' per entrare.</div></body></html>';
            $mail->AltBody = $messaggio.': '.$password.' --- Vai su '.$link.' per entrare.';
            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }

    }

}