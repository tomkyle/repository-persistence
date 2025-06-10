<?php

/**
 * This file is part of tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tests\Unit;

use PHPUnit\Framework\TestCase;
use tomkyle\RepositoryPersistence\Repositories\Repository;
use tomkyle\RepositoryPersistence\Persistence\Persistence;
use tomkyle\RepositoryPersistence\Persistence\NoPersistence;
use tomkyle\RepositoryPersistence\Persistence\InMemoryPersistence;

/**
 * Unit tests for RepositoryAbstract.
 *
 * Verifies the initialization and default behaviors of RepositoryAbstract methods,
 * including handling of persistence and resource factory dependencies.
 */
class RepositoryTest extends TestCase
{
    private $sample_record = ['id' => 'abcdef', 'foo' => 'bar'];

    private $persistence_mock;

    private $sut; // Subject under test

    protected function setUp(): void
    {
        parent::setUp();

        $this->persistence_mock = $this->createMock(Persistence::class);
        $this->persistence_mock->method('read')->willReturn($this->sample_record);

        $this->sut = new Repository($this->persistence_mock);
    }


    /**
     * Tests the constructor's ability to correctly initialize the repository
     * with a persistence mechanism and an optional resource factory.
     */
    public function testConstructorInitializesDependencies(): void
    {
        $this->assertSame($this->persistence_mock, $this->sut->getPersistence());
    }


    /**
     * Tests creating new ID
     */
    public function testIdCreation(): void
    {
        $new_id = $this->sut->getNextId();
        $this->assertIsString($new_id);
    }

    /**
     * Tests correct return type
     */
    public function testGetMethodWithResult(): void
    {
        $id = $this->sample_record['id'];
        $entity = $this->sut->get($id);
        $this->assertIsArray($entity);
    }

    /**
     * Tests handling of \OutOfBoundsException for find method when no result is found.
     */
    public function testExceptionOnGetMethodWithoutResult(): void
    {
        $sut = new Repository(new NoPersistence());

        $this->expectException(\OutOfBoundsException::class);
        $sut->get('foo');

        $this->expectException(\OutOfBoundsException::class);
        $sut->get(0);
    }



    public static function provideRepositoryAndPersistencesWithCriteria(): array
    {
        $repo = new Repository(new InMemoryPersistence([
            ['some' => 'criteria', 'color' => 'blue'],
            ['some' => 'criteria', 'color' => 'orange'],
            ['some' => 'other', 'color' => 'red'],
        ]));

        $empty_repo = new Repository(new NoPersistence());

        return [
            'Empty repo with "NoPersistence"'            => [ $empty_repo, ['color' => 'yellow'], 0 ],
            'Look for color that does not exist in repo' => [ $repo,       ['color' => 'yellow'], 0 ],
            'Should return only 1 item for unique value' => [ $repo,       ['some' => 'criteria', 'color' => 'orange'], 1 ],
            'Should return 2 matching items'             => [ $repo,       ['some' => 'criteria'],                      2 ],
        ];
    }



    /**
     * Tests the findAll method.
     *
     * @dataProvider provideRepositoryAndPersistencesWithCriteria
     */
    public function testFindAll($sut, $criteria, $expected_count): void
    {
        $result = $sut->findAll();
        $this->assertIsIterable($result);
    }


    /**
     * Tests the findBy method.
     *
     * @dataProvider provideRepositoryAndPersistencesWithCriteria
     */
    public function testFindMethod($sut, $criteria, $expected_count): void
    {
        $collection = $sut->findBy($criteria);
        $this->assertIsIterable($collection);
        $this->assertEquals($expected_count, count($collection));
    }


    /**
     * Tests the findBy method and expect at least one result.
     *
     * @dataProvider provideRepositoryAndPersistencesWithCriteria
     */
    public function testFindOneByMethod($sut, $criteria, $expectable_count): void
    {
        $record = $sut->findOneBy($criteria);

        if ($expectable_count > 0) {
            $this->assertIsArray($record);
        } else {
            $this->assertNull($record);
        }

    }

    /**
     * Tests the save method.
     */
    public function testSaveMethod(): void
    {
        $id = $this->sample_record['id'];
        $persistence = new InMemoryPersistence();

        $sut = new Repository($persistence);
        $result = $sut->save($this->sample_record);
        $this->assertIsBool($result);

        $created = $persistence->read($id);
        $this->assertIsArray($created);
    }

    /**
     * Tests the delete method.
     */
    public function testDeleteMethod(): void
    {
        $id = $this->sample_record['id'];
        $persistence = new InMemoryPersistence([$id => $this->sample_record]);

        $sut = new Repository($persistence);

        $result = $sut->delete($this->sample_record);
        $this->assertTrue($result);

        $this->expectException(\OutOfBoundsException::class);
        $persistence->read($id);

    }



}
