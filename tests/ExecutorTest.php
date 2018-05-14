<?php
namespace NeedleProject\Transaction;

use PHPUnit\Framework\TestCase;

class ExecutorTest extends TestCase
{
    public function testExecuteTwoProcess()
    {
        $executor = new Executor();

        $firstProcess = new Process(
            function () {
                return "First process execute!";
            },
            function () {
            },
            "FirstProcess"
        );

        $secondProcess = new Process(
            function () {
                return "Second process execute!";
            },
            function () {
            },
            "SecondProcess"
        );

        $executor->addProcess($firstProcess);
        $executor->addProcess($secondProcess);

        $this->assertFalse($firstProcess->hasExecuted());
        $this->assertFalse($secondProcess->hasExecuted());

        $executor->execute();

        $this->assertTrue($firstProcess->hasExecuted());
        $this->assertTrue($secondProcess->hasExecuted());
    }

    public function testExecutionStopsIfOneFails()
    {
        $executor = new Executor();

        $firstProcess = new Process(
            function () {
                throw new \Exception("Cannot process!");
            },
            function () {
            }
        );

        $secondProcess = new Process(
            function () {
            },
            function () {
            }
        );

        $executor->addProcess($firstProcess);
        $executor->addProcess($secondProcess);

        try {
            $executor->execute();
        } catch (\Exception $e) {
            // do nothing
        }
        $this->assertFalse($secondProcess->hasExecuted());
    }

    public function testExecuteOrder()
    {
        $items = [];

        $executor = new Executor();
        $executor->addProcess(
            new Process(
                function () use (&$items) {
                    $items[] = 'Foo';
                },
                function () {
                }
            )
        );
        $executor->addProcess(
            new Process(
                function () use (&$items) {
                    $items[] = 'Bar';
                },
                function () {
                }
            )
        );
        $executor->addProcess(
            new Process(
                function () use (&$items) {
                    $items[] = 'Baz';
                },
                function () {
                }
            )
        );
        $executor->execute();

        $this->assertEquals(['Foo', 'Bar', 'Baz'], $items);
    }

    public function testNoRollbackForOnlyForExecutedProcesses()
    {
        $executor = new Executor();

        $firstProcess =  new Process(
            function () {
            },
            function () {
            }
        );
        $secondProcess = new Process(
            function () {
                throw new \RuntimeException("Could not execute!");
            },
            function () {
                throw new \Exception("Rollback should not execute!");
            }
        );
        $thirdProcess =  new Process(
            function () {
            },
            function () {
                throw new \Exception("Rollback should not execute!");
            }
        );

        $executor->addProcess($firstProcess);
        $executor->addProcess($secondProcess);
        $executor->addProcess($thirdProcess);

        try {
            $executor->execute();
        } catch (\RuntimeException $e) {
            $executor->rollBack();
        }

        $this->assertFalse($thirdProcess->hasExecuted());
    }

    public function testNotExecutionResultAware()
    {
        $executor = new Executor();

        $firstProcess =  new class(
            function () {
            },
            function () {
            }
        ) implements ProcessInterface {

            use ExecutionStateTrait;

            public function execute()
            {
                // TODO: Implement execute() method.
            }
            public function rollBack()
            {
                // TODO: Implement rollBack() method.
            }

            public function setExecutionResult($executionResult)
            {
                throw new \Exception(
                    "This should not be called as in the current process is not of " .
                    "ExecutionResultInterface type!"
                );
            }
        };
        $executor->addProcess($firstProcess);

        $executor->execute();
        $this->assertTrue($firstProcess->hasExecuted());
    }

    public function testNotRollBackResultAware()
    {
        $executor = new Executor();

        $firstProcess =  new class(
            function () {
            },
            function () {
            }
        ) implements ProcessInterface {

            use ExecutionStateTrait;

            public function execute()
            {
                // TODO: Implement execute() method.
            }
            public function rollBack()
            {
                // TODO: Implement rollBack() method.
            }

            public function setRollBackResult($rollBackResult)
            {
                throw new \Exception(
                    "This should not be called as in the current process is not of " .
                    "RollBackResultInterface type!"
                );
            }
        };
        $executor->addProcess($firstProcess);

        $executor->rollBack();
        $this->assertFalse($firstProcess->hasExecuted());
    }
}
