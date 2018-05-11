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
     * @param Process $process
     */
    public function addProcess(Process $process)
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
            $this->processList->current()->execute();
            $this->processList->next();
        }
    }

    /**
     * Rollback in reverse order
     */
    public function rollback()
    {
        $maxOffset = $this->processList->count() - 1;

        for ($i = $maxOffset; $i >= 0; $i--) {
            /** @var Process $current */
            $current = $this->processList->offsetGet($i);
            // Exclude rolling back processes that has not been executed
            if ($current->hasExecuted() === false) {

                continue;
            }
            $this->processList->offsetGet($i)->rollback();
        }
    }
}
