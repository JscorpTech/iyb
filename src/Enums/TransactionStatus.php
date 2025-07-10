<?php

namespace JscorpTech\IYB\Enums;

enum TransactionStatus
{
    case CREATED;
    case OK;
    case FAILED;
    case CANCELED;
}
