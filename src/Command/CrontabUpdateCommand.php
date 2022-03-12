<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CrontabUpdateCommand extends Command
{
    public const FILE_PATH = __DIR__ . '/../../config/crontab';
    public const USER = 'app';

    protected function configure(): void
    {
        $this->setName('app:crontab:update');
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if(!file_exists(self::FILE_PATH)) {
            $output->writeln('<error>crontab file not found in current project</error>');
            return 1;
        }

        $output->writeln('Crontab config before update:');
        exec(sprintf('crontab -u %s -l', self::USER), $res);
        foreach ($res as $line) {
            $output->writeln((string)$line);
        }

        exec(sprintf('crontab -u %s %s', self::USER, self::FILE_PATH), $result);
        foreach ($result as $line) {
            $output->writeln((string)$line);
        }

        $output->writeln('Crontab config after update:');
        exec(sprintf('crontab -u %s -l', self::USER), $resultNew);
        foreach ($resultNew as $line) {
            $output->writeln((string)$line);
        }

        return 0;
    }
}