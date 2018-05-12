[![Build Status](https://travis-ci.org/needle-project/common.svg?branch=master)](https://travis-ci.org/needle-project/common)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/needle-project/process-transaction/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/needle-project/process-transaction/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/needle-project/process-transaction/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/needle-project/process-transaction/?branch=master)

# Transaction Process

This library helps you run your process with capabilities of rollback in case one of the processes fails! (Similar to Database Transactions).

## 1. Install
```
composer require needle-project/transactional
```

## 2. Usage
A simple usage:
```php
<?php
require_once 'vendor/autoload.php';

$paymentService = new class {
    public function chargeMoney() {
        // your logic
        echo "Customer has been charge!\n";
    }
    public function refund() {
        echo "Customer has been refunded!\n";
    }
};

$stockReservationService = new class {
    public function reserveStock() {
        echo "Could not reserve stock!\n";
        throw new \Exception("The trigger of failed process");
    }
};

$charge = \NeedleProject\Transaction\Process::createProcess(
    function () use ($paymentService) {
        return $paymentService->chargeMoney();
    },
    function () use ($paymentService) {
        return $paymentService->refund();
    },
    'Payment Actions'
);

$reserveStock = \NeedleProject\Transaction\Process::createProcess(
    function () use ($stockReservationService) {
        return $stockReservationService->reserveStock();
    },
    function () {
        echo "This will not be executed!\n";
    },
    "Stock Reserve"
);

// Processing an order
$executor = new \NeedleProject\Transaction\Executor();
$executor->addProcess($charge);
$executor->addProcess($reserveStock);

// Executing the processes
try {
    $executor->execute();
} catch (\Exception $e) {
    $executor->rollBack();
}

// Getting the process result
echo $charge->getResult() . "\n";
echo $reserveStock->getResult() . "\n";
```


Timing processes:
> Note tha this is just as a debug process, metrics should be done
> using other methods like statsD
```php
<?php
require_once 'vendor/autoload.php';

$paymentService = new class {
    public function chargeMoney() {
        // your logic
        echo "Customer has been charge!\n";
    }
    public function refund() {
        echo "Customer has been refunded!\n";
    }
};

$charge = \NeedleProject\Transaction\Process::createProcess(
    function () use ($paymentService) {
        usleep(50000);
        return $paymentService->chargeMoney();
    },
    function () use ($paymentService) {
        return $paymentService->refund();
    },
    'Payment Actions',
    true // debug mode
);

// Processing an order
$executor = new \NeedleProject\Transaction\Executor();
$executor->addProcess($charge);

try {
    $executor->execute();
} catch (\Exception $e) {
    $executor->rollBack();
}

echo sprintf("%s took %.2f sec.", $charge->getName(), $charge->executionDuration);
```
> The same can be done using `$charge->rollbackDuration` for getting execution time of a rollback process

Using with a callable:
```php
<?php
require_once 'vendor/autoload.php';

class MyService {
    public function rollbackProcess()
    {
        // your business logic for undo-ing the project
    }

    public function runSomeProcess()
    {
        // your business logic
    }
}

$myService = new MyService();
$process = \NeedleProject\Transaction\Process::createProcess(
    Closure::fromCallable([$myService, 'runSomeProcess']),
    Closure::fromCallable([$myService, 'rollbackProcess'])
);

$process->execute();
$process->rollBack();
```

Using a callable from inside:
```php
<?php
require_once 'vendor/autoload.php';

use NeedleProject\Transaction\Process;
use NeedleProject\Transaction\Executor;

class ExampleOrderProcess
{
    private $order;

    public function placeOrder(array $orderDetails)
    {
        $this->order = $orderDetails;

        $executor = new Executor();
        $executor->addProcess(
            Process::createProcess(
                Closure::fromCallable([$this, 'chargeOrder']),
                Closure::fromCallable([$this, 'refundOrder'])
            ),
            'Charge Process'
        );
        $executor->addProcess(
            Process::createProcess(
                Closure::fromCallable([$this, 'reserveStock']),
                Closure::fromCallable([$this, 'revertStockReservation'])
            ),
            'Reserve Stock Process'
        );

        try {
            $executor->execute();
        } catch (\Exception $e) {
            $executor->rollBack();
        }
    }

    public function chargeOrder()
    {
        echo sprintf("Charging order %d\n", (int)$this->order['id']);
        $this->order['charged'] = true;
    }

    public function refundOrder()
    {
        // refund logic
    }

    public function reserveStock()
    {
        echo sprintf("Reserving stock for order %d\n", (int)$this->order['id']);
        $this->order['stock_reserved'] = true;
    }

    public function revertStockReservation()
    {
        // your logic for reverting stock reservation
    }

    public function getOrder()
    {
        return $this->order;
    }
}
$example = new ExampleOrderProcess();
$example->placeOrder(['id' => 1]);

print_r($example->getOrder());
return;
```

Custom processes:
```php
<?php
require_once 'vendor/autoload.php';

use NeedleProject\Transaction\Executor;
use NeedleProject\Transaction\ProcessInterface;

class CustomProcess implements ProcessInterface
{
    public function execute()
    {
        echo "This is my own process that just to implements an interface!\n";
    }

    public function rollBack()
    {
        // TODO: Implement rollBack() method.
    }
}
$executor = new Executor();
$executor->addProcess(new CustomProcess());

$executor->execute();
```