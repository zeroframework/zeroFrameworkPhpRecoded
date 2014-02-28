<?php

namespace Command;

use Model\ContainerCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\Generator\Dumper\PhpGeneratorDumper;
use Symfony\Component\Routing\Matcher\Dumper\PhpMatcherDumper;
use TwigJs\Compiler\Expression\FilterCompiler;
use TwigJs\CompileRequest;
use TwigJs\CompileRequestHandler;
use TwigJs\FilterCompilerInterface;
use TwigJs\JsCompiler;
use TwigJs\Twig\TwigJsExtension;

class testFilterCompiler implements FilterCompilerInterface
{
    function getName()
    {
        // TODO: Implement getName() method.
    }

    function compile(JsCompiler $compiler, \Twig_Node_Expression_Filter $filter)
    {
        echo "test".PHP_EOL;

        // TODO: Implement compile() method.
    }

}

class CommandCacheClear extends ContainerCommand
{
    public function configure()
    {
        $this
            ->setName("app:cache:clear")
            ->addOption("--compile-twig", null, InputOption::VALUE_NONE)
            ->addOption("--compile-twig-js", null, InputOption::VALUE_NONE)
            ->addOption("--compile-routing", null, InputOption::VALUE_NONE)
            ->addOption("--compile-all", null, InputOption::VALUE_NONE)
            ->addOption("--clear-apc", null, InputOption::VALUE_REQUIRED)
            ->addOption("--compile-assetic", null, InputOption::VALUE_NONE)
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {

        $fileSystem = new Filesystem();

        $cacheDirectory = APP_DIRECTORY.DIRECTORY_SEPARATOR."cache";

        if($input->getOption("compile-all") || $input->getOption("compile-routing"))
        {
            $fileSystem->remove(array(
                $cacheDirectory.DIRECTORY_SEPARATOR."ProjectUrlGenerator.php",
                $cacheDirectory.DIRECTORY_SEPARATOR."ProjectUrlMatcher.php"
            ));

            /** @var \Symfony\Component\Routing\Router $router */
            $router = $this->getContainer()->get("routersymfony");

            $router->getGenerator();

            $output->writeln("routing generated [OK]");
        }

        if($input->getOption("clear-apc"))
        {
            file_get_contents("http://".$input->getOption("clear-apc")."/clear.php");

            $output->writeln("apc generated [OK]");
        }

        /** @var \Twig_Environement $twig */
        $twig = $this->getContainer()->get("twig");


        if($input->getOption("compile-all") || $input->getOption("compile-twig"))
        {
            $twig->enableAutoReload();

            $templateDirectory = APP_DIRECTORY.DIRECTORY_SEPARATOR."html";

            $templateIterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($templateDirectory, \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS));

            foreach($templateIterator as $template)
            {
                if($template->isFile())
                {
                    //$output->writeln($template->getPathName());
                    $twig->loadTemplate(str_replace($templateDirectory.DIRECTORY_SEPARATOR, "", $template->getPathName()));
                }
            }

            $output->writeln("twig generated [OK]");
        }

        if($input->getOption("compile-all") || $input->getOption("compile-twig-js"))
        {
            // Generate template javascript

            $env = $twig;

            $compiler = new JsCompiler($env);

            $compiler->setFilterFunction("relativetime", "convertToRelativeTime");

            $env->setCompiler($compiler);

            $folder_html = APP_DIRECTORY."/html";
            $folder_js_twig = APP_DIRECTORY."/../js/twig";

            $templateJsForCompiler = array(
                "inbox/part_discutions_item" => "part_discutions_item",
                "inbox/part_message" => "part_message",
                "search/item_view" => "template_search_item_view",
                "popover/room" => "popover_room",
                "inbox/part_notification" => "template_notification_view",
                "modals/sendifprofileuncomplete" => "template_sendifprofileuncomplete",
                "documents_item" => "documents_item",
                "user/signin" => "template_user_signin",
                "modals/login_subscribe" => "login_subscribe",
                "home/home" => "template_home",
                "search/mappanel" => "template_search_mappanel",
                "search/resultpanel" => "template_search_resultpanel",
                "search/searchpanel" => "template_search_searchpanel",
                "search/page" => "template_search_page",
                "pages/messages" => "template_pages_messages",
                "pages/visites" => "template_pages_visites",
                "visite/calendar" => "template_visite_calendar",
                "header" => "template_header",
                "user/hosting" => "template_user_hosting",
                "rooms/view_add_liststatic" => "template_room_edit",
                "inbox/part_discution" => "template_inbox_discution",
                "user/forgotpassword" => "template_user_forgotpassword",
                "wishlist/list" => "template_wishlist_list",
                "modals/formcompleteprofile" => "template_modal_formcompleteprofile"
            );

            foreach($templateJsForCompiler as $file => $templatename)
            {
                $file = $folder_html."/".$file.".html.twig";

                file_put_contents($folder_js_twig.DIRECTORY_SEPARATOR.$templatename.".js", $env->compileSource(file_get_contents($file), $file));

                $output->writeln("js twig => $file compiler");
            }

            $output->writeln("twig js generated [OK]");
        }

        if($input->getOption("compile-all") || $input->getOption("compile-assetic"))
        {
            $helper = $this->getContainer()->get('assetic.dumper');
            $helper->addTwigAssets();
            $helper->dumpAssets();
        }


        /**
       $elements = new \DirectoryIterator($cacheDirectory);

       foreach($elements as $element)
       {
           if(!$element->isDot()) $fileSystem->remove($element->getPathname());
       }
         *
         */

       $fileSystem->dumpFile($cacheDirectory.DIRECTORY_SEPARATOR."autoloading.json", json_encode(array()));


        $output->writeln("cache clear [OK]");
    }
}