<?php

namespace Igestis\Bash;

use Igestis\Types\String as IgestisString;

/**
 * Description of IgestisScripts
 *
 * @author Gilles Hemmerlé <giloux@gmail.com>
 */

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class RegenLocales extends Command {
    protected function configure()
    {
        $this
            ->setName('i18n:regen-locales')
            ->setDescription('Regenerate all locales');       
        
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Regenerating locales</info>");
        
        $getTextCaching = new \Igestis\Utils\GetTextCaching();
        $getTextCaching->setCachingForAll();

        $output->writeln("<info>Done</info>");
    }
}
