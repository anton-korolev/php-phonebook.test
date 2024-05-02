<?php

declare(strict_types=1);

namespace common;

use Exception;
use Throwable;

class AppException extends Exception
{
    /**
     * {@inheritdoc}
     */
    public function __construct(string $message, int $code, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string the user-friendly name of this exception.
     */
    public function getName()
    {
        return 'Exception';
    }
}
