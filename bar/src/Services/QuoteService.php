<?php

namespace App\Services;

use App\Repository\QuoteRepository;
use cebe\markdown\Markdown;

class QuoteService {

    private $parser;
    private $quoteRepo;

    public function __construct(QuoteRepository $quoteRepo, Markdown $parser)
    {
        $this->quoteRepo = $quoteRepo;
        $this->parser = $parser;
    }

    public function getQuotes():array{

        $quotes = $this->quoteRepo->findAll();

        $parseQuotes = [];

        foreach($quotes as $quote){
            $parseQuotes[] = [
                'title' => $quote->getTitle(),
                'content' => $this->parser->parse($quote->getContent()),
            ];
        }

        return $parseQuotes;
    }

}