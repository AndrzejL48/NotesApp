<?php

declare(strict_types=1);
namespace App;

class View
{
    public function render(string $page, array $params): void
    {
        $params = $this->paramsEscapeFilter($params);
        require_once('templates/layout.php');
    }

    private function paramsEscapeFilter($params)
    {
        $clearParams = [];

        foreach ($params as $key => $param) {
            if (is_array($param)) {
                $clearParams[$key] = $this->paramsEscapeFilter($param);
            } elseif ($param && !is_int($param)) {
                $clearParams[$key] = htmlentities($param);
            } else {
                $clearParams[$key] = $param;
            }
        }

        return $clearParams;
    }
}
