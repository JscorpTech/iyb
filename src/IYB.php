<?php

namespace Jscorptech\IYB;

use JscorpTech\IYB\Services\API;

class IYB
{
    public API $api;
    function __construct()
    {
        $this->api = new API();
    }

    function create_transaction(int $amount)
    {
        $res = $this->api->create_transaction(10000, description: "Ishladi");
        print_r($res);
    }
}
