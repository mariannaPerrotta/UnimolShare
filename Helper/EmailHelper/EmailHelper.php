<?php
/**
 * Created by PhpStorm.
 * User: Andrea
 * Date: 16/05/18
 * Time: 23:59
 */

class EmailHelper
{

    /**
     * Emailelper constructor.
     */
    public function __construct()
    {
    }

    function sendEmail($message){

        $emailTo = "andreacb94@gmail.com";
        $subject = "My Subject";
        $wordwrapLimit = 70; //Limite di 70 caratteri

        // use wordwrap() if lines are longer than 70 characters - va a capo dopo 70 caratteri
        $message = wordwrap($message, $wordwrapLimit);

        // send email
        mail($emailTo, $subject,$message);
    }

}