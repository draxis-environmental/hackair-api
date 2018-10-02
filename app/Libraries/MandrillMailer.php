<?php

namespace App\Libraries;

use Mandrill;
use Config;
use Log;

class MandrillMailer
{
    protected static function getTemplateContent()
    {
        return [
            [
                'name' => 'example name',
                'content' => 'example content'
            ]
        ];
    }

    protected static function getGlobalMergeVars()
    {
        return array(
            array(
                'name' => 'email_content',
                'content' => []
            )
        );
    }

    protected static function getMergeVars()
    {
        return [];
    }

    protected static function getMessage()
    {
        $message = array(
            'subject' => '',
            'from_email' => Config::get('mail.from.address'),
            'from_name' => Config::get('mail.from.name'),
            'to' => [],
            'merge' => true,
            'global_merge_vars' => self::getGlobalMergeVars(),
            'merge_vars' => static::getMergeVars(),
            /*'headers' => array('Reply-To' => 'message.reply@example.com'),
            'important' => false,
            'track_opens' => null,
            'track_clicks' => null,
            'auto_text' => null,
            'auto_html' => null,
            'inline_css' => null,
            'url_strip_qs' => null,
            'preserve_recipients' => null,
            'view_content_link' => null,
            'bcc_address' => 'message.bcc_address@example.com',
            'tracking_domain' => null,
            'signing_domain' => null,
            'return_path_domain' => null,
            'merge' => true,
            'tags' => array('password-resets'),
            'subaccount' => 'customer-123',
            'google_analytics_domains' => array('example.com'),
            'google_analytics_campaign' => 'message.from_email@example.com',
            'metadata' => array('website' => 'www.example.com'),
            'recipient_metadata' => array(
                array(
                    'rcpt' => 'recipient.email@example.com',
                    'values' => array('user_id' => 123456)
                )
            ),
            'attachments' => array(
                array(
                    'type' => 'text/plain',
                    'name' => 'myfile.txt',
                    'content' => 'ZXhhbXBsZSBmaWxl'
                )
            ),
            'images' => array(
                array(
                    'type' => 'image/png',
                    'name' => 'IMAGECID',
                    'content' => 'ZXhhbXBsZSBmaWxl'
                )
            )*/
        );

        return $message;
    }

    public static function send($templateName, $templateContent, $message)
    {
        if(getenv('APP_ENV') == 'staging') {
            return true;
        }

        $mandrill = new Mandrill(Config::get('services.mandrill.secret'));

        $templateContent = $templateContent ?: self::getTemplateContent();
        $message = array_merge(self::getMessage(), $message);

        try {
            if (empty($templateName) == false) {
                $mandrill->messages->sendTemplate($templateName, $templateContent, $message);
            } else {
                $mandrill->messages->send($message);
            }
            Log::info('Email sent successfully through Mandrill using template: ' . $templateName);
        } catch (Mandrill_Error $e) {
            // catch Mandrill error
            Log::info('A mandrill error occurred:' . get_class($e) . ' - ' . $e->getMessage());
            // Throw exception
            throw $e;
        }
    }
}