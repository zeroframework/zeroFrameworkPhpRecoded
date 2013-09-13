<?php
/**
 * Created by JetBrains PhpStorm.
 * User: adibox
 * Date: 13/09/13
 * Time: 18:20
 * To change this template use File | Settings | File Templates.
 */

class swiftmailerbundle {
    public static function register($app)
    {
        $serviceContainer = $app->getServiceContainer();

        $serviceContainer["mailer"] = $serviceContainer->share(function() use ($serviceContainer)
        {
            $defaultsOptions = array(
                "server" => "localhost",
                "port" => 25,
                "security" => null,
                "username" => "",
                "password" => "",
            );

            $configuration = $serviceContainer["mailer.options"];

            $options = array_merge($defaultsOptions, $configuration);

            $transporter = Swift_SmtpTransport::newInstance($options["server"], $options["port"], $options["security"])
                ->setUsername($options["username"])
                ->setPassword($options["password"])
            ;

            $mailer = Swift_Mailer::newInstance($transporter);

            return  $mailer;
        });
    }
}