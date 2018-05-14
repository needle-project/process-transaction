<?php
namespace NeedleProject\Transaction;

use PHPUnit\Framework\TestCase;

class ProcessTest extends TestCase
{
    public function testConstruct()
    {
        $process = new Process(
            function () {
            },
            function () {
            },
            'Foo'
        );

        $this->assertInstanceOf(Process::class, $process);
    }

    public function testNameConstruct()
    {
        $process = new Process(
            function () {
            },
            function () {
            },
            'Foo'
        );
        $this->assertEquals('Foo', $process->getName());
    }

    public function testEmptyProcessName()
    {
        $execute = function () {
        };
        $rollBack = function () {
        };

        $process = new Process($execute, $rollBack);

        $this->assertEquals(
            spl_object_hash($execute) . '_' . spl_object_hash($rollBack),
            $process->getName()
        );
    }

    public function testExecuteCollaboration()
    {
        // Unfortunately, closures cannot be mocked in PHPUnit so will test the
        // collaboration by implementing the closure
        $executed = false;

        $closureExecuteMock = function () use (&$executed) {
            $executed = true;
        };
        $closureRollBackMock = function () {
            throw new \Exception("Rollback should not be executed!");
        };

        $process = new Process($closureExecuteMock, $closureRollBackMock);

        $this->assertFalse($executed);
        $process->execute();

        $this->assertTrue($executed);
    }

    public function testRollbackCollaboration()
    {
        $rollbackExecuted = false;

        $closureExecuteMock = function () {
        };
        $closureRollBackMock = function () use (&$rollbackExecuted) {
            $rollbackExecuted = true;
        };

        $process = new Process($closureExecuteMock, $closureRollBackMock);

        $this->assertFalse($rollbackExecuted);
        $process->rollBack();

        $this->assertTrue($rollbackExecuted);
    }
}
