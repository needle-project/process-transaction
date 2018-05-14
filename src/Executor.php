<?php
namespace NeedleProject\Transaction;

/**
 * Class Executor
 *
 * @package NeedleProject\Transaction
 * @author  Adrian Tilita <adrian@tilita.ro>
 */
class Executor
{
    /**
     * @var \ArrayIterator|null
     */
    private $processList = null;

    /**
     * Executor constructor.
     */
    public function __construct()
    {
        $this->processList = new \ArrayIterator();
    }

    /**
     * @param ProcessInterface $process
     */
    public function addProcess(ProcessInterface $process)
    {
        $this->processList->append($process);
    }

    /**
     * Execute a set of processes
     */
    public function execute()
    {
        $this->processList->rewind();
        while ($this->processList->valid()) {
            /** @var ProcessInterface $currentProcess */
            $currentProcess = $this->processList->current();

            $executionResult = $currentProcess->execute();
            if ($currentProcess instanceof ExecutionResultInterface) {
                $currentProcess->setExecutionResult($executionResult);
            }
            $currentProcess->markAsExecuted();

            $this->processList->next();
        }
    }

    /**
     * Rollback in reverse order
     */
    public function rollBack()
    {
        $maxOffset = $this->processList->count() - 1;

        for ($i = $maxOffset; $i >= 0; $i--) {
            /** @var ProcessInterface $currentProcess */
            $currentProcess = $this->processList->offsetGet($i);
            // Exclude rolling back processes that has not been executed
            if ($currentProcess->hasExecuted() === false) {
                continue;
            }
            $rollBackResult = $currentProcess->rollBack();
            if ($currentProcess instanceof RollBackResultInterface) {
                $currentProcess->setRollBackResult($rollBackResult);
            }
        }
    }
}
