<?php
/**
 * Created by JetBrains PhpStorm.
 * User: adibox
 * Date: 13/09/13
 * Time: 18:20
 * To change this template use File | Settings | File Templates.
 */

class swiftmailerbundle {
    public static function register($core)
    {
        $app = $core->getServiceContainer();

        $app['mailer.initialized'] = false;

        $app['mailer'] = $app->share(function ($app) {
            $app['mailer.initialized'] = true;

            return new \Swift_Mailer($app['swiftmailer.spooltransport']);
        });

        $app['swiftmailer.spooltransport'] = $app->share(function ($app) {
            return new \Swift_SpoolTransport($app['swiftmailer.spool']);
        });

        /**

        $app['swiftmailer.spool'] = $app->share(function ($app) {
            return new \Swift_MemorySpool();
        });
         *
         */

        $app['swiftmailer.spool'] = $app->share(function ($app) {
            return new \Swift_FileSpool(APP_DIRECTORY."/spool");
        });

        $app['swiftmailer.transport'] = $app->share(function ($app) {
            $transport = new \Swift_Transport_EsmtpTransport(
                $app['swiftmailer.transport.buffer'],
                array($app['swiftmailer.transport.authhandler']),
                $app['swiftmailer.transport.eventdispatcher']
            );

            $defaultsOptions = array(
                "server" => "localhost",
                "port" => 25,
                "security" => null,
                "username" => "",
                "password" => "",
                'auth_mode'  => null,
            );

            $configuration = $app["mailer.options"];

            $options = array_merge($defaultsOptions, $configuration);

            $transport->setHost($options['server']);
            $transport->setPort($options['port']);
            $transport->setEncryption($options['security']);
            $transport->setUsername($options['username']);
            $transport->setPassword($options['password']);
            $transport->setAuthMode($options['auth_mode']);

            return $transport;
        });

        $app['swiftmailer.transport.buffer'] = $app->share(function () {
            return new \Swift_Transport_StreamBuffer(new \Swift_StreamFilters_StringReplacementFilterFactory());
        });

        $app['swiftmailer.transport.authhandler'] = $app->share(function () {
            return new \Swift_Transport_Esmtp_AuthHandler(array(
                new \Swift_Transport_Esmtp_Auth_CramMd5Authenticator(),
                new \Swift_Transport_Esmtp_Auth_LoginAuthenticator(),
                new \Swift_Transport_Esmtp_Auth_PlainAuthenticator(),
            ));
        });

        $app['swiftmailer.transport.eventdispatcher'] = $app->share(function () {
            return new \Swift_Events_SimpleEventDispatcher();
        });
    }
}