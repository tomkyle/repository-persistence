<?php

/**
 * tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tests\Unit;

use PHPUnit\Framework\TestCase;
use tomkyle\RepositoryPersistence\Persistence\InMemoryPersistence;

/**
 * @covers \tomkyle\RepositoryPersistence\Persistence\InMemoryPersistence
 */
class InMemoryPersistenceTest extends TestCase
{
    private InMemoryPersistence $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sut = new InMemoryPersistence();
    }

    public function testCreate(): void
    {
        $data = ['name' => 'Test', 'value' => '123'];
        $id = $this->sut->create($data);

        $this->assertIsString($id);
    }

    public function testRetrieve(): void
    {
        $data = ['name' => 'Test', 'value' => '123'];
        $id = $this->sut->create($data);

        $retrievedData = $this->sut->read($id);
        $this->assertSame($data, $retrievedData);
    }

    public function testUpdate(): void
    {
        $data = ['name' => 'Test', 'value' => '123'];
        $id = $this->sut->create($data);

        $updateData = ['id' => $id, 'name' => 'Test Updated', 'value' => '456'];
        $affectedRows = $this->sut->update($updateData);

        $this->assertSame(1, $affectedRows);
        $this->assertSame($updateData, $this->sut->read($id));
    }

    public function testDelete(): void
    {
        $data = ['name' => 'Test', 'value' => '123'];
        $id = $this->sut->create($data);

        $affectedRows = $this->sut->delete($id);
        $this->assertSame(1, $affectedRows);

        $this->expectException(\OutOfBoundsException::class);
        $this->sut->read($id);
    }
}
