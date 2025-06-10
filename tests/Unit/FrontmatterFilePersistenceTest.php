<?php

/**
 * This file is part of tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tests\Unit;

use PHPUnit\Framework\TestCase;
use tomkyle\RepositoryPersistence\Persistence\FrontmatterFilePersistence;
use tomkyle\RepositoryPersistence\Persistence\FilePersistence;
use tomkyle\RepositoryPersistence\Persistence\Persistence;
use Symfony\Component\Yaml\Yaml;

/**
 * Tests functionality of the FrontmatterFilePersistence class.
 *
 * This test suite ensures that FrontmatterFilePersistence handles the reading and writing
 * of files with YAML frontmatter correctly. It verifies that frontmatter is properly parsed
 * and included/excluded in file operations, adhering to expectations for such a persistence
 * mechanism.
 */
class FrontmatterFilePersistenceTest extends TestCase
{
    /**
     * @var FilePersistence Mock of the FilePersistence interface for dependency injection.
     */
    private $filePersistenceMock;

    /**
     * @var FrontmatterFilePersistence The instance of FrontmatterFilePersistence under test.
     */
    private FrontmatterFilePersistence $sut;

    /**
     * @var string Path to a temporary file used during testing.
     */
    private string $tempFilePath;

    /**
     * @var string Sample content body without frontmatter for testing.
     */
    private string $testContent = "Content without frontmatter";

    /**
     * @var array Sample frontmatter data for testing.
     */
    private array $frontmatter = ['title' => 'Test Title', 'foo' => 'bar'];

    /**
     * Sets up the test environment with a mock FilePersistence and an instance of the SUT.
     * It also creates a temporary file path for use in file operations.
     */
    protected function setUp(): void
    {
        parent::setUp();
        // $this->innerPersistenceMock = $this->createMock(Persistence::class);
        // $this->innerPersistenceMock->method('read')->willReturn(['foo' => 'bar']);

        $this->filePersistenceMock = $this->createMock(FilePersistence::class);
        $this->filePersistenceMock->method('read')->willReturn(['foo' => 'bar']);

        $this->sut = new FrontmatterFilePersistence($this->filePersistenceMock, $this->frontmatter);

        $this->tempFilePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('test', true) . '.md';
    }

    /**
     * Cleans up the testing environment by removing the temporary file if it exists.
     */
    protected function tearDown(): void
    {
        if (file_exists($this->tempFilePath)) {
            unlink($this->tempFilePath);
        }

        parent::tearDown();
    }

    /**
     * Verifies that frontmatter data can be set and retrieved accurately.
     */
    public function testSetAndGetFrontmatter(): void
    {
        $this->sut->setFrontmatter($this->frontmatter);
        $this->assertEquals($this->frontmatter, $this->sut->getFrontmatter(), 'The frontmatter should be available by getter method.');
    }




    /**
     * Tests the read(id) method, ensuring the frontmatter is stored correctly
     * in the second parameter.
     */
    public function testReadWithFrontmatterFromFile(): void
    {
        $sample = ['foo' => 'bar'];

        $fileContent = "---\ntitle: Test Title\n" . Yaml::dump($sample) . "\n---\n" . $this->testContent;
        $this->filePersistenceMock->method('readFromFile')->willReturn($fileContent);
        $this->filePersistenceMock->method('decode')->willReturn($sample);

        $data = $this->sut->read(42, $frontmatter);
        $this->assertEquals($sample, $data);
        $this->assertEquals($this->frontmatter, $frontmatter, 'Frontmatter should be stored in second parameter variable.');
    }






    /**
     * Tests the reading of a file's content, ensuring the frontmatter is parsed correctly
     * and the content body is returned accurately.
     */
    public function testReadFromFile(): void
    {
        $fileContent = "---\ntitle: Test Title\nfoo: bar\n---\n" . $this->testContent;
        $this->filePersistenceMock->method('readFromFile')->willReturn($fileContent);

        $content = $this->sut->readFromFile($this->tempFilePath, $frontmatter);
        $this->assertEquals($this->testContent, $content, 'The body content should be correctly extracted excluding the frontmatter.');
        $this->assertEquals($this->frontmatter, $this->sut->getFrontmatter(), 'The frontmatter should be available by getter method.');
        $this->assertEquals($this->frontmatter, $frontmatter, 'Frontmatter should be stored in second parameter variable.');
    }

    /**
     * Tests the writing of a file's content, confirming that frontmatter is correctly
     * prepended to the file's content.
     */
    public function testWriteToFile(): void
    {
        $this->filePersistenceMock->expects($this->once())->method('writeToFile')
            ->with(
                $this->equalTo($this->tempFilePath),
                $this->callback(fn($fileContent) => str_starts_with((string) $fileContent, "---\n") && str_contains((string) $fileContent, $this->testContent))
            )
            ->willReturn(true);

        $result = $this->sut->writeToFile($this->tempFilePath, $this->testContent);
        $this->assertTrue($result, 'The writeToFile method should successfully delegate to the wrapped persistence while including frontmatter.');
    }
}
