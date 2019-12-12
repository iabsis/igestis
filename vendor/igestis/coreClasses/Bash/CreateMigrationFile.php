<?php

namespace Igestis\Bash;

use Igestis\Types\EnhancedString as IgestisString;

/**
 * Description of IgestisScripts
 *
 * @author Gilles Hemmerlé <giloux@gmail.com>
 */

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class CreateMigrationFile extends Command {
    protected function configure()
    {
        $this
            ->setName('igestis:create-migration-file')
            ->setDescription('Generate an empty migration file')
            ->addArgument(
                'moduleName',
                InputArgument::REQUIRED,
                'Module name'
            )
            ->addArgument(
                'migrationReason',
                InputArgument::REQUIRED,
                'Short migration descripion (included in file name)'
            );

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $moduleName = $input->getArgument('moduleName');
        $migrationReason = IgestisString::slug($input->getArgument('migrationReason'));


        $modulesList = \IgestisModulesList::getInstance();

        if($moduleName != "core" && (!$modulesList->moduleExists($moduleName) || !$modulesList->getFolder($moduleName))) {
            $output->writeln("<error>" . sprintf(\Igestis\I18n\Translate::_("The '%s' module doest not exist"), $moduleName) . "</error>");
            exit(1);
        }

        $file = $modulesList->getFolder($moduleName) . '/install/database/mysql/' . date("Ymd-Hi-") . $migrationReason;
        $pathInfo = pathinfo($file);

        $installFolder = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];

        if (!is_dir($installFolder)) {
            $output->writeln("<info>Folder '$installFolder' does not exist. Trying to create it</info>");
            try {
                mkdir($installFolder, 0755, true);
            } catch(\Exception $e) {
                $output->writeln("<error>Error during folder creation : " . $e->getMessage() . "</error>");
                exit(2);
            }

        }

        $output->writeln('Create file : ' . $file);
        try {
            file_put_contents($file, '-- Put here your sql content');
        } catch(\Exception $e) {
            $output->writeln("<error>Error during folder creation : " . $e->getMessage() . "</error>");
            exit(3);
        }

        $output->writeln("<info>Done</info>");
    }
}
