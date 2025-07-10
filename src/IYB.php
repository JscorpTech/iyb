<?php

namespace Jscorptech\IYB;

use JscorpTech\IYB\Services\API;

class IYB
{
    public API $api;
    function __construct(string $certificate, string $certificate_key)
    {
        $this->api = new API(
            $certificate,
            $certificate_key,
        );
    }

    function create_transaction(int $amount)
    {
        $res = $this->api->create_transaction(10000, description: "Ishladi");
        print_r($res);
    }
}
