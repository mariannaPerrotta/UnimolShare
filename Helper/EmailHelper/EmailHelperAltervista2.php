<?php
/**
 * Created by PhpStorm.
 * User: Andrea
 * Date: 30/05/18
 * Time: 21:57
 */

class EmailHelperAltervista
{
    /**
     * EmailHelperAltervista constructor.
     */
    public function __construct()
    {
    }

    //Funzione per inviare un'email con la nuova password
    function sendResetPasswordEmail($email, $password){

        $messaggio = "Usa questa password temporanea";

        $linkLogin = 'https://www.unimolshare.it/login.php';
        $emailTo = $email;
        $subject = "UnimolShare - Conferma registrazione";
        $message   = '<html><body><h1>UnimolShare</h1><div>';
        $message   .= $messaggio.':<br/><br/><b>'.$password.'</div><br/><div>Vai su '.$linkLogin.' per entrare.</div></body></html>';
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        try {
            mail($emailTo, $subject, $message, $headers);
            return true;
        } catch (Exception $e){
            return false;
        }

    }

    //Funzione per inviare un'email di conferma dell'account
    function sendConfermaAccount($email, $link){

        // using SendGrid's PHP Library
//      https://github.com/sendgrid/sendgrid-php
        require '../../vendor/autoload.php';
        $sendgrid = new SendGrid("SENDGRID_APIKEY");
        $emailTo    = new SendGrid\Email();

        $sendgrid->send($emailTo);

        $messaggio = 'Hai appena richiesto di iscriverti ad UnimolShare!<br>Conferma la tua iscrizione col seguente link:';
        $linkLogin = 'https://www.unimolshare.it/login.php';
        $subject = "UnimolShare - Conferma registrazione";
        $message   = '<html><body><h1>UnimolShare</h1><div>';
        $message   .= $messaggio.'<br/><br/>'.$link.'</div><br/><div>Vai su '.$linkLogin.' per entrare.</div></body></html>';
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";



        $emailTo->addTo($email)
            ->setSubject($subject)
            ->setHtml($message);

        try {
            return mail($emailTo, $subject, $message, $headers);
        } catch (Exception $e){
            return false;
        }

    }

    //Funzione per inviare email di segnalazione
    function sendSegnalazione($nome, $cognome, $motivo, $contatto, $email){

        $emailTo = "unimolshare@gmail.com";
        $subject = "UnimolShare - Seg";
        $message   = '<html><body><h1>UnimolShare - Segnalazione Profilo</h1><div>';
        $message   .= $nome.', '.$cognome.'</div><br/><div>Motivo segnalazione: '.$motivo.'<br/>Contatti studente segnalato: '.$contatto.' '.$email.'.<br/><br/></div></body></html>';
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        try {
            mail($emailTo, $subject, $message, $headers);
            return true;
        } catch (Exception $e){
            return false;
        }

    }

}