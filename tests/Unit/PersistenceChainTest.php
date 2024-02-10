<?php

/**
 * tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tests\Unit;

use tomkyle\RepositoryPersistence\Persistence\Persistence;
use tomkyle\RepositoryPersistence\Persistence\PersistenceChain;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the PersistenceChain class.
 */
class PersistenceChainTest extends TestCase
{
    private PersistenceChain $sut;

    private $mockPersistence1;

    private $mockPersistence2;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a single mock object for the Persistence interface
        $this->mockPersistence1 = $this->createMock(Persistence::class);
        $this->mockPersistence2 = $this->createMock(Persistence::class);

        // Initialize PersistenceChain with the mock Persistence object
        $this->sut = new PersistenceChain([
            $this->mockPersistence1,
            $this->mockPersistence2
        ]);
    }

    public static function dataProviderForCreate(): array
    {
        return [
            'simple data' => [['name' => 'Test'], '123'],
        ];
    }

    /**
     * @dataProvider dataProviderForCreate
     */
    public function testCreateDelegates(array $data, string|int $expectedId): void
    {
        $this->mockPersistence1->expects($this->once())->method('create')->with($data)->willReturn($expectedId);
        $this->mockPersistence2->expects($this->once())->method('create')->with($data)->willReturn($expectedId);

        $this->assertSame($expectedId, $this->sut->create($data));
    }

    public static function dataProviderForRetrieve(): array
    {
        return [
            'existing record' => ['123', ['name' => 'Test']],
        ];
    }

    /**
     * @dataProvider dataProviderForRetrieve
     */
    public function testRetrieveDelegates(string|int $id, ?array $expectedData): void
    {
        $this->mockPersistence1->expects($this->once())->method('read')->with($id)->willReturn($expectedData);

        $this->assertEquals($expectedData, $this->sut->read($id));
    }

    public static function dataProviderForUpdate(): array
    {
        return [
            'update record' => [['id' => '123', 'name' => 'Updated'], 2],
        ];
    }

    /**
     * @dataProvider dataProviderForUpdate
     */
    public function testUpdateDelegates(array $data, int $affectedRows): void
    {
        $this->mockPersistence1->expects($this->once())->method('update')->with($data)->willReturn(1);
        $this->mockPersistence2->expects($this->once())->method('update')->with($data)->willReturn(1);

        $this->assertSame($affectedRows, $this->sut->update($data));
    }

    public static function dataProviderForDelete(): array
    {
        return [
            'delete record' => ['123', 2],
        ];
    }

    /**
     * @dataProvider dataProviderForDelete
     */
    public function testDeleteDelegates(string|int $id, int $deletedRows): void
    {
        $this->mockPersistence1->expects($this->once())->method('delete')->with($id)->willReturn(1);
        $this->mockPersistence2->expects($this->once())->method('delete')->with($id)->willReturn(1);

        $this->assertSame($deletedRows, $this->sut->delete($id));
    }
}
