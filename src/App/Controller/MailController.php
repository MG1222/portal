<?php

namespace App\Controller;

use Library\Core\AbstractController;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


class MailController extends AbstractController
{
    /**
     * function to send email for verification
     * @param array $user , string $product
     */

    public function resetPasswordLink(array $user, string $resetLink): void
    {
        //Load Composer’s autoloader
        require 'vendor/autoload.php';

        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host = 'localhost';
            $mail->Port = 1025;
            $mail->SMTPAuth = false;
            $mail->SMTPSecure = false;

            //Charset
            $mail->CharSet = "UTF-8";

            //Recipients
            $mail->setFrom(' ldeville@atakama-technologies.com', 'Atakama Technologies');
            $mail->addAddress($user['email'], $user['lastName']);     //Add a recipient
            $mail->addReplyTo('norepley@site.com', 'No reply');
            // $mail->addCC('cc@example.com');
            $mail->addBCC('atktestatk@gmail.com');
            //$mail->addBCC('rrenaudin@atakama-technologies.com');


            //Attachments
            //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            //$mail->addAttachment('asset/img/website/Logo Atakama Puce Avant.png', 'Logo Atakama');    //Optional name
            $mail->AddEmbeddedImage('asset/img/LogoAtakamaPuceAvant.png', 'Logo', 'Logo Atakama');
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = "Réinitialiser votre mot de passe";
            $mail->isHTML(true);

            $mailContent = "
								<h4> Madame, Monsieur {$user['lastName']}</h4>
    							<p>Vous avez récemment demandé à réinitialiser votre mot de passe. Veuillez cliquer sur le lien suivant pour réinitialiser votre mot de passe:<br><br><a href='{$resetLink}'>Changez mon mot de passe</a><br><br>Si vous n'avez pas demandé cette réinitialisation de mot de passe, veuillez ignorer ce message .</p>
    							
    							<p style='margin-top: 3rem;'>Bien cordialement</p>
    							<p style='margin-top: 0;'>Lea DEVILLE</p>
    							<p>Marketing - Atakama Technologies</p>
    							
    							<img src='cid:Logo' alt='Logo Atakama' width='233' height='48'>
    							<p>
    							<a href='https://www.atakama-technologies.com/?lang=fr'>www.atakama-technologies.com</a>
    							</p>
    							
    						";
            $mail->Body = $mailContent;

            $mail->AltBody = 'Réinitialiser votre mot de passe 2';


            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

    }

}
