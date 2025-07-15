<?php


require __DIR__ . '/vendor/autoload.php';

use JscorpTech\IYB\Enums\TransactionStatus;
use JscorpTech\IYB\Services\Api;

$iyb = new Api(
    "./keys/MCertResp.pem",
    "./keys/ima.key",
);

$trans_id = $iyb->create_transaction(1000);
print_r($iyb->check_transaction_status($trans_id) == TransactionStatus::CREATED);
