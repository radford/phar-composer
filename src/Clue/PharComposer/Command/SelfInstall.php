<?php

namespace Clue\PharComposer\Command;

use Symfony\Component\Console\Command\Command;
use Clue\PharComposer\Packager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;

class SelfInstall extends Command
{
    protected function configure()
    {
        $this->setName('self-install')
             ->setDescription('System wide installation of phar-composer');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $packager = new Packager();
        $packager->setOutput($output);

        if (substr(__FILE__, 0, '7') === 'phar://') {
            $output->writeln('<info>Already a phar!</info> Consider running "phar-composer install clue/phar-composer" instead?');

            // TODO: copy $_SERVER['PHP_SELF'] instead?

            return;
        }

        $packager->coerceWritable();

        $pharer = $packager->getPharer(__DIR__ . '/../../../../');

        $path = $packager->getSystemBin($pharer, null);

        if (is_file($path)) {
            $dialog = $this->getHelperSet()->get('dialog');
            /* @var $dialog DialogHelper */

            if (!$dialog->askConfirmation($output, 'Overwrite existing file <info>' . $path . '</info>? [y] > ')) {
                $output->writeln('Aborting');
                return;
            }
        }

        $packager->install($pharer, $path);
    }
}
