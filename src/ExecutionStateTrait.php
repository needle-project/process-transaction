<?php
namespace NeedleProject\Transaction;

/**
 * Trait ExecutionStateTrait
 *
 * @package NeedleProject\Transaction
 * @author  Adrian Tilita <adrian@tilita.ro>
 */
trait ExecutionStateTrait
{
    /**
     * @var bool
     */
    private $executionStatus = false;

    /**
     * Retrieve the execution state of the process
     * @return bool
     */
    public function hasExecuted(): bool
    {
        return $this->executionStatus;
    }

    /**
     * Mark the process as executed
     */
    public function markAsExecuted()
    {
        $this->executionStatus = true;
    }
}
