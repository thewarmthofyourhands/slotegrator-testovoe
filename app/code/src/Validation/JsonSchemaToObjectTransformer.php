<?php

declare(strict_types=1);

namespace App\Validation;

use RuntimeException;

readonly class JsonSchemaToObjectTransformer
{
    public function __construct(
        private string $jsonSchemaPath,
        private string $baseNamespace,
        private string $baseDir,
    ) {}

    private function parseSourceFile(string $jsonSchemaFile): array
    {
        ['extension' => $ext, 'filename' => $fileName, 'dirname' => $fileDir] = pathinfo($jsonSchemaFile);

        if ('json' === $ext) {
            $data = json_decode(file_get_contents($jsonSchemaFile), true);
            return compact('fileName', 'fileDir', 'data');
        }

        throw new RuntimeException("Unknown $ext extension of source file");
    }

    private function scanSource(): array
    {
        $jsonSchemaPathList = [];

        if (is_file($this->jsonSchemaPath)) {
            return [$this->jsonSchemaPath];
        }

        $dirIterator = new \RecursiveDirectoryIterator($this->jsonSchemaPath);
        $iterator = new \RecursiveIteratorIterator($dirIterator);

        foreach ($iterator as $file) {
            assert($file instanceof \SplFileInfo);
            if ($file->isFile()) {
                $jsonSchemaPathList[] = (string) $file;
            }
        }

        return $jsonSchemaPathList;
    }

    private function parseSource(): \Generator
    {
        $sourceList = $this->scanSource();

        foreach ($sourceList as $sourceFilePath) {
            yield $this->parseSourceFile($sourceFilePath);
        }
    }

    public function transform(): void
    {
        foreach ($this->parseSource() as $sourceSchema) {
            $this->generateSchema($sourceSchema);
        }
    }

    /**
     * @throws RuntimeException
     */
    private function mkdir(string $path, int $permission = 0777, bool $recursive = false, mixed $context = null): void
    {
        $mkdirStatus = mkdir($path, $permission, $recursive, $context);

        if (false === $mkdirStatus || false === is_dir($path)) {
            throw new RuntimeException('Failed to make dir');
        }
    }

    private function generateSchema(array $sourceSchema): void
    {
        $data = $sourceSchema['data'];
        $fileDir = str_replace($this->jsonSchemaPath, $this->baseDir, $sourceSchema['fileDir']);
        $fileClass = $sourceSchema['fileName'];
        $namespace = str_replace($this->jsonSchemaPath, $this->baseNamespace, $sourceSchema['fileDir']);
        $namespace = str_replace('/', '\\', $namespace);
        $schema = var_export($data, true);
        $class = <<<EOL
        <?php
        
        declare(strict_types=1);
        
        namespace $namespace;
        
        class $fileClass
        {
            public const SCHEMA = {$schema};
        }
        
        EOL;

        if (false === is_dir($fileDir)) {
            $this->mkdir($fileDir, 0755, true);
        }

        if (file_exists("$fileDir/$fileClass.php")) {
            unlink("$fileDir/$fileClass.php");
        }

        file_put_contents("$fileDir/$fileClass.php", $class);
    }
}
