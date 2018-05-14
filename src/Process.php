<?php
namespace NeedleProject\Transaction;

/**
 * Class Process
 *
 * @package NeedleProject\Transaction
 * @author  Adrian Tilita <adrian@tilita.ro>
 */
class Process implements ProcessInterface, ExecutionResultInterface, RollBackResultInterface
{
    use ExecutionResultTrait;
    use RollBackResultTrait;
    use ExecutionStateTrait;

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
     * Process constructor.
     *
     * @param \Closure $executeAction
     * @param \Closure $rollbackAction
     * @param string $processName
     */
    public function __construct(\Closure $executeAction, \Closure $rollbackAction, string $processName = null)
    {
        if (is_null($processName)) {
            $processName = spl_object_hash($executeAction) . '_' . spl_object_hash($rollbackAction);
        }
        $this->executeAction = $executeAction;
        $this->rollbackAction = $rollbackAction;
        $this->processName = $processName;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->processName;
    }

    /**
     * Execute the process
     */
    public function execute()
    {
        return $this->executeAction->__invoke();
    }

    /**
     * Rollback action
     */
    public function rollBack()
    {
        return $this->rollbackAction->__invoke();
    }
}
