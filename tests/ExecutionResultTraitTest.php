<?php
namespace NeedleProject\Transaction;

use PHPUnit\Framework\TestCase;

class ExecutionResultTraitTest extends TestCase
{
    public function testSetResult()
    {
        $concrete = new class {
            use ExecutionResultTrait;
        };

        $concrete->setExecutionResult('Foo');

        $this->assertEquals('Foo', $concrete->getExecutionResult());
    }
}
