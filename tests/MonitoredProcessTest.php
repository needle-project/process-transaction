<?php
namespace NeedleProject\Transaction;

use PHPUnit\Framework\TestCase;

class MonitoredProcessTest extends TestCase
{
    /**
     * Test that a process called with debug will return a Monitored Process
     */
    public function testConstruct()
    {
        $process = Process::createProcess(
            function () {
            },
            function () {
            },
            null,
            true
        );
        $this->assertInstanceOf(MonitoredProcess::class, $process);
    }

    /**
     * Test duration
     */
    public function testExecutionDuration()
    {
        $process = Process::createProcess(
            function () {
                sleep(1);
            },
            function () {
            },
            null,
            true
        );
        $process->execute();
        $this->assertEquals(1, round($process->executionDuration));
    }

    /**
     * Test duration
     */
    public function testRollbackDuration()
    {
        $process = Process::createProcess(
            function () {

            },
            function () {
                sleep(1);
            },
            null,
            true
        );
        $process->execute();
        $process->rollBack();
        $this->assertEquals(1, round($process->rollbackDuration));
    }
}
