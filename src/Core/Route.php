<?php

namespace App\Core;

class Route
{
    private string $path;
    private array $controller;

    /**
     * matched params for a route
     */
    private array $matches = [];

    /**
     * match params with "with" method
     */
    private array $params = [];

    public function __construct(string $path, array $controller)
    {
        $this->path = trim($path, "/");
        $this->controller = $controller;
    }

    public function with(string $param, string $regex): Route
    {
        $this->params[$param] = str_replace("(", "(?:", $regex);
        return $this;
    }

    public function match(string $url): bool
    {
        $url = trim($url, "/");
        $path = preg_replace_callback("#:([\w]+)#", [$this,'paramMatch'], $this->path);
        $regex = "#^{$path}$#i";

        if (!preg_match($regex, $url, $matches)) {
            return false;
        }

        array_shift($matches);
        $this->matches = $matches;
        return true;
    }

    private function paramMatch($match): string
    {
        if (isset($this->params[$match[1]])) {
            return "(".$this->params[$match[1]].")";
        }
        return '([^/]+)';
    }

    public function generateUri(array $params): string
    {
        $path = $this->path;
        foreach ($params as $k => $v) {
            $path = str_replace(":$k", "$v", $path);
        }

        return $path;
    }

    public function getMatches(): array
    {
        return $this->matches;
    }

    public function getController(): array
    {
        return $this->controller;
    }
}
