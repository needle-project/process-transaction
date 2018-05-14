<?php
namespace NeedleProject\Transaction;

use PHPUnit\Framework\TestCase;

class RollBackResultTraitTest extends TestCase
{
    public function testSetResult()
    {
        $concrete = new class {
            use RollBackResultTrait;
        };

        $concrete->setRollBackResult('Foo');

        $this->assertEquals('Foo', $concrete->getRollBackResult());
    }
}
