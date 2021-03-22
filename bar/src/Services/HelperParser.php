<?php

namespace App\Services;

use cebe\markdown\Markdown;

class HelperParser{

    private $parser;

    public function __construct(Markdown $parser)
    {
        $this->parser = $parser;
    }

    public function translateHtml(array $markdowns):array{

        $translate = [];
        foreach($markdowns as $markdown){
            $translate[] = $this->parser->parse($markdown);
        }

        return $translate;
    }
}