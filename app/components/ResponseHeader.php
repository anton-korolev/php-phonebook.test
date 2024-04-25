<?php

declare(strict_types=1);

namespace components;

class ResponseHeader
{
    // public bool $isOk = false;
    public int $statusCode = -1;
    public string $errorMessage = '';
    public string $location = '';
    public string $route = '';
    public string $content = '';

    public function isOk(): bool
    {
        return 200 === $this->statusCode;
    }

    public function setStatusCode(int $statusCode, string $message): void
    {
        $this->statusCode = $statusCode;
        if (($statusCode >= 300) && ($statusCode < 400)) {
            $this->location = $message;
        } elseif (404 === $statusCode) {
            // $this->errorMessage = 'Page not found.';
            $this->errorMessage = 'Кажется что-то пошло не так! Страница, которую Вы запрашиваете, не существует.';
        } elseif (($statusCode >= 400) && ($statusCode < 500)) {
            $this->errorMessage = $message;
        } elseif (($statusCode >= 500) && ($statusCode < 600)) {
            $this->errorMessage = $message;
        };
    }

    public function send(): void
    {
        if ($this->statusCode < 300) {
            header("HTTP/1.1 200 Ok");
            // echo $this->content;
        } elseif (($this->statusCode >= 300) && ($this->statusCode < 400)) {
            header("Location: {$this->location}", true, $this->statusCode);
            return;
        } else {
            // header("HTTP/1.1 {$this->statusCode} {$this->errorMessage}");
            header("HTTP/1.1 {$this->statusCode}");
        }
        echo $this->content;
    }
}
