<?php

namespace SunlightExtend\Phpmailer;

use PHPMailer\PHPMailer\PHPMailer;
use Sunlight\Plugin\Action\PluginAction;
use Sunlight\Plugin\ExtendPlugin;
use Sunlight\Settings;

class PhpmailerPlugin extends ExtendPlugin
{
    public function onMailSand(array $args): void
    {
        $config = $this->getConfig();

        try {
            $mail = new PHPMailer(true);

            // server settings
            if ($config['use_smtp'] === true) {
                $mail->isSMTP();
                $mail->SMTPAuth = $config['smtp_auth'];
                $mail->SMTPDebug = $config['smtp_debug']; //SMTP::DEBUG_OFF; https://github.com/PHPMailer/PHPMailer/wiki/SMTP-Debugging
                $mail->SMTPAutoTLS = $config['smtp_auto_tls'];
                $mail->SMTPOptions = ['ssl' => ['verify_peer_name' => false]];
                $mail->Host = $config['smtp_host'];
                $mail->Username = $config['smtp_user'] ?? '';
                $mail->Password = $config['smtp_pass'] ?? '';
                $mail->SMTPSecure = $config['smtp_secure'];
                $mail->Port = $config['smtp_port'];
            }

            $mail->CharSet = PHPMailer::CHARSET_UTF8;
            $mail->Encoding = PHPMailer::ENCODING_BASE64;
            $mail->Priority = 1; // 1 = High, 3 = Normal, 5 = low
            $mail->WordWrap = 78;
            $mail->XMailer = $args['headers']['X-Mailer'] ?? null;

            // recipients
            if (Settings::get('mailerusefrom')) {
                $mail->setFrom($config['sender_email'], $config['sender_name']);
            }
            $mail->addAddress($args['to']);
            $mail->addReplyTo($config['sender_email']);

            // content
            $mail->isHTML(strpos(($args['headers']['Content-Type'] ?? ''), PHPMailer::CONTENT_TYPE_TEXT_HTML));
            $mail->Subject = $args['subject'];
            $mail->Body = $args['message'];

            // handled by a plugin
            $args['result'] = true;

            // send
            $mail->send();
        } catch (\Exception $e) {
            // handled by a plugin - email sending failed
            dump($e);
            $args['result'] = false;
        }

    }

    protected function getConfigDefaults(): array
    {
        return [
            'use_smtp' => true,
            'smtp_secure' => PHPMailer::ENCRYPTION_STARTTLS,
            'smtp_auth' => true,
            'smtp_host' => '',
            'smtp_port' => 587,
            'smtp_user' => '',
            'smtp_pass' => '',
            'sender_email' => Settings::get('sysmail'),
            'sender_name' => Settings::get('title'),
            'smtp_auto_tls' => true,
            // hidden configuration
            'smtp_debug' => 0,
        ];
    }

    public function getAction(string $name): ?PluginAction
    {
        if ($name === 'config') {
            return new ConfigAction($this);
        }
        return parent::getAction($name);
    }

}