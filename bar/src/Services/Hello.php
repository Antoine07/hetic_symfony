<?php

namespace App\Services;

class Hello {

    private $message;
    private $content;

    public function __construct($message, $content){
        $this->message = $message;
        $this->content = $content;
    }

    public function say(){

        return $this->message['message'] . $this->content['content'] ; 
    }
}