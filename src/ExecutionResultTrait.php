<?php
namespace NeedleProject\Transaction;

/**
 * Trait ExecutionResultTrait
 *
 * @package NeedleProject\Transaction
 * @author  Adrian Tilita <adrian@tilita.ro>
 */
trait ExecutionResultTrait
{
    /**
     * @var mixed
     */
    private $executionResult;

    /**
     * @param mixed $executionResult
     */
    public function setExecutionResult($executionResult)
    {
        $this->executionResult = $executionResult;
    }

    /**
     * @return mixed
     */
    public function getExecutionResult()
    {
        return $this->executionResult;
    }
}
