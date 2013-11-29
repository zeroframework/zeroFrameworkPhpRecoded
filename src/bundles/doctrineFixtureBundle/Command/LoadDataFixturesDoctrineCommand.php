<?php

namespace Command;

use Doctrine\Common\DataFixtures\Loader;
use Model\ContainerCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use InvalidArgumentException;

/**
 * Load data fixtures from bundles.
 */
class LoadDataFixturesDoctrineCommand extends ContainerCommand
{
    protected function configure()
    {
        $this
            ->setName('doctrine:fixtures:load')
            ->setDescription('Load data fixtures to your database.')
            ->addOption('append', null, InputOption::VALUE_NONE, 'Append the data fixtures instead of deleting all data from the database first.')
            ->addOption('purge-with-truncate', null, InputOption::VALUE_NONE, 'Purge data by using a database-level TRUNCATE statement')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var $doctrine \Doctrine\Common\Persistence\ManagerRegistry */
        $em = $this->getContainer()->get("db.orm.em");

        $container = $this->getContainer();

        if ($input->isInteractive() && !$input->getOption('append')) {
            $dialog = $this->getHelperSet()->get('dialog');
            if (!$dialog->askConfirmation($output, '<question>Careful, database will be purged. Do you want to continue Y/N ?</question>', false)) {
                return;
            }
        }

        $loader = new Loader();

        foreach($container->services as $servicename => $serviceparameters)
        {
            if(!empty($serviceparameters["tags"]))
            {
                foreach($serviceparameters["tags"] as $tag)
                {
                    if($tag["name"] == "doctrine.fixture")
                    {
                        $loader->addFixture($container->get($servicename));
                    }
                }
            }
        }

        $fixtures = $loader->getFixtures();

        if (!$fixtures) {
            throw new InvalidArgumentException(
                'Could not find any fixtures to load'
            );
        }
        $purger = new ORMPurger($em);
        $purger->setPurgeMode($input->getOption('purge-with-truncate') ? ORMPurger::PURGE_MODE_TRUNCATE : ORMPurger::PURGE_MODE_DELETE);
        $executor = new ORMExecutor($em, $purger);
        $executor->setLogger(function($message) use ($output) {
            $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', $message));
        });
        $executor->execute($fixtures, $input->getOption('append'));
    }
}