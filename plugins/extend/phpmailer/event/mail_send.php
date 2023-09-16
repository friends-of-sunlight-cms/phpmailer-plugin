<?php

use PHPMailer\PHPMailer\PHPMailer;
use Sunlight\Settings;

return function (array $args) {
    $config = $this->getConfig();

    // server settings
    if ($config['use_smtp'] !== true || empty($config['smtp_host'])) {
        return; // return to native mail() function
    }

    try {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = $config['smtp_host'];
        $mail->SMTPAuth = $config['smtp_auth'];
        $mail->Username = $config['smtp_user'] ?? '';
        $mail->Password = $config['smtp_pass'] ?? '';
        $mail->SMTPSecure = $config['smtp_secure'];
        $mail->Port = $config['smtp_port'];
        $mail->SMTPDebug = $config['smtp_debug']; //SMTP::DEBUG_OFF; https://github.com/PHPMailer/PHPMailer/wiki/SMTP-Debugging
        $mail->SMTPAutoTLS = $config['smtp_auto_tls'];
        $mail->SMTPOptions = ['ssl' => ['verify_peer_name' => false]];

        $mail->CharSet = PHPMailer::CHARSET_UTF8;
        $mail->Encoding = PHPMailer::ENCODING_BASE64;
        $mail->Priority = 1; // 1 = High, 3 = Normal, 5 = low
        $mail->XMailer = $args['headers']['X-Mailer'] ?? null;

        $senderEmail = $config['sender_email'] ?? Settings::get('sysmail');
        $senderName = $config['sender_name'] ?? Settings::get('title');

        $mail->setFrom($senderEmail, $senderName);
        $mail->addAddress($args['to']);
        $mail->addReplyTo($senderEmail);

        // content
        $mail->isHTML(strpos(($args['headers']['Content-Type'] ?? ''), PHPMailer::CONTENT_TYPE_TEXT_HTML));
        $mail->Subject = $args['subject'];
        $mail->Body = $args['message'];
        $mail->AltBody = strip_tags($args['message']);

        // handled by a plugin
        $args['result'] = true;

        // send
        $mail->send();
    } catch (\Exception $e) {
        //throw $e;
        // handled by a plugin - email sending failed
        return; // return to native mail() function
    }
};
