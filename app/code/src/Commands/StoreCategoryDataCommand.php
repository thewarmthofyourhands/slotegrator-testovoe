<?php

declare(strict_types=1);

namespace App\Commands;

use App\Dto\UseCase\Category\AddCategoryDto;
use App\UseCase\Category\AddCategoryHandler;
use App\Validation\Schema\Cli\Input\StoreCategoryDataInputFileSchema;
use App\Validation\Validator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StoreCategoryDataCommand extends Command
{
    public function __construct(
        private readonly Validator $validator,
        private readonly AddCategoryHandler $addCategoryHandler,
        null|string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setName('data:category:store')
            ->addOption('dataFile', null, InputOption::VALUE_REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dataFile = $input->getOption('dataFile');

        if (null === $dataFile) {
            throw new InvalidOptionException('sourcePath is required option');
        }

        $data = file_get_contents($dataFile);
        $categoryList = json_decode($data, false,512,JSON_THROW_ON_ERROR);
        $this->validator->validate($categoryList, StoreCategoryDataInputFileSchema::SCHEMA);

        foreach ($categoryList as $category) {
            $dto = new AddCategoryDto(
                ...((array) $category),
            );
            $this->addCategoryHandler->handle($dto);
        }

        return Command::SUCCESS;
    }
}
