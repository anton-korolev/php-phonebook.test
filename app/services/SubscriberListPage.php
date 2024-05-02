<?php

declare(strict_types=1);

namespace services;

class SubscriberListPage
{
    public function __construct(
        public readonly int $currentPage,
        public readonly int $pageCount,
        public readonly array $subscribers,
    ) {
    }
}
