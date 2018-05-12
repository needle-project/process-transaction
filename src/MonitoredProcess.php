<?php
namespace NeedleProject\Transaction;

/**
 * Class MonitoredProcess
 *
 * @package NeedleProject\Transaction
 * @author  Adrian Tilita <adrian@tilita.ro>
 */
class MonitoredProcess extends Process
{
    /**
     * @var float Total duration for a process to be executed!
     */
    public $executionDuration = 0;

    /**
     * @var float Total duration for a process to be roll-back!
     */
    public $rollbackDuration = 0;

    /**
     * Execute the process
     */
    public function execute()
    {
        $time = microtime(true);
        parent::execute();
        $this->executionDuration = microtime(true) - $time;
        return $this;
    }

    /**
     * Rollback action
     */
    public function rollBack()
    {
        $time = microtime(true);
        parent::rollback();
        $this->rollbackDuration = microtime(true) - $time;
        return $this;
    }
}
