<?php

/**
 * This file is part of tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tomkyle\RepositoryPersistence\Persistence;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Exception\DumpException;

/**
 * JSON-based persistence implementation.
 */
class YamlFilePersistence extends FilePersistenceAbstract implements FilePersistence
{
    /**
     * Options for YAML::dump function.
     *
     * @var int
     */
    protected $encode_options = Yaml::DUMP_OBJECT_AS_MAP | Yaml::DUMP_EXCEPTION_ON_INVALID_TYPE | Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE;

    /**
     * Options for YAML::parse function.
     *
     * @var int
     */
    protected $decode_options = Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE;


    protected string $file_extension = 'yaml';


    /**
     * Encodes the given data array to a YAML string.
     *
     * @param array<string,mixed> $data Data to update the record with.
     * @return string YAML encoded string.
     * @throws \UnexpectedValueException If encoding fails.
     */
    #[\Override]
    public function encode(array $data): string
    {
        try {
            return Yaml::dump($data, flags: $this->encode_options);
        } catch (\Throwable $throwable) {
            $msg = sprintf("Failed encoding string to YAML: %s: %s", $throwable::class, $throwable->getMessage());
            throw new \UnexpectedValueException($msg, 0, $throwable);
        }
    }

    /**
     * Decodes the given YAML string to a PHP array.
     *
     * @param string $content The YAML string to decode.
     * @return array<mixed,mixed> An array of associative arrays.
     * @throws \UnexpectedValueException If decoding fails or the result is not an array.
     */
    #[\Override]
    public function decode(string $content): array
    {
        try {
            $result = Yaml::parse($content, flags: $this->decode_options);
        } catch (\Throwable $throwable) {
            $msg = sprintf("Failed decoding YAML string: %s: %s", $throwable::class, $throwable->getMessage());
            throw new \UnexpectedValueException($msg, 0, $throwable);
        }

        if (!is_array($result)) {
            throw new \UnexpectedValueException("Decoded YAML did not yield array.");
        }

        return $result;

    }

}
