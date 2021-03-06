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

$charge = new \NeedleProject\Transaction\Process(
    function () use ($paymentService) {
        return $paymentService->chargeMoney();
    },
    function () use ($paymentService) {
        return $paymentService->refund();
    },
    'Payment Actions'
);

$reserveStock = new \NeedleProject\Transaction\Process(
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
echo $charge->getExecutionResult() . "\n";
echo $reserveStock->getRollBackResult() . "\n";
```
