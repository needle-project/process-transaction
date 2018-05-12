<?php
namespace NeedleProject\Transaction;

use PHPUnit\Framework\TestCase;

class ProcessTest extends TestCase
{
    /**
     * Test that a process can be build
     */
    public function testConstruct()
    {
        $process = Process::createProcess(
            function () {
            },
            function () {
            },
            'Foo'
        );

        $this->assertInstanceOf(Process::class, $process);
    }

    /**
     * Test that a process can be build without a process name
     */
    public function testNameConstruct()
    {
        $process = Process::createProcess(
            function () {
            },
            function () {
            }
        );
        $this->assertNotNull($process->getName());
    }

    /**
     * Test that a process passed-on data are correct
     */
    public function testProcessName()
    {
        $process = Process::createProcess(
            function () {
            },
            function () {
            },
            'Foo'
        );

        $this->assertEquals('Foo', $process->getName());
    }

    /**
     * Test that a process passed-on data are correct
     */
    public function testExecute()
    {
        $data = 'foo';
        $process = Process::createProcess(
            function () use ($data) {
                return $data;
            },
            function () {
            },
            'Foo'
        );

        $this->assertEquals('foo', $process->execute()->getResult());
    }

    /**
     * Test that a process passed-on data are correct
     */
    public function testRollback()
    {
        $data = 'bar';
        $process = Process::createProcess(
            function () {
            },
            function () use ($data) {
                return $data;
            },
            'Foo'
        );

        $process->execute();

        $this->assertEquals('bar', $process->rollback()->getResult());
    }

    /**
     * Test that a process passed-on data are correct
     */
    public function testExecuted()
    {
        $process = Process::createProcess(
            function () {
            },
            function () {
            },
            'Foo'
        );

        $process->execute();

        $this->assertTrue($process->hasExecuted());
    }

    /**
     * Test that a process passed-on data are correct
     */
    public function testNotExecuted()
    {
        $process = Process::createProcess(
            function () {
            },
            function () {
            },
            'Foo'
        );

        $this->assertFalse($process->hasExecuted());
    }

    /**
     * Test that a process passed-on data are correct
     */
    public function testUnExecutableRollback()
    {
        $process = Process::createProcess(
            function () {
            },
            function () {
            },
            'Foo'
        );

        $this->expectException(\RuntimeException::class);

        $process->rollback();
    }
}
