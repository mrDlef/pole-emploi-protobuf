<?php

namespace MrDlef\PoleEmploi\Command\Protobuf;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use ZipArchive;

#[AsCommand(
    name: 'protobuf:install',
    description: 'Install protobuf compiler.',
    hidden: false
)]
class Install extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Installing protobuf compiler!');
        $httpResponse = HttpClient::create()->request('GET', 'https://api.github.com/repos/protocolbuffers/protobuf/releases/latest');
        $content = json_decode($httpResponse->getContent());
        $tagName = ltrim($content->tag_name, 'v');
        $fileName = sprintf('protoc-%s-%s.zip', $tagName, 'linux-x86_64');
        $latestZipUrl = current(array_filter($content->assets, fn ($asset) => $asset->name === $fileName))->browser_download_url;

        $protocZip = HttpClient::create()->request('GET', $latestZipUrl)->getContent();
        // Write protoc zip in tmp dir
        $tmpFileName = tempnam(sys_get_temp_dir(), 'protoc');
        file_put_contents($tmpFileName, $protocZip);
        // Unzip protoc
        $zip = new ZipArchive();
        $zip->open($tmpFileName);
        // Extract in tmp dir
        $tmpDir = sys_get_temp_dir();
        $zip->extractTo($tmpDir);
        $zip->close();
        // Move protoc to bin dir
        rename($tmpDir . '/bin/protoc', __DIR__ . '/../../../bin/protoc');
        // Make protoc executable
        chmod(__DIR__ . '/../../../bin/protoc', 0755);
        return Command::SUCCESS;
    }
}