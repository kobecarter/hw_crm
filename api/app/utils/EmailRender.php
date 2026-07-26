<?php

namespace App\Utils;

class EmailRender
{
    public function __construct($template, array $data)
    {
        $this->template = $template;
        $this->data = $data;
    }
    private static function fileGetContentsUFT8($file)
    {
        $content = file_get_contents($file);
        return mb_convert_encoding($content, "HTML-ENTITIES", "UTF-8");
    }
    public function render()
    {
        $template = self::fileGetContentsUFT8($this->template);
        foreach ($this->data as $key => $value) {
            $template = str_replace('{{ ' . $key . ' }}', $value, $template);
        }
        return $template;
    }
}
