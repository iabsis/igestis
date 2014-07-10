<?php

namespace Igestis\Bash;
/**
 * Description of IgestisScripts
 *
 * @author Gilles Hemmerlé <giloux@gmail.com>
 */

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class IgestisScripts extends Command {
    protected function configure()
    {
        $this
            ->setName('databases:update')
            ->setDescription('Update igestis database')
        ;
        
        
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        \Igestis\Utils\DBUpdater::init(\Application::getEntityManager());
        $importStatus = \Igestis\Utils\DBUpdater::startUpdate();
        if($importStatus) {
            $output->writeln(\Igestis\I18n\Translate::_("The mysql database has been successfully imported"));
        }
        else {
            $output->writeln(\Igestis\I18n\Translate::_("An error has occured during the import process. Try to import the sql file manually."));
        }
        
    }
}