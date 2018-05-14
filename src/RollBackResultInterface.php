<?php
namespace NeedleProject\Transaction;

/**
 * Interface RollBackResultInterface
 *
 * @package NeedleProject\Transaction
 * @author  Adrian Tilita <adrian@tilita.ro>
 */
interface RollBackResultInterface
{
    /**
     * Set the roll-back result of the process
     * @param $rollBackResult
     */
    public function setRollBackResult($rollBackResult);

    /**
     * Retrieve the rollback result
     * @return mixed
     */
    public function getRollBackResult();
}
