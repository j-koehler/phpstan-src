<?php declare(strict_types = 1);

namespace PHPStan\Internal;

use RuntimeException;
use function memory_get_peak_usage;
use function microtime;

class FileConsumptionTracking
{

	private int $memoryConsumedAtStart;

	private float $processingStartedAt;

	private float $timeConsumed;

	private int $memoryConsumed;

	private int $totalMemoryConsumed;

	private bool $trackingIsRunning;

	public function __construct(private string $file)
	{
		$this->processingStartedAt = microtime(true);
		$this->memoryConsumedAtStart = memory_get_peak_usage(true);
		$this->trackingIsRunning = true;
	}

	public function getFile(): string
	{
		return $this->file;
	}

	public function getTimeConsumed(): float
	{
		if ($this->trackingIsRunning) {
			throw new RuntimeException('can not return data when data collection is running, call stopTracking first');
		}

		return $this->timeConsumed;
	}

	public function getMemoryConsumed(): int
	{
		if ($this->trackingIsRunning) {
			throw new RuntimeException('can not return data when data collection is running, call stopTracking first');
		}

		return $this->memoryConsumed;
	}

	public function getTotalMemoryConsumed(): int
	{
		if ($this->trackingIsRunning) {
			throw new RuntimeException('can not return data when data collection is running, call stopTracking first');
		}

		return $this->totalMemoryConsumed;
	}

	public function stopTracking(): void
	{
		$this->totalMemoryConsumed = memory_get_peak_usage(true);
		$this->memoryConsumed = $this->totalMemoryConsumed - $this->memoryConsumedAtStart;
		$this->timeConsumed = microtime(true) - $this->processingStartedAt;

		$this->trackingIsRunning = false;
	}

	/**
	 * @return array<string, array{"totalMemoryConsumed": int, "memoryConsumed": int, "timeConsumed": float}>
	 */
	public function toArray(): array
	{
		return [
			$this->file => [
				'totalMemoryConsumed' => $this->getTotalMemoryConsumed(),
				'memoryConsumed' => $this->getMemoryConsumed(),
				'timeConsumed' => $this->getTimeConsumed(),
			],
		];
	}

}
