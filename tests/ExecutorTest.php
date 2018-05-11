<?php
namespace NeedleProject\Transaction;

use PHPUnit\Framework\TestCase;

class ExecutorTest extends TestCase
{
    /**
     * Test that 2 processes wil get executed
     */
    public function testExecuteTwoProcess()
    {
        $executor = new Executor();

        $firstProcess = Process::createProcess(function () {
            return "First process execute!";
        }, function () {
        }, "FirstProcess");

        $secondProcess = Process::createProcess(function () {
            return "Second process execute!";
        }, function () {
        }, "SecondProcess");

        $executor->addProcess($firstProcess);
        $executor->addProcess($secondProcess);

        $executor->execute();

        $this->assertEquals('First process execute!', $firstProcess->getResult());
        $this->assertEquals('Second process execute!', $secondProcess->getResult());
    }

    /**
     * Assert that the second process is not executed if the first one fails!
     */
    public function testExecutionStopsIfOneFails()
    {
        $executor = new Executor();

        $firstProcess = Process::createProcess(function () {
            throw new \Exception("Cannot process!");
        }, function () {
            return "Rollback executed!";
        }, "FirstProcess");

        $secondProcess = Process::createProcess(function () {
            return "Second process execute!";
        }, function () {
            return "Should not call rollback!";
        }, "SecondProcess");

        $executor->addProcess($firstProcess);
        $executor->addProcess($secondProcess);

        try {
            $executor->execute();
        } catch (\Exception $e) {
            // do nothing
        }
        $this->assertNull($secondProcess->getResult());
    }

    /**
     * Assert that the second process is not roll-back if it fails!
     */
    public function testNoRollbackForFailedProcess()
    {
        $executor = new Executor();

        $firstProcess = Process::createProcess(function () {
            return "First process executed!";
        }, function () {
            return "Rollback executed!";
        }, "FirstProcess");

        $secondProcess = Process::createProcess(function () {
            throw new \Exception("Cannot process!");
        }, function () {
            return "Should not call rollback!";
        }, "SecondProcess");

        $executor->addProcess($firstProcess);
        $executor->addProcess($secondProcess);

        try {
            $executor->execute();
        } catch (\Exception $e) {
            $executor->rollback();
        }
        $this->assertNull($secondProcess->getResult());
    }

    /**
     * Test that if a process fails, second one does not execute and first one gets roll-backed
     */
    public function testRollback()
    {
        $executor = new Executor();

        $firstProcess = Process::createProcess(function () {
            throw new \Exception("Cannot process!");
        }, function () {
            return "Rollback executed!";
        }, "FirstProcess");

        $secondProcess = Process::createProcess(function () {
            return "Second process execute!";
        }, function () {
            return "Should not return nothing!";
        }, "SecondProcess");

        $executor->addProcess($firstProcess);
        $executor->addProcess($secondProcess);

        try {
            $executor->execute();
        } catch (\Exception $e) {
            $executor->rollback();
        }
        $this->assertNull($firstProcess->getResult());
        $this->assertNull($secondProcess->getResult());
    }

    /**
     * Test that the processes are executed in the desired order
     */
    public function testExecutionOrder()
    {
        $executor = new Executor();

        $order = [];

        $firstProcess = Process::createProcess(function () use (&$order) {
            $order[] = 'Foo';
        }, function () {
        }, "Foo");

        $secondProcess = Process::createProcess(function () use (&$order) {
            $order[] = 'Bar';
        }, function () {
        }, "Bar");

        $executor->addProcess($firstProcess);
        $executor->addProcess($secondProcess);

        $executor->execute();

        $this->assertEquals(['Foo','Bar'], $order);
    }

    /**
     * Test that the processes are executed in the desired order
     */
    public function testRollbackOrder()
    {
        $executor = new Executor();

        $order = [];

        $firstProcess = Process::createProcess(
            function () {
            },
            function () use (&$order) {
                $order[] = 'Foo';
            },
            'Foo'
        );

        $secondProcess = Process::createProcess(
            function () {
            },
            function () use (&$order) {
                $order[] = 'Bar';
            },
            'Bar'
        );

        $thirdProcess = Process::createProcess(
            function () {
            },
            function () use (&$order) {
                $order[] = 'Baz';
            },
            'Baz'
        );

        $executor->addProcess($firstProcess);
        $executor->addProcess($secondProcess);
        $executor->addProcess($thirdProcess);

        $executor->execute();
        $executor->rollback();

        $this->assertEquals(['Baz','Bar', 'Foo'], $order);
    }
}
