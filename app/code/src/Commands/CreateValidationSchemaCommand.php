<?php

declare(strict_types=1);

namespace App\Commands;

use App\Validation\JsonSchemaToObjectTransformer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateValidationSchemaCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('validation:schema:generate')
            ->addOption('sourcePath', null, InputOption::VALUE_REQUIRED)
            ->addOption('targetNamespace', null, InputOption::VALUE_OPTIONAL)
            ->addOption('targetDir', null, InputOption::VALUE_OPTIONAL)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sourcePath = $input->getOption('sourcePath');

        if (null === $sourcePath) {
            throw new InvalidOptionException('sourcePath is required option');
        }

        $targetNamespace = $input->getOption('targetNamespace') ?? 'App\\Validation\\Schema';
        $targetDir = $input->getOption('targetDir') ?? 'src/Validation/Schema';
        $creator = new JsonSchemaToObjectTransformer($sourcePath, $targetNamespace, $targetDir);
        $creator->transform();

        return Command::SUCCESS;
    }
}
