<?php

namespace MrDlef\PoleEmploi\Command\Generate;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'generate:protobuf',
    description: 'Generate protobuf files.',
    hidden: false
)]
class Protobuf extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Generating protobuf files!');
        $this->generateProtobufFiles();
        return Command::SUCCESS;
    }

    private function generateProtobufFiles(): void
    {
        // Remove old generated files
        $filesystem = new Filesystem();
        $filesystem->remove(__DIR__ . '/../../../build/protobuf');
        $this->makeMessage('Theme');
        $this->makeMessage('Profession');
        $this->makeMessage('MainProfessionalField');
        $this->makeMessage('ProfessionalField');
        $this->makeMessage('ProfessionName');
        $this->makeMessage('CareerInterest');
        $this->makeMessage('ActivitySector');
    }

    private function makeMessage(string $messageName, string $identifier = 'code'): void
    {
        $content = <<<PROTOBUF
syntax = "proto3";

// This message was auto-generated.
// Do not modify this file manually.

package mrDlef.poleEmploi;

option php_namespace = "MrDlef\\\\PoleEmploi\\\\Messages";
option php_metadata_namespace = "MrDlef\\\\PoleEmploi\\\\Metadata";

message $messageName {
  string $identifier = 1;
}
PROTOBUF;
        (new Filesystem())->dumpFile(__DIR__ . '/../../../build/protobuf/PoleEmploi/' . $messageName . '.proto', $content);
    }

}
