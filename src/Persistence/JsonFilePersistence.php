<?php

/**
 * This file is part of tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tomkyle\RepositoryPersistence\Persistence;

// use Nette\Utils\Json;


/**
 * JSON-based persistence implementation.
 */
class JsonFilePersistence extends FilePersistenceAbstract implements FilePersistence
{
    /**
     * Options for json_encode function.
     *
     * @var int
     */
    protected $encode_options = JSON_PRETTY_PRINT;

    /**
     * Options for json_decode function.
     *
     * @var int
     */
    protected $decode_options = JSON_OBJECT_AS_ARRAY;


    protected string $file_extension = 'json';


    /**
     * Encodes the given data array to a JSON string.
     *
     * @param array<string,mixed> $data Data to update the record with.
     * @return string JSON encoded string.
     * @throws \UnexpectedValueException If encoding fails.
     */
    #[\Override]
    public function encode(array $data): string
    {
        try {
            // $result = Json::encode($data, pretty: true);
            $result = json_encode($data, flags: $this->decode_options | JSON_THROW_ON_ERROR);
            if (($error = json_last_error()) !== 0) {
                throw new \JsonException(json_last_error_msg(), $error);
            }

            return $result;
        } catch (\Throwable $throwable) {
            $msg = sprintf("Failed encoding string to JSON: %s: %s", $throwable::class, $throwable->getMessage());
            throw new \RuntimeException($msg, 0, $throwable);
        }
    }

    /**
     * Decodes the given JSON string to a PHP array.
     *
     * @param string $content The JSON string to decode.
     * @return array<mixed,mixed> An array of associative arrays.
     * @throws \RuntimeException If decoding fails or the result is not an array.
     */
    #[\Override]
    public function decode(string $content): array
    {
        try {
            // $result = Json::decode($content, forceArrays: true);
            $result = json_decode($content, associative: true, flags: $this->decode_options | JSON_THROW_ON_ERROR);
            if (($error = json_last_error()) !== 0) {
                throw new \JsonException(json_last_error_msg(), $error);
            }
        } catch (\Throwable $throwable) {
            $msg = sprintf("Failed decoding JSON string: %s: %s", $throwable::class, $throwable->getMessage());
            throw new \RuntimeException($msg, 0, $throwable);
        }

        if (!is_array($result)) {
            throw new \UnexpectedValueException("Decoding JSON did not yield array.");
        }

        return $result;
    }


}
