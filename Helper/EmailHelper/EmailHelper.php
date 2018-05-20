<?php
/**
 * Created by PhpStorm.
 * User: Andrea
 * Date: 16/05/18
 * Time: 23:59
 */

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
//require_once '../../vendor/autoload.php';

class EmailHelper
{

    /**
     * Emailelper constructor.
     */
    public function __construct()
    {
    }

    function sendEmail($messaggio){

        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
        try {
            //Server settings
            $mail->SMTPDebug = 2;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'unimolshare@gmail.com';                 // SMTP username
            $mail->Password = 'projectUnimol300518';                           // SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587;                                    // TCP port to connect to

            //Recipients
            $mail->setFrom('unimolshare@gmail.com', 'Mailer');
            $mail->addAddress('andreacb94@gmail.com', 'Andrea User');     // Add a recipient
            $mail->addAddress('andrea_cb_94@hotmail.it');               // Name is optional
            $mail->addReplyTo('unimolshare2@gmail.com', 'Information');
            /*$mail->addCC('cc@example.com');
            $mail->addBCC('bcc@example.com');*/

            //Attachments
            /*$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name*/

            //Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Here is the subject';
            $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }

    }

}