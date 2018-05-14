<?php
namespace NeedleProject\Transaction;

/**
 * Interface ProcessInterface
 *
 * @package NeedleProject\Transaction
 * @author  Adrian Tilita <adrian@tilita.ro>
 */
interface ProcessInterface
{
    /**
     * Execute the process
     * @return mixed
     */
    public function execute();

    /**
     * Roll-back the process
     * @return mixed
     */
    public function rollBack();

    /**
     * Retrieve the execution state of the process
     * @return bool
     */
    public function hasExecuted(): bool;

    /**
     * Mark the process as executed
     */
    public function markAsExecuted();
}
