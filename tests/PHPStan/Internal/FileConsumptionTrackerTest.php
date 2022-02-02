<?php declare(strict_types = 1);

namespace PHPStan\Internal;

use Generator;
use InvalidArgumentException;
use PHPStan\ShouldNotHappenException;
use PHPUnit\Framework\TestCase;

class FileConsumptionTrackerTest extends TestCase
{

	public function testCreateFromArrayWillCreateValidObject(): void
	{
		$expectedData = [
			'file' => 'phpunit',
			'timeConsumed' => 1.2345,
			'memoryConsumed' => 1,
			'totalMemoryConsumed' => 2,
		];

		$object = FileConsumptionTracker::createFromArray($expectedData);

		$this->assertEquals($expectedData['file'], $object->getFile());
		$this->assertEquals($expectedData['timeConsumed'], $object->getTimeConsumed());
		$this->assertEquals($expectedData['memoryConsumed'], $object->getMemoryConsumed());
		$this->assertEquals($expectedData['totalMemoryConsumed'], $object->getTotalMemoryConsumed());
	}

	/**
	 * @param mixed[] $data
	 * @dataProvider dataCreateFromArrayWillThrowOnInvalidStructure
	 */
	public function testCreateFromArrayWillThrowOnInvalidStructure(array $data): void
	{
		$this->expectException(ShouldNotHappenException::class);

		FileConsumptionTracker::createFromArray($data);
	}

	public function dataCreateFromArrayWillThrowOnInvalidStructure(): Generator
	{
		yield 'missing file' => [[
			'timeConsumed' => 1.23,
			'memoryConsumed' => 2,
			'totalMemoryConsumed' => 3,
		]];

		yield 'missing time consumed' => [[
			'file' => 'phpunit',
			'memoryConsumed' => 2,
			'totalMemoryConsumed' => 3,
		]];

		yield 'missing memory consumed' => [[
			'file' => 'phpunit',
			'timeConsumed' => 1,
			'totalMemoryConsumed' => 3,
		]];

		yield 'missing total memory consumed' => [[
			'file' => 'phpunit',
			'timeConsumed' => 1,
			'memoryConsumed' => 2,
		]];
	}

	/**
	 * @param mixed[] $data
	 * @dataProvider dataCreateFromArrayWillThrowOnInvalidData
	 */
	public function testCreateFromArrayWillThrowOnInvalidData(array $data): void
	{
		$this->expectException(InvalidArgumentException::class);

		FileConsumptionTracker::createFromArray($data);
	}

	public function dataCreateFromArrayWillThrowOnInvalidData(): Generator
	{
		yield 'file is no string' => [[
			'file' => 0,
			'timeConsumed' => 1,
			'memoryConsumed' => 2,
			'totalMemoryConsumed' => 3,
		]];

		yield 'time consumed is no float' => [[
			'file' => 'phpunit',
			'timeConsumed' => '1.23',
			'memoryConsumed' => 2,
			'totalMemoryConsumed' => 3,
		]];

		yield 'memory consumed is no int' => [[
			'file' => 0,
			'timeConsumed' => 1,
			'memoryConsumed' => '2',
			'totalMemoryConsumed' => 3,
		]];

		yield 'total memory is no int' => [[
			'file' => 0,
			'timeConsumed' => 1,
			'memoryConsumed' => 2,
			'totalMemoryConsumed' => '3',
		]];
	}

}
