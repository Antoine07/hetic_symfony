<?php

namespace App\Services;

use cebe\markdown\Markdown;

class HelperParser{

    private $parser;
    private $randonData;

    public function __construct(Markdown $parser, RandomData $randonData)
    {
        $this->parser = $parser;
        $this->randonData = $randonData;
    }

    public function translateHtml(array $markdowns ):array{

        $translate = [];
        foreach($markdowns as $markdown){
            $translate[] = $this->parser->parse($markdown);
        }

        return $this->randonData->shuffle( $translate );
    }
}