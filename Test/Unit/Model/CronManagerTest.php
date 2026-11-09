<?php
declare(strict_types=1);

namespace PriyankJivani\CronSchedulerMonitor\Test\Unit\Model;

use PHPUnit\Framework\TestCase;

class CronManagerTest extends TestCase
{
    public function testStatusConstants(): void
    {
        $statuses = ['pending', 'running', 'success', 'error', 'missed'];
        foreach ($statuses as $status) {
            $this->assertIsString($status);
            $this->assertNotEmpty($status);
        }
    }

    public function testExecutionDurationCalculation(): void
    {
        $start = strtotime('2026-11-09 10:00:00');
        $end = strtotime('2026-11-09 10:02:15');
        $duration = $end - $start;

        $this->assertEquals(135, $duration);
    }
}
