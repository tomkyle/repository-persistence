<?php

/**
 * This file is part of tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tests\Unit;

use PHPUnit\Framework\TestCase;
use tomkyle\RepositoryPersistence\Repositories\RepositoryDecoratorAbstract;
use tomkyle\RepositoryPersistence\Repositories\RepositoryInterface;
use tomkyle\RepositoryPersistence\Repositories\Repository;
use tomkyle\RepositoryPersistence\Persistence\Persistence;
use App\Resources\ResourceInterface;
use App\Resources\ResourceCollectionInterface;

class RepositoryDecoratorAbstractTest extends TestCase
{
    private $repository;

    private $sut;

    private $persistence_mock;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a mock for Persistence
        $this->persistence_mock = $this->createMock(Persistence::class);

        // Create a concrete instance of RepositoryInterface
        $this->repository = new Repository($this->persistence_mock);

        // Instantiate the RepositoryDecoratorAbstract with the concrete repository instance for testing.
        $this->sut = new class ($this->repository) extends RepositoryDecoratorAbstract {};
    }

    /**
     * Tests if getPersistence method correctly delegates the call to the underlying repository.
     */
    public function testGetPersistenceDelegation(): void
    {
        $result = $this->sut->getPersistence();
        $this->assertSame($this->persistence_mock, $result);
    }

    /**
     * Tests if setPersistence method correctly delegates the call to the underlying repository and is chainable.
     */
    public function testSetPersistenceDelegation(): void
    {
        $result = $this->sut->setPersistence($this->persistence_mock);
        $this->assertSame($this->sut, $result);
    }

    /**
     * Tests delegation of the find method to the underlying repository.
     */
    public function testGetMethodDelegation(): void
    {
        $id = 'testId';
        $this->persistence_mock->expects($this->once())
                               ->method('read')
                               ->with($id)
                               ->willReturn(['id' => $id]);

        $result = $this->sut->get($id);
        $this->assertEquals(['id' => $id], $result);
    }

    /**
     * Tests delegation of the findOneBy method to the underlying repository.
     */
    public function testFindOneByDelegation(): void
    {
        $criteria = ['field' => 'value'];
        // $this->expectException(\OutOfBoundsException::class);

        $result = $this->sut->findOneBy($criteria);
        $this->assertNull($result);
        // $this->assertEquals($criteria, $result);
    }

    /**
     * Tests delegation of the findAll method to the underlying repository.
     */
    public function testFindAllDelegation(): void
    {
        $result = $this->sut->findAll();
        $this->assertIsArray($result);
    }

    /**
     * Tests delegation of the findBy method to the underlying repository.
     */
    public function testFindByDelegation(): void
    {
        $criteria = ['field' => 'value'];
        // 'findBy' is not configured in Repository class
        $result = $this->sut->findBy($criteria);
        $this->assertIsIterable($result);
    }

    /**
     * Tests delegation of the save method to the underlying repository.
     */
    public function testSaveDelegation(): void
    {
        $entity = ['id' => 'testId'];
        $this->persistence_mock->expects($this->once())
                               ->method('update')
                               ->with($entity);

        $result = $this->sut->save($entity);
        $this->assertIsBool($result);
    }

    /**
     * Tests delegation of the delete method to the underlying repository.
     */
    public function testDeleteDelegation(): void
    {
        $id = 'testId';
        $entity = ['id' => $id];

        $this->persistence_mock->expects($this->once())
                               ->method('delete')
                               ->with($id);

        $result = $this->sut->delete($entity);
        $this->assertIsBool($result);

    }
}
