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
    public function getExtension(): string
    {
        return $this->persistence->getExtension();
    }

    /**
     * @inheritDoc
     */
    public function getFilePath(int|string $id): string
    {
        return $this->persistence->getFilePath($id);
    }

    /**
     * @inheritDoc
     */
    public function setBaseDir(string $basedir): self
    {
        $this->persistence->setBaseDir($basedir);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBaseDir(): string
    {
        return $this->persistence->getBaseDir();
    }

    /**
     * @inheritDoc
     */
    public function readFromFile(string $file_path): string
    {
        return $this->persistence->readFromFile($file_path);
    }

    /**
     * @inheritDoc
     */
    public function writeToFile(string $file_path, \Stringable|string $file_content): bool
    {
        return $this->persistence->writeToFile($file_path, $file_content);
    }

    /**
     * @inheritDoc
     */
    public function encode(array $data): string
    {
        return $this->persistence->encode($data);
    }

    /**
     * @inheritDoc
     */
    public function decode(string $content): array
    {
        return $this->persistence->decode($content);
    }

}
