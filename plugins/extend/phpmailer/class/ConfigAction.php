<?php

namespace SunlightExtend\Phpmailer;

use Fosc\Plugin\Action\ConfigAction as BaseConfigAction;
use PHPMailer\PHPMailer\PHPMailer;
use Sunlight\Router;

class ConfigAction extends BaseConfigAction
{
    protected function getFields(): array
    {
        $langPrefix = '%p:phpmailer.cfg';

        $fields = [];
        $fields += $this->generateField('use_smtp', $langPrefix, '%checkbox');
        $fields += $this->generateField('smtp_secure', $langPrefix, '%select', [
            'class' => 'inputmedium',
            'select_options' => [
                'none' => 'none',
                PHPMailer::ENCRYPTION_STARTTLS => 'TLS',
                PHPMailer::ENCRYPTION_SMTPS => 'SSL',
            ],
        ], 'text');
        $fields += $this->generateField('smtp_auth', $langPrefix, '%checkbox');
        $fields += $this->generateField('smtp_port', $langPrefix, '%number', ['class' => 'inputmedium']);
        $fields += $this->generateField('smtp_host', $langPrefix, '%text', ['class' => 'inputmedium']);
        $fields += $this->generateField('smtp_user', $langPrefix, '%text', ['class' => 'inputmedium']);
        $fields += $this->generateField('smtp_pass', $langPrefix, '%password', ['class' => 'inputmedium']);

        $emailSetupLink = ' <a href="' . _e(Router::admin('settings', ['fragment' => 'settings_emails'])) . '" target="_blank" class="button"><img src="' . Router::path('admin/images/icons/action.png') . '" alt="setting" class="icon">' . _lang('phpmailer.cfg.email_setting') . '</a>';
        $fields += $this->generateField('sender_email', $langPrefix, '%text', ['class' => 'inputmedium', 'readonly'], 'text', ['after' => $emailSetupLink]);
        $fields += $this->generateField('sender_name', $langPrefix, '%text', ['class' => 'inputmedium']);
        $fields += $this->generateField('smtp_auto_tls', $langPrefix, '%checkbox');

        return $fields;
    }
}
