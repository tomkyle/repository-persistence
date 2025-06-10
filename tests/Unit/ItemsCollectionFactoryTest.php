<?php

/**
 * This file is part of tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tests\Unit;

use PHPUnit\Framework\TestCase;
use tomkyle\RepositoryPersistence\Repositories\ItemsCollectionFactory;

class ItemsCollectionFactoryTest extends TestCase
{
    /**
     * Tests if the ItemsCollectionFactory pass-through callable returns the input array.
     */
    public function testItemsCollectionFactoryPassThrough(): void
    {
        $sut = new ItemsCollectionFactory();

        $inputArray = [
            ['id' => 1, 'name' => 'Test 1'],
            ['id' => 2, 'name' => 'Test 2'],
            ['id' => 3, 'name' => 'Test 3'],
        ];

        $result = $sut($inputArray);

        $this->assertEquals($inputArray, $result);
    }
}
