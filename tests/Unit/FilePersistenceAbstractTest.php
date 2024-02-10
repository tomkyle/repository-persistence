<?php

/**
 * tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tests\Unit;

use PHPUnit\Framework\TestCase;
use tomkyle\RepositoryPersistence\Persistence\FilePersistenceAbstract;
use Spatie\TemporaryDirectory\TemporaryDirectory;

/**
 * Test suite for the FilePersistence abstract class.
 */
class FilePersistenceAbstractTest extends TestCase
{
    private FilePersistenceAbstract $sut;

    private $temp_dir;

    protected function setUp(): void
    {
        $this->temp_dir = (new TemporaryDirectory())->create();

        // Mock the FilePersistenceAbstract abstract class, specifying default behavior for abstract methods.
        $temp_dir = $this->temp_dir->path();

        $this->sut = $this->getMockForAbstractClass(FilePersistenceAbstract::class, [$temp_dir]);

        $this->sut->method('encode')->willReturnCallback(static fn ($data) => json_encode($data));
        $this->sut->method('decode')->willReturnCallback(static fn ($content) => json_decode((string) $content, true));
    }

    protected function tearDown(): void
    {
        $this->temp_dir->delete();
        parent::tearDown();
    }

    /**
     * Tests whether the FilePersistenceAbstract constructor initializes with a default base directory.
     */
    public function testConstructWithDefaultBaseDir(): void
    {
        $this->assertInstanceOf(FilePersistenceAbstract::class, $this->sut);
    }

    /**
     * Verifies that an exception is thrown when attempting to set an invalid base directory.
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
     * Ensures that setBaseDir creates the directory if it does not exist, using the provided permissions.
     */
    public function testSetBaseDirCreatesDirectoryOnNonExistentPath(): void
    {
        $nonExistentPath = sys_get_temp_dir() . '/nonexistent_' . uniqid();
        $this->sut->setBaseDir($nonExistentPath);
        $this->assertDirectoryExists($nonExistentPath);
        rmdir($nonExistentPath); // Cleanup
    }

    /**
     * Tests creating a new record and reading it back to ensure data integrity.
     */
    public function testCreateAndReadRecord(): void
    {
        $id = uniqid();
        $data = ['key' => 'value', 'id' => $id];

        $found = $this->sut->create($data);
        $this->assertNotNull($found);

        $readData = $this->sut->read($id);
        $this->assertSame($data, $readData);
    }

    /**
     * Verifies that attempting to update a record that does not exist throws an exception.
     */
    public function testUpdateNonexistentRecordThrowsException(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->sut->update(['id' => 'nonexistent', 'key' => 'value']);
    }

    /**
     * Confirms that deleting a nonexistent record returns zero, indicating failure.
     */
    public function testDeleteNonexistentRecordReturnsZero(): void
    {
        $result = $this->sut->delete('nonexistent');
        $this->assertSame(0, $result);
    }

    /**
     * Tests the ability to read all records in the storage directory.
     */
    public function testReadAllRecords(): void
    {
        // Setup: Create two records.
        $data1 = ['key' => 'value1'];
        $id1 = $this->sut->create($data1);
        $data1['id'] = $id1;

        $data2 = ['key' => 'value2'];
        $id2 = $this->sut->create($data2);
        $data2['id'] = $id2;

        $allData = $this->sut->readAll();
        $this->assertCount(2, $allData);

        $this->assertContains($data1, $allData);
        $this->assertContains($data2, $allData);

        // Cleanup: Delete created records.
        $this->sut->delete($id1);
        $this->sut->delete($id2);
    }

    /**
     * Ensures the update functionality correctly updates an existing record's data.
     */
    public function testUpdateRecord(): void
    {
        // Setup: Create a record to update.
        $originalData = ['key' => 'originalValue'];
        $id = $this->sut->create($originalData);

        // Test: Update the record and read back the updated data.
        $updatedData = ['id' => $id, 'key' => 'updatedValue'];
        $this->sut->update($updatedData);
        $readData = $this->sut->read($id);

        $this->assertSame($updatedData, $readData);

        // Cleanup: Delete the updated record.
        $this->sut->delete($id);
    }

    /**
     * Verifies that the delete method successfully removes an existing record.
     */
    public function testDeleteRecord(): void
    {
        $id = uniqid();
        $data = ['key' => 'value', 'id' => $id];

        $new_id = $this->sut->create($data);
        $this->assertNotNull($new_id);

        $entity = $this->sut->read($new_id);
        $this->assertSame($data, $entity);

        $deleteResult = $this->sut->delete($new_id);
        $this->assertSame(1, $deleteResult);

        $this->expectException(\OutOfBoundsException::class);
        $this->sut->read($new_id);

    }
}
