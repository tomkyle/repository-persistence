<?php

/**
 * tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tests\Unit;

use PHPUnit\Framework\TestCase;
use tomkyle\RepositoryPersistence\Persistence\YamlFilePersistence;
use Spatie\TemporaryDirectory\TemporaryDirectory;

/**
 * Base Test suite for FilePersistence instances.
 * @covers \tomkyle\RepositoryPersistence\Persistence\FilePersistence
 */
abstract class FilePersistenceTestBase extends TestCase
{
    protected TemporaryDirectory $temp_dir;

    protected $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->temp_dir = (new TemporaryDirectory())->create();

        $base_dir = $this->temp_dir->path();
        $this->sut = new YamlFilePersistence($base_dir);
    }

    protected function tearDown(): void
    {
        $this->temp_dir->delete();
        parent::tearDown();
    }


    protected function createFilePath($id): string
    {
        $base_dir = $this->temp_dir->path();
        $extension = $this->sut->getExtension();
        $filename = $id . ('.' . $extension);

        return implode(DIRECTORY_SEPARATOR, [$base_dir, $filename]);
    }



    /**
     * Tests that the correct file extension is returned.
     */
    public function testGetExtensionReturnsExtension(): void
    {
        $extension = $this->sut->getExtension();
        $this->assertIsString($extension);
        $this->assertNotEmpty($extension);
    }


    /**
     * Verifies that creating a new record stores a file in the base directory.
     */
    public function testCreate(): void
    {
        $data = ['key' => 'value'];
        $id = $this->sut->create($data);
        $this->assertNotEmpty($id, 'ID should not be empty.');

        $file_path = $this->createFilePath($id);

        $this->assertFileExists($file_path, 'File should exist after creation.');
    }

    /**
     * Ensures that reading a previously created record returns the correct data.
     */
    public function testRead(): void
    {
        $data = ['key' => 'value'];
        $id = $this->sut->create($data);

        $data_with_id = array_merge($data, ['id' => $id]);

        $result = $this->sut->read($id);
        $this->assertEquals($result, $data_with_id, 'The read data should match the original data.');
    }


    /**
     * Tests updating an existing record correctly alters the stored data.
     */
    public function testUpdate(): void
    {
        $data = ['key' => 'original value'];
        $id = $this->sut->create($data);

        $updated_data = ['id' => $id, 'key' => 'updated value'];
        $affectedRows = $this->sut->update($updated_data);
        $this->assertEquals(1, $affectedRows, 'One row should be affected by the update.');

        $readData = $this->sut->read($id);
        $this->assertEquals($updated_data, $readData, 'The read data should match the updated data.');
    }

    /**
     * Verifies that deleting a record removes the corresponding file and makes it unreadable.
     */
    public function testDelete(): void
    {
        $data = ['key' => 'value'];
        $id = $this->sut->create($data);

        $affectedRows = $this->sut->delete($id);
        $this->assertEquals(1, $affectedRows, 'One row should be affected by the delete.');

        $file_path = $this->createFilePath($id);
        $this->assertFalse(file_exists($file_path));


        $this->expectException(\OutOfBoundsException::class);
        $this->sut->read($id);
    }


    /**
     * Confirms that readAll correctly returns all records stored in the base directory.
     */
    public function testReadAll(): void
    {
        $data1 = ['key1' => 'value1'];
        $data2 = ['key2' => 'value2'];

        $this->sut->create($data1);
        $this->sut->create($data2);

        $allData = $this->sut->readAll();
        $this->assertCount(2, $allData, 'All records should be returned.');
    }


    /**
     * @dataProvider invalidBaseDirProvider
     */
    public function testExceptionOnInvalidSetBaseDir(string $new_base_dir): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->sut->setBaseDir($new_base_dir);
    }

    public static function invalidBaseDirProvider(): array
    {
        return [
            'root directory' => ['/'],
            'empty directory' => ['']
        ];
    }


    /**
     * Ensures that changing the base directory affects where files are stored and read from.
     */
    public function testSetBaseDir(): void
    {
        $temp_dir = (new TemporaryDirectory())->create();
        $new_base_dir = $temp_dir->path();

        $this->sut->setBaseDir($new_base_dir);

        $data = ['key' => 'value'];
        $id = $this->sut->create($data);
        $extension = $this->sut->getExtension();

        $new_file = $new_base_dir . DIRECTORY_SEPARATOR . $id . ('.' . $extension);
        $this->assertFileExists($new_file, 'JSON file should exist in the new base directory after creation.');

        $temp_dir->delete();
    }

}
