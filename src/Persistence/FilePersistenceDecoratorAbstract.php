<?php

/**
 * This file is part of tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tomkyle\RepositoryPersistence\Persistence;

/**
 * Abstract Decorator
 */
abstract class FilePersistenceDecoratorAbstract extends PersistenceDecoratorAbstract implements FilePersistence
{
    /**
     * @var FilePersistence The persistence mechanism.
     */
    public $persistence;


    public function __construct(FilePersistence $persistence)
    {
        parent::__construct($persistence);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getExtension(): string
    {
        return $this->persistence->getExtension();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getFilePath(int|string $id): string
    {
        return $this->persistence->getFilePath($id);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function setBaseDir(string $basedir): self
    {
        $this->persistence->setBaseDir($basedir);
        return $this;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getBaseDir(): string
    {
        return $this->persistence->getBaseDir();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function readFromFile(string $file_path): string
    {
        return $this->persistence->readFromFile($file_path);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function writeToFile(string $file_path, \Stringable|string $file_content): bool
    {
        return $this->persistence->writeToFile($file_path, $file_content);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function encode(array $data): string
    {
        return $this->persistence->encode($data);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function decode(string $content): array
    {
        return $this->persistence->decode($content);
    }

}
