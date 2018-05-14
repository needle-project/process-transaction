<?php
namespace NeedleProject\Transaction;

use PHPUnit\Framework\TestCase;

class ExecutionStateTraitTest extends TestCase
{
    public function testSetResult()
    {
        $concrete = new class {
            use ExecutionStateTrait;
        };

        $this->assertFalse($concrete->hasExecuted());

        $concrete->markAsExecuted();

        $this->assertTrue($concrete->hasExecuted());
    }
}
