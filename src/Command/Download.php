<?php

namespace MrDlef\PoleEmploi\Command;

use MrDlef\PoleEmploi\PoleEmploiHttpClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'download',
    description: 'Download all needed assets in cache.',
    hidden: false
)]
class Download extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = (new PoleEmploiHttpClient());

        $filesystem = new Filesystem();
        $filesystem->remove(__DIR__ . '/../../json');
        $filesystem->mkdir(__DIR__ . '/../../json');
        $downloadDir = __DIR__ . '/../../json';

        if (! is_dir($downloadDir)) {
            mkdir($downloadDir);
        }

        $output->writeln('Downloading Themes!');
        file_put_contents($downloadDir . '/themes.json', json_encode($client->themes(), JSON_PRETTY_PRINT));

        $output->writeln('Downloading Professions!');
        file_put_contents($downloadDir . '/professions.json', json_encode($client->professions(), JSON_PRETTY_PRINT));

        $output->writeln('Downloading Main Professional Fields!');
        file_put_contents($downloadDir . '/mainProfessionalFields.json', json_encode($client->mainProfessionalFields(), JSON_PRETTY_PRINT));

        $output->writeln('Downloading Professional Fields!');
        file_put_contents($downloadDir . '/professionalFields.json', json_encode($client->professionalFields(), JSON_PRETTY_PRINT));

        $output->writeln('Downloading Professional Names!');
        file_put_contents($downloadDir . '/professionNames.json', json_encode($client->professionNames(), JSON_PRETTY_PRINT));

        $output->writeln('Downloading Career Intesrests!');
        file_put_contents($downloadDir . '/careerInterests.json', json_encode($client->careerInterests(), JSON_PRETTY_PRINT));

        $output->writeln('Downloading Activity Sectors!');
        file_put_contents($downloadDir . '/activitySectors.json', json_encode($client->activitySectors(), JSON_PRETTY_PRINT));

        return Command::SUCCESS;
    }
}