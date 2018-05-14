<?php
namespace NeedleProject\Transaction;

/**
 * Class RollBackResultTrait
 *
 * @package NeedleProject\Transaction
 * @author  Adrian Tilita <adrian@tilita.ro>
 */
trait RollBackResultTrait
{
    /**
     * @var mixed
     */
    private $rollBackResult;

    /**
     * @param mixed $rollBackResult
     */
    public function setRollBackResult($rollBackResult)
    {
        $this->rollBackResult = $rollBackResult;
    }

    /**
     * @return mixed
     */
    public function getRollBackResult()
    {
        return $this->rollBackResult;
    }
}
