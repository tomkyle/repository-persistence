<?php

/**
 * tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tests\Unit;

use PHPUnit\Framework\TestCase;
use tomkyle\RepositoryPersistence\Repositories\ItemFactory;

class ItemFactoryTest extends TestCase
{
    /**
     * Tests if the ItemFactory pass-through callable returns the input array.
     */
    public function testItemFactoryPassThrough(): void
    {
        $sut = new ItemFactory();

        $inputArray = ['id' => 1, 'name' => 'Test'];

        $result = $sut($inputArray);

        $this->assertEquals($inputArray, $result);
    }
}
