<?php

/**
 * tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tomkyle\RepositoryPersistence\Persistence;

use Nette\Utils\Random;

/**
 * File-based persistence implementation.
 */
abstract class FilePersistenceAbstract implements FilePersistence
{
    /**
     * @var string The base directory for storage.
     */
    protected string $basedir;

    /**
     * @var int The output directory permissions.
     */
    protected int $directory_permissions = 0755;

    /**
     * Examples: "json", "yaml"
     */
    protected string $file_extension = '';

    /**
     * Options for encoding data for file content.
     *
     * @var int
     */
    protected $encode_options = 0;

    /**
     * Options for decoding file content function.
     *
     * @var int
     */
    protected $decode_options = 0;



    /**
     * @param string $basedir The base directory for file storage.
     */
    public function __construct(string $basedir = null)
    {
        $basedir = $basedir ?: getcwd();
        if ($basedir === false) {
            throw new \RuntimeException("Could not determine work directory (getcwd)");
        }

        $this->setBaseDir($basedir);
    }



    /**
     * @inheritDoc
     */
    abstract public function encode(array $data): string;

    /**
     * @inheritDoc
     */
    abstract public function decode(string $content): array;



    /**
     * @inheritDoc
     */
    public function getExtension(bool $dot = false): string
    {

        if (!$dot) {
            return $this->file_extension;
        }

        return $this->file_extension !== '' && $this->file_extension !== '0' ? "." . $this->file_extension : "";
    }


    /**
     * @inheritDoc
     */
    public function getBaseDir(): string
    {
        return $this->basedir;
    }


    /**
     * @inheritDoc
     */
    public function getFilePath(int|string $id): string
    {
        if (is_string($id) && ($id === '' || $id === '0')) {
            throw new \UnexpectedValueException("ID must not be empty");
        }

        $ext = $this->getExtension(dot: true);
        return implode(DIRECTORY_SEPARATOR, [$this->basedir, $id]) . $ext;
    }



    /**
     * Sets the decoding options.
     *
     * @param int $options Bitmask consisting of JSON decode option constants.
     * @return self Returns instance of JsonEncoder for method chaining.
     */
    public function setDecodeOptions(int $options): self
    {
        $this->decode_options = $options;
        return $this;
    }

    /**
     * Sets the encoding options.
     *
     * @param int $options Bitmask consisting of JSON encode option constants.
     * @return self Returns instance of JsonEncoder for method chaining.
     */
    public function setEncodeOptions(int $options): self
    {
        $this->encode_options = $options;
        return $this;
    }


    /**
     * @inheritDoc
     * @param int $permissions Optional: Override directory permissions, default: null
     */
    public function setBaseDir(string $basedir, int $permissions = null): self
    {
        $basedir = rtrim($basedir, DIRECTORY_SEPARATOR);
        if ($basedir === '' || $basedir === '0') {
            throw new \UnexpectedValueException("Invalid directory name, must not be empty or root /");
        }

        $this->basedir = $basedir;
        if (!is_dir($this->basedir)) {
            $result = mkdir($this->basedir, $permissions ?: $this->directory_permissions, true);

            if ($result === false) {
                $msg = sprintf("Could not create directory %s (mkdir)", $this->basedir);
                throw new \RuntimeException($msg);
            }
        }

        return $this;
    }



    /**
     * @inheritDoc
     */
    public function create(array $data): string|int
    {
        if (!array_key_exists('id', $data)) {
            $data['id'] = Random::generate(10);
        }

        $id = $data['id'];
        if (!is_string($id) && !is_int($id)) {
            $msg = "Element 'id' not of type string|int";
            throw new \UnexpectedValueException($msg);
        }

        $file_path = $this->getFilePath($id);
        $content = $this->encode($data);

        $this->writeToFile($file_path, $content);

        return $id;
    }

    /**
     * @inheritDoc
     *
     * @throws \RuntimeException if file is not readable
     * @throws \UnexpectedValueException if reading file failed
     * @throws \UnexpectedValueException if decoding JSON failed
     * @throws \OutOfBoundsException when no record can be found
     */
    public function read(string|int $id): array
    {
        $file_path = $this->getFilePath($id);

        try {
            $data = $this->readFromFile($file_path);
            $result = $this->decode($data);
        } catch (\OutOfBoundsException $e) {
            $msg = sprintf("File for ID '%s' not found: %s", $id, $file_path);
            throw new \OutOfBoundsException($msg, 0, $e);
        } catch (\Throwable $e) {
            $msg = sprintf("Caught exception for ID '%s' (file %s)", $id, $file_path);
            throw new \RuntimeException($msg, 0, $e);
        }

        return $result;
    }


    /**
     * @inheritDoc
     */
    public function readAll(): array
    {
        $ext = $this->getExtension(dot: true);
        $files = glob($this->basedir . ('/*' . $ext)) ?: [];

        $id_array = array_map(static fn ($file) => basename($file, $ext), $files);
        $all_read = array_map(fn (string|int $id): array => $this->read($id), $id_array);

        return array_filter($all_read);
    }

    /**
     * @inheritDoc
     *
     * @throws \UnexpectedValueException if ID element is not string|int
     * @throws \UnexpectedValueException if file can not be found
     */
    public function update(array $data): int
    {
        $id = $data['id'] ?? null;
        if (!is_string($id) && !is_int($id)) {
            $msg = "Element 'id' not of type string|int";
            throw new \UnexpectedValueException($msg);
        }

        $file_path = $this->getFilePath($id);
        if (!file_exists($file_path)) {
            $msg = sprintf("File %s not found for ID '%s'", $file_path, $id);
            throw new \UnexpectedValueException($msg);
        }

        $content = $this->encode($data);
        $result = $this->writeToFile($file_path, $content);

        return $result ? 1 : 0;
    }

    /**
     * @inheritDoc
     */
    public function delete(string|int $id): int
    {
        $file_path = $this->getFilePath($id);

        $exists = file_exists($file_path);
        if (!$exists) {
            return 0;
        }

        $unlinked = unlink($file_path);
        if (!$unlinked) {
            $msg = sprintf("Failed deleting file %s for ID '%s'", $file_path, $id);
            throw new \RuntimeException($msg);
        }

        return 1;
    }


    /**
     * @inheritDoc
     */
    public function readFromFile(string $file_path): string
    {
        if (!file_exists($file_path)) {
            $msg = sprintf("File not found: %s", $file_path);
            throw new \OutOfBoundsException($msg);
        }

        if (!is_readable($file_path)) {
            $msg = sprintf("File not readable: %s", $file_path);
            throw new \RuntimeException($msg);
        }

        $data = file_get_contents($file_path);
        if (!is_string($data)) {
            $msg = sprintf("Failed reading file: %s", $file_path);
            throw new \UnexpectedValueException($msg);
        }

        return $data;
    }


    /**
     * @inheritDoc
     */
    public function writeToFile(string $file_path, \Stringable|string $file_content): bool
    {
        if ($file_content instanceof \Stringable) {
            $file_content = $file_content->__toString();
        }

        $result = file_put_contents($file_path, $file_content);
        if ($result === false) {
            throw new \UnexpectedValueException("Failed to write data to file (file_put_contents).");
        }

        return true;
    }

}
