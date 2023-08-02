<?php

declare(strict_types=1);

namespace App;

class Request
{
    public function __construct(
        private array $get = [],
        private array $post = [],
        private array $server = []
    )
    {
    }

    public function isPostSend(): bool
    {
        return !empty($this->post);
    }

    public function isGetSend(): bool
    {
        return !empty($this->get);
    }

    public function getParam(string $name, $default = null)
    {
        return !empty($this->get[$name]) ? htmlentities($this->get[$name]) : $default;
    }

    public function postParam(string $name, $default = null)
    {
        return !empty($this->post[$name]) ? htmlentities($this->post[$name]) : $default;
    }

    public function isPostRequest(): bool
    {
        return $this->server['REQUEST_METHOD'] === 'POST';
    }

    public function isGetRequest(): bool
    {
        return $this->server['REQUEST_METHOD'] === 'GET';
    }
}

