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
    $executor->rollback();
}

// Getting the process result
echo $charge->getResult() . "\n";
echo $reserveStock->getResult() . "\n";
```

Timing processes:
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
    $executor->rollback();
}

echo sprintf("%s took %.2f sec.", $charge->getName(), $charge->executionDuration);
```