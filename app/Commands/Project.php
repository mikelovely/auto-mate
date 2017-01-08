<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class Project extends Command
{
    const SLASH = DIRECTORY_SEPARATOR;
    const PROJECT_FOLDER = "code";
    const USER_NAME = "mike";

    protected function configure()
    {
        $this->setName('new')
            ->setDescription('Set up a new development environment.')
            ->addArgument('name', InputArgument::REQUIRED, "Project or application name")
            ->addOption('sub', null, InputOption::VALUE_OPTIONAL, 'Name of bootstrap location', '');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // define some variables
        $name = $input->getArgument('name');
        $sub = $input->getOption('sub');
        
        // define some more variables
        $home = self::SLASH . "Users" . self::SLASH . self::USER_NAME . self::SLASH;
        $projects = $home . self::PROJECT_FOLDER . self::SLASH;
        $etc = self::SLASH . "etc" . self::SLASH . "apache2" . self::SLASH;
        $sitesAvailableFilePath = $etc . "sites-available" . self::SLASH . $name . ".conf";
        $sitesEnabledFilePath = $etc . "sites-enabled" . self::SLASH . $name . ".conf";

        $fs = new Filesystem();

        // create conf file in sites-available
        $fs->touch($sitesAvailableFilePath);

        // append "/"" to $sub if required (if bootstrap location)
        if ($sub != '') { $sub = self::SLASH . $sub; }

        // put contents in to it
        $fs->dumpFile($sitesAvailableFilePath, '<VirtualHost *:80>
                DocumentRoot "' . $projects . $name . $sub . '"
                ServerName ' . $name . '.dev
            </VirtualHost>
        ');

        // create sym-link at sites-enabled
        $fs->symlink($sitesAvailableFilePath, $sitesEnabledFilePath);

        // create .pow file
        $pow = $home . ".pow" . self::SLASH . $name;
        $fs->touch($pow);

        // put contents in to it
        $fs->dumpFile($pow, '127.0.0.1:80');

        // restart apache
        exec('apachectl restart');

        // Use a bit of Style to make your output look hideous
        // -- Text color not changing in iTerm though :(
        $style = new OutputFormatterStyle('yellow', 'magenta');
        $output->getFormatter()->setStyle('custom', $style);

        $output->writeln("<custom>Your dev environment should be available at: http://{$name}.dev</custom>");
    }
}
