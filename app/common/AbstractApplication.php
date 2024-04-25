<?php

declare(strict_types=1);

namespace common;

abstract class AbstractApplication
{
    protected function __construct(
        public readonly string $basePath,
    ) {
    }
}
