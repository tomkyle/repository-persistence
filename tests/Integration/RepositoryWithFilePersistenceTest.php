<?php

/**
 * This file is part of tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tests\Integration;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use tomkyle\RepositoryPersistence\Repositories\Repository;
use tomkyle\RepositoryPersistence\Persistence\FilePersistence;
use tomkyle\RepositoryPersistence\Persistence\YamlFilePersistence;
use tomkyle\RepositoryPersistence\Persistence\JsonFilePersistence;
use tomkyle\RepositoryPersistence\Persistence\InMemoryPersistence;
use tomkyle\RepositoryPersistence\Persistence\NoPersistence;
use tomkyle\RepositoryPersistence\Persistence\PersistenceChain;
use tomkyle\RepositoryPersistence\Persistence\FrontmatterFilePersistence;
use Nette\Utils\FileSystem;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class RepositoryWithFilePersistenceTest extends TestCase
{
    /**
     * Fixtures base directory
     */
    public string $fixtures_path;


    /**
     * Actual fixtures directory (subdir of base directory)
     */
    public static $fixtures_dir = 'json';


    /**
     * Holds temporary fixtures directores
     */
    public static $temp_fixtures_dirs = [];


    #[DataProvider('provideFilePersistences')]
    public function testFindItemByItsId($persistence): void
    {
        $repo = new Repository($persistence);

        // These will usually fail
        if ($persistence instanceof NoPersistence || $persistence instanceof InMemoryPersistence) {
            $this->expectException(\OutOfBoundsException::class);
        }

        // We know that from fixture file
        $person = $repo->get('john-doe');
        $this->assertIsArray($person);
        $this->assertEquals($person['name'], 'John Doe');

    }


    #[DataProvider('provideFilePersistences')]
    public function testFindAllItems($persistence): void
    {
        $repo = new Repository($persistence);
        $all_persons = $repo->findAll();

        // print_r($all_persons);

        $this->assertIsArray($all_persons);
    }


    #[DataProvider('provideFilePersistences')]
    public function testUpdateItem($persistence): void
    {
        $repo = new Repository($persistence);

        // ID must exist in fixtures
        $id = 'john-doe';
        $mock_entity = ['id' => $id, 'name' => 'Test', 'value' => '123'];

        // Seed empty InMemoryPersistence
        if ($persistence instanceof InMemoryPersistence) {
            $persistence->setRecords([ $id => $mock_entity ]);
        }

        // The item "original"
        if ($persistence instanceof NoPersistence) {
            try {
                $repo->get($id);
            } catch (\OutOfBoundsException) {
                $found = $mock_entity;
            }
        } else {
            $found = $repo->get($id);
            $found['id'] ??= $id;
        }

        // Update item
        $new_name = uniqid();
        $found['name'] = $new_name;
        $saved = $repo->save($found);
        $this->assertTrue($saved);

        // Check if item was actually changed
        if ($persistence instanceof NoPersistence) {
            $this->expectException(\OutOfBoundsException::class);
        }

        $updated = $repo->get($id);
        $this->assertEquals($updated['name'], $new_name);

    }


    #[DataProvider('provideFilePersistences')]
    public function testDeleteItem($persistence): void
    {
        $repo = new Repository($persistence);

        // Must exist in fixtures
        $id = 'john-doe';
        $mock_entity = ['id' => $id, 'name' => 'Test', 'value' => '123'];

        // Seed empty InMemoryPersistence
        if ($persistence instanceof InMemoryPersistence) {
            $persistence->setRecords([ $id => $mock_entity ]);
        }

        // Check if saved entity can be found
        if ($persistence instanceof NoPersistence) {
            try {
                $repo->get($id);
            } catch (\OutOfBoundsException) {
                $found = $mock_entity;
            }
        } else {
            $found = $repo->get($id);
            $found = array_merge($found, ['id' => $id]);
        }

        // Now delete …
        $result = $repo->delete($found);
        $this->assertTrue($result);

        // Must throw Eception entity must not be found
        $this->expectException(\OutOfBoundsException::class);
        $repo->get($id);
    }



    public static function provideFilePersistences(): array
    {
        $jp = new JsonFilePersistence(static::buildFixturesPath('json'));
        $fjp = new JsonFilePersistence(static::buildFixturesPath('frontmatter-json'));

        $yp = new YamlFilePersistence(static::buildFixturesPath('yaml'));
        $fyp = new YamlFilePersistence(static::buildFixturesPath('frontmatter-yaml'));

        return ['JsonFilePersistence' => [ $jp ], 'YamlFilePersistence' => [ $yp ], 'Frontmatter + JsonFilePersistence' => [ new FrontmatterFilePersistence($fjp) ], 'Frontmatter + YamlFilePersistence' => [ new FrontmatterFilePersistence($fyp) ], 'NoPersistence' => [ new NoPersistence() ], 'InMemoryPersistence' => [ new InMemoryPersistence() ], 'Chained JsonFilePersistence + YamlFilePersistence' => [ new PersistenceChain([
            new JsonFilePersistence(static::buildFixturesPath('json')),
            new YamlFilePersistence(static::buildFixturesPath('yaml')),
            new FrontmatterFilePersistence(new JsonFilePersistence(static::buildFixturesPath('json'))),
            new FrontmatterFilePersistence(new YamlFilePersistence(static::buildFixturesPath('yaml'))),
        ])]];
    }


    /**
     * @see https://github.com/spatie/temporary-directory
     */
    public static function buildFixturesPath(string $dir): string
    {
        $base_dir = implode(\DIRECTORY_SEPARATOR, [
            dirname(__DIR__, 2),
            'tests',
            'fixtures',
            $dir,
        ]);

        // Copy fixtures to a temp directory which will be deleted afterwards
        //
        $temp_dir = (new TemporaryDirectory())->create()->deleteWhenDestroyed();
        $temp_dir_path = $temp_dir->path();

        FileSystem::copy($base_dir, $temp_dir_path, overwrite: true);

        $id = uniqid();
        static::$temp_fixtures_dirs[$id] = $temp_dir;

        // echo "ID $id: Dir $dir - $temp_dir_path", PHP_EOL;
        return $temp_dir_path;
    }

}
