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
    public $executionDuration = null;

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
    public function rollback()
    {
        $time = microtime(true);
        parent::rollback();
        $this->executionDuration = microtime(true) - $time;
        return $this;
    }
}
