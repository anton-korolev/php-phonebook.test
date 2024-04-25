<?php

declare(strict_types=1);

namespace common;

use Exception;

class AppException extends Exception
{
    /**
     * @return string the user-friendly name of this exception.
     */
    public function getName()
    {
        return 'Exception';
    }
}
