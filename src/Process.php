<?php
namespace NeedleProject\Transaction;

/**
 * Class Process
 *
 * @package NeedleProject\Transaction
 * @author  Adrian Tilita <adrian@tilita.ro>
 */
class Process
{
    /**
     * @var null|\Closure
     */
    private $executeAction = null;

    /**
     * @var null|\Closure
     */
    private $rollbackAction = null;

    /**
     * @var string
     */
    private $processName = null;

    /**
     * @var bool
     */
    private $executed = false;

    /**
     * @var null
     */
    private $processResult = null;

    /**
     * Process constructor.
     *
     * @param \Closure $executeAction
     * @param \Closure $rollbackAction
     * @param string $processName
     */
    protected function __construct(\Closure $executeAction, \Closure $rollbackAction, string $processName = null)
    {
        if (is_null($processName)) {
            $processName = spl_object_hash($executeAction) . '_' . spl_object_hash($rollbackAction);
        }
        $this->executeAction = $executeAction;
        $this->rollbackAction = $rollbackAction;
        $this->processName = $processName;
    }

    /**
     * Create a new Process
     *
     * @param \Closure $executeAction
     * @param \Closure $rollbackAction
     * @param string $processName
     * @param bool $debug
     * @return Process
     */
    public static function createProcess(
        \Closure $executeAction,
        \Closure $rollbackAction,
        string $processName = null,
        bool $debug = false
    ): Process {
        if (true === $debug) {
            return new MonitoredProcess($executeAction, $rollbackAction, $processName);
        }
        return new self($executeAction, $rollbackAction, $processName);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->processName;
    }

    /**
     * Set the process result
     *
     * @param $result
     * @return $this
     */
    protected function putResult($result)
    {
        $this->processResult = $result;
        return $this;
    }

    /**
     * Retrieve the result
     *
     * @return mixed
     */
    public function getResult()
    {
        return $this->processResult;
    }

    /**
     * Execute the process
     */
    public function execute()
    {
        $this->putResult($this->executeAction->__invoke());
        $this->executed = true;
        return $this;
    }

    /**
     * Rollback action
     */
    public function rollback()
    {
        if (false === $this->hasExecuted()) {
            throw new \RuntimeException(
                sprintf("Process %s cannot be roll-backed because was not executed!", $this->getName())
            );
        }

        $this->putResult($this->rollbackAction->__invoke());
        $this->executed = false;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasExecuted(): bool
    {
        return $this->executed;
    }
}
