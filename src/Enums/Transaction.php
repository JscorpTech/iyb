<?php

namespace Jscorptech\IYB\Enums;

enum TransactionStatus: string
{
    case CREATED = 'created';
    case OK = 'ok';
    case FAILED = 'failed';
    case CANCELED = 'canceled';
}
