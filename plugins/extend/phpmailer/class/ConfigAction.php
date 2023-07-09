<?php

namespace SunlightExtend\Phpmailer;

use Fosc\Feature\Plugin\Config\FieldGenerator;
use PHPMailer\PHPMailer\PHPMailer;
use Sunlight\Plugin\Action\ConfigAction as BaseConfigAction;
use Sunlight\Router;

class ConfigAction extends BaseConfigAction
{
    protected function getFields(): array
    {
        $langPrefix = '%p:phpmailer.config';

        $gen = new FieldGenerator($this->plugin);
        $gen->generateField('use_smtp', $langPrefix, '%checkbox')
            ->generateField('smtp_secure', $langPrefix, '%select', [
                'class' => 'inputmedium',
                'select_options' => [
                    'none' => 'none',
                    PHPMailer::ENCRYPTION_STARTTLS => 'TLS',
                    PHPMailer::ENCRYPTION_SMTPS => 'SSL',
                ],
            ], 'text')
            ->generateField('smtp_auth', $langPrefix, '%checkbox')
            ->generateField('smtp_port', $langPrefix, '%number', ['class' => 'inputmedium'])
            ->generateField('smtp_host', $langPrefix, '%text', ['class' => 'inputmedium'])
            ->generateField('smtp_user', $langPrefix, '%text', ['class' => 'inputmedium'])
            ->generateField('smtp_pass', $langPrefix, '%password', ['class' => 'inputmedium']);

        $emailSetupLink = ' <a href="' . _e(Router::admin('settings', ['fragment' => 'settings_emails'])) . '" target="_blank" class="button"><img src="' . Router::path('admin/public/images/icons/action.png') . '" alt="setting" class="icon">' . _lang('phpmailer.config.email_setting') . '</a>';
        $gen->generateField('sender_email', $langPrefix, '%text', ['class' => 'inputmedium', 'readonly'], 'text', ['after' => $emailSetupLink])
            ->generateField('sender_name', $langPrefix, '%text', ['class' => 'inputmedium'])
            ->generateField('smtp_auto_tls', $langPrefix, '%checkbox');

        return $gen->getFields();
    }
}
