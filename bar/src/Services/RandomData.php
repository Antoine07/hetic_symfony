<?php

namespace App\Services;

class RandomData
{

    public function shuffle(array $data): array
    {
        shuffle($data);

        return $data;
    }
}
