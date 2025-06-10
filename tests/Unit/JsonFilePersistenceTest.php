<?php

/**
 * This file is part of tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tests\Unit;

use tomkyle\RepositoryPersistence\Persistence\JsonFilePersistence;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use PHPUnit\Framework\TestCase;

/**
 * Test suite for JsonFilePersistence class.
 */
class JsonFilePersistenceTest extends FilePersistenceTestBase
{
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();


        $base_dir = $this->temp_dir->path();
        $this->sut = new JsonFilePersistence($base_dir);
    }



    /**
     * Tests encoding of data to JSON format.
     */
    public function testEncodeDataToJson(): void
    {
        $data = ['key' => 'value', 'number' => 42];
        $json_content = $this->sut->encode($data);

        $validation_result = json_validate($json_content);

        $this->assertTrue($validation_result);
    }


    /**
     * Tests decoding of JSON content back to a PHP array.
     */
    public function testDecodeJsonToData(): void
    {
        $data = ['key' => 'value', 'number' => 42];
        $data_json = json_encode($data);

        $decoded = $this->sut->decode($data_json);

        $this->assertSame($data, $decoded);
    }

}
