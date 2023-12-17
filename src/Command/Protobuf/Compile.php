<?php

namespace MrDlef\PoleEmploi\Command\Protobuf;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'protobuf:compile',
    description: 'Compile protobuf files.',
    hidden: false
)]
class Compile extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Compiling protobuf files!');
        // Remove old generated files
        $filesystem = new Filesystem();
        $filesystem->remove(__DIR__ . '/../../../build/php/*');
        // Compile protobuf files
        $binPath = realpath(__DIR__ . '/../../../bin/protoc');
        $phpPath = realpath(__DIR__ . '/../../../build/php');
        $protobufPath = realpath(__DIR__ . '/../../../build/protobuf');
        $messagesPath = glob($protobufPath . '/**/*.proto');


        $process = new Process([$binPath, '--php_out=' . $phpPath, '--proto_path=' . $protobufPath, ...$messagesPath]);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return Command::SUCCESS;
    }
}