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
}
