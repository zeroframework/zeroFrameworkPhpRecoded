<?php

namespace Command;

use mageekguy\atoum\scripts\runner;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CommandUnitTest extends \Model\ContainerCommand
{
    private $atoumArguments = array();

    public function configure()
    {
        $this
            ->setName("app:testunit")
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $runner = new runner("atoum");

        //$runner->addTestAllDirectory(APP_DIRECTORY."/tests/units");
        $runner->addTestsFromDirectory(APP_DIRECTORY."/tests/units");
        //$runner->getRunner()->setBootstrapFile(sprintf("%s/testunit.php", APP_DIRECTORY));
        if($input->getOption("verbose")) $runner->enableDebugMode();
        //$runner->getRunner()->addTest(APP_DIRECTORY."/tests/units/service/ApplicationRoomManager.php");

        $this->atoumArguments["--bootstrap-file"] = sprintf("%s/testunit.php", APP_DIRECTORY);

        $runner->run($this->getAtoumArguments());
    }

    protected function getAtoumArguments()
    {
        $inlinedArguments = array();

        foreach ($this->atoumArguments as $name => $values) {
            $inlinedArguments[] = $name;
            if (null !== $values) {
                $inlinedArguments[] = $values;
            }
        }

        return $inlinedArguments;
    }
}