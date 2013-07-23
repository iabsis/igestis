<?php

/**
 * @Author : Gilles Hemmerlé <giloux@gmail.com>
 */

class IgestisMailer
{
    /**
     * Initialize the Swift mailer and return a Swift_Message object
     * @static
     * @return Swift_Mime_Message
     */
    public static function init() {
        Swift::init(function () {
            Swift_DependencyContainer::getInstance()
                ->register('mime.qpcontentencoder')
                ->asAliasOf('mime.nativeqpcontentencoder');
        });

        return Swift_Message::newInstance();
    }

    /**
     * @static
     * @param Swift_Mime_Message $message The message object to send
     * @param Swift_SmtpTransport $transport Transport object to determine how to achemine the mail (see Swiftdoc : http://swiftmailer.org/docs/sending.html)
     * @return int Count sent emails
     */
    public static function send(Swift_Mime_Message $message, Swift_SmtpTransport $transport = null) {
        if($transport == null) {
            $transport = Swift_SmtpTransport::newInstance();
        }
        $mailer = Swift_Mailer::newInstance($transport);

        return $mailer->send($message);
    }

}
