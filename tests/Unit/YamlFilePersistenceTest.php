<?php

/**
 * This file is part of tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tests\Unit;

use PHPUnit\Framework\TestCase;
use tomkyle\RepositoryPersistence\Persistence\YamlFilePersistence;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Symfony\Component\Yaml\Yaml;

/**
 * Test suite for YamlFilePersistence class.
 */
class YamlFilePersistenceTest extends FilePersistenceTestBase
{
    protected function setUp(): void
    {
        parent::setUp();

        $base_dir = $this->temp_dir->path();
        $this->sut = new YamlFilePersistence($base_dir);
    }


    /**
     * Tests encoding of data to YAML format.
     */
    public function testEncodeDataToYaml(): void
    {
        $data = ['key' => 'value', 'number' => 42];
        $yaml_content = $this->sut->encode($data);

        // Assert that the YAML content contains expected keys and values
        $this->assertStringContainsString('key: value', $yaml_content);
        $this->assertStringContainsString('number: 42', $yaml_content);
    }

    /**
     * Tests decoding of YAML content back to a PHP array.
     */
    public function testDecodeYamlToData(): void
    {
        $yaml_content = "key: value\nnumber: 42";
        $expected = ['key' => 'value', 'number' => 42];
        $decoded = $this->sut->decode($yaml_content);

        $this->assertSame($expected, $decoded);
    }

    /**
     * Verifies that decoding invalid YAML content throws an exception.
     */
    public function testDecodeInvalidYamlThrowsException(): void
    {
        // Simulate invalid YAML content.
        $invalidYamlContent = "key: : value";

        $this->expectException(\UnexpectedValueException::class);
        $this->sut->decode($invalidYamlContent);
    }
}
