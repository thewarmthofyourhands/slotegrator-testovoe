<?php

declare(strict_types=1);

namespace App\Commands;

use App\Dto\UseCase\Product\AddProductDto;
use App\UseCase\Product\AddProductHandler;
use App\Validation\Schema\Cli\Input\StoreProductDataInputFileSchema;
use App\Validation\Validator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StoreProductDataCommand extends Command
{
    public function __construct(
        private readonly Validator $validator,
        private readonly AddProductHandler $addProductHandler,
        null|string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setName('data:product:store')
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
        $productList = json_decode($data, false,512,JSON_THROW_ON_ERROR);
        $this->validator->validate($productList, StoreProductDataInputFileSchema::SCHEMA);

        foreach ($productList as $product) {
            $dto = new AddProductDto(
                $product->title,
                $product->price,
                $product->eId,
                $product->categoriesEId,
            );
            $this->addProductHandler->handle($dto);
        }

        return Command::SUCCESS;
    }
}
