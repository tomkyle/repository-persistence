<?php

/**
 * tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tomkyle\RepositoryPersistence\Persistence;

/**
 * interface for file-based persistence implementations.
 */
interface FilePersistence extends Persistence
{
    /**
     * Retrieves the file extension associated with encoded files.
     *
     * @return string File extension
     */
    public function getExtension(): string;

    /**
     * Sets base directory.
     *
     * @param string $basedir The directory in which data files reside.
     *
     * @return self Returns itself to allow for method chaining.
     *
     * @throws \UnexpectedValueException when invalid directory namen given
     * @throws \RuntimeException when creating directory failed.
     */
    public function setBaseDir(string $basedir): self;


    /**
     * Returns the base directory.
     *
     * @return string The base directory
     */
    public function getBaseDir(): string;

    /**
     * Constructs the file path for a given ID.
     *
     * @param string|int $id The record ID.
     * @return string The file path.
     */
    public function getFilePath(int|string $id): string;

    /**
     * Reads from the given file.
     *
     * @param string $file_path The file to read
     *
     * @throws \OutOfBoundsException when no record can be found
     * @throws \RuntimeException if file is not readable
     * @throws \UnexpectedValueException if file_get_contents failed
     */
    public function readFromFile(string $file_path): string;

    /**
     * Writes data to file.
     *
     * @param string $file_path    The file to write the record in.
     * @param \Stringable|string $file_content The file content.
     *
     * @throws \UnexpectedValueException when file writing failed.
     *
     * @return bool Success
     */
    public function writeToFile(string $file_path, \Stringable|string $file_content): bool;

    /**
     * Creates a string representation for the given data.
     *
     * @param array<string,mixed> $data Data to update the record with.
     * @return string String representation
     *
     * @throws \UnexpectedValueException when encoding fails.
     */
    public function encode(array $data): string;

    /**
     * @param  string $content Encoded string
     * @return array<string,mixed> An associative array of the record.
     *
     * @throws \UnexpectedValueException when decoding fails.
     */
    public function decode(string $content): array;
}
