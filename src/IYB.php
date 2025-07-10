<?php

namespace Jscorptech\IYB;

use JscorpTech\IYB\Enums\TransactionStatus;
use JscorpTech\IYB\Services\Api;

class IYB
{
    public Api $api;
    function __construct(string $certificate, string $certificate_key)
    {
        $this->api = new Api(
            $certificate,
            $certificate_key,
        );
    }

    function create_transaction(int $amount)
    {
        return $this->api->create_transaction(10000, description: "Ishladi");
    }

    public function check_transaction(string $trans_id): TransactionStatus{
        return $this->api->check_transaction_status($trans_id);
    }
}
