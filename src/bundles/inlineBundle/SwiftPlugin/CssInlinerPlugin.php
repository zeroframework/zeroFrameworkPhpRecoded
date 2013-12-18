<?php

namespace SwiftPlugin;

use interfaces\containerAwaireInterface;

class CssInlinerPlugin implements \Swift_Events_SendListener, containerAwaireInterface
{
    private $container;

    public function __construct($container)
    {
        $this->setContainer($container);
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function get($name)
    {
        return $this->getContainer()->get($name);
    }

    public function getInline()
    {
        static $inline;

        if(null === $inline)
        {
            $inline = $this->getContainer()->get("inline");

            //$inline->setCss(file_get_contents($this->get("bootstrapcss")));

            $inline->setEncoding("UTF-8");

            $inline->setUseInlineStylesBlock(FALSE);

            $inline->setCssRules(require_once(APP_DIRECTORY.DIRECTORY_SEPARATOR."CssRulesCompiled".DIRECTORY_SEPARATOR."bootstrap.cssrules.php"));

        }

        return $inline;
    }


    /**
     * Invoked immediately before the Message is sent.
     *
     * @param Swift_Events_SendEvent $evt
     */
    public function beforeSendPerformed(\Swift_Events_SendEvent $evt)
    {
        $message = $evt->getMessage();

        $converter = $this->getInline();
        //$converter->setEncoding($message->getCharset());

        if ($message->getContentType() === 'text/html')
        {
            $converter->setHTML($message->getBody());

            $message->setBody($converter->convert());
        }

        foreach ($message->getChildren() as $part)
        {
            if (strpos($part->getContentType(), 'text/html') === 0)
            {
                $converter->setHTML($part->getBody());

                $part->setBody($converter->convert(false, false));
            }
        }
    }

    /**
     * Invoked immediately after the Message is sent.
     *
     * @param Swift_Events_SendEvent $evt
     */
    public function sendPerformed(\Swift_Events_SendEvent $evt)
    {
        // TODO: Implement sendPerformed() method.
    }

}