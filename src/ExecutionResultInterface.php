<?php
namespace NeedleProject\Transaction;

/**
 * Interface ExecutionResultInterface
 *
 * @package NeedleProject\Transaction
 * @author  Adrian Tilita <adrian@tilita.ro>
 */
interface ExecutionResultInterface
{
    /**
     * Set the execution result of the process
     * @param $executionResult
     */
    public function setExecutionResult($executionResult);

    /**
     * Retrieve the execution result
     * @return mixed
     */
    public function getExecutionResult();
}
