<?php

namespace App\Services\View;

use App\Exceptions\ViewNotFoundException;

class View
{
    private $data = array();

    private $render = false;

    public function __construct($template)
    {
        try {
            $dir = dirname(__FILE__, 3);
            $file = $dir . '/templates/' . strtolower($template);

            if (file_exists($file)) {
                $this->render = $file;
            } else {
                throw new ViewNotFoundException('Template ' . $template . ' not found!');
            }
        } catch (ViewNotFoundException $e) {
        }
    }

    public function assign(array $data)
    {
        $this->data = $data;
    }

    public function __destruct()
    {
        extract($this->data);
        include_once $this->render;

    }
}